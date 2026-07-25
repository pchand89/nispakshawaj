<?php
/**
 * Single-post reaction counts (post meta + REST API).
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allowed reaction keys.
 *
 * @return string[]
 */
function maglist_child_reaction_keys() {
	return array( 'happy', 'sad', 'angry', 'surprise', 'love' );
}

/**
 * Post meta key for reaction totals.
 *
 * @return string
 */
function maglist_child_reactions_meta_key() {
	return '_na_reaction_counts';
}

/**
 * Normalized reaction counts for a post.
 *
 * @param int $post_id Post ID.
 * @return array<string,int>
 */
function maglist_child_get_reaction_counts( $post_id ) {
	$post_id = absint( $post_id );
	$keys    = maglist_child_reaction_keys();
	$counts  = array_fill_keys( $keys, 0 );

	if ( ! $post_id ) {
		return $counts;
	}

	$stored = get_post_meta( $post_id, maglist_child_reactions_meta_key(), true );
	if ( ! is_array( $stored ) ) {
		return $counts;
	}

	foreach ( $keys as $key ) {
		if ( isset( $stored[ $key ] ) ) {
			$counts[ $key ] = max( 0, (int) $stored[ $key ] );
		}
	}

	return $counts;
}

/**
 * Persist reaction counts.
 *
 * @param int               $post_id Post ID.
 * @param array<string,int> $counts  Counts map.
 */
function maglist_child_update_reaction_counts( $post_id, $counts ) {
	$post_id = absint( $post_id );
	if ( ! $post_id ) {
		return;
	}

	$clean = maglist_child_get_reaction_counts( 0 ); // zeroed keys
	foreach ( maglist_child_reaction_keys() as $key ) {
		$clean[ $key ] = isset( $counts[ $key ] ) ? max( 0, (int) $counts[ $key ] ) : 0;
	}

	update_post_meta( $post_id, maglist_child_reactions_meta_key(), $clean );
}

/**
 * Fingerprint for rate-limiting / per-visitor vote state.
 *
 * @return string
 */
function maglist_child_reaction_visitor_key() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? (string) wp_unslash( $_SERVER['REMOTE_ADDR'] ) : '';
	$ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? (string) wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) : '';
	return substr( hash( 'sha256', $ip . '|' . $ua . '|' . wp_salt( 'nonce' ) ), 0, 32 );
}

/**
 * Transient key for this visitor's selected reaction on a post.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function maglist_child_reaction_vote_transient_key( $post_id ) {
	return 'na_react_vote_' . absint( $post_id ) . '_' . maglist_child_reaction_visitor_key();
}

/**
 * Transient key for rate limiting.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function maglist_child_reaction_rate_transient_key( $post_id ) {
	return 'na_react_rate_' . absint( $post_id ) . '_' . maglist_child_reaction_visitor_key();
}

/**
 * Apply a reaction change for the current visitor.
 *
 * @param int    $post_id  Post ID.
 * @param string $reaction New reaction key, or '' to clear.
 * @return array|\WP_Error { counts, selected }
 */
function maglist_child_apply_reaction( $post_id, $reaction ) {
	$post_id = absint( $post_id );
	if ( ! $post_id || 'publish' !== get_post_status( $post_id ) ) {
		return new WP_Error( 'na_react_invalid_post', __( 'अमान्य पोस्ट।', 'maglist-child' ), array( 'status' => 404 ) );
	}

	$reaction = sanitize_key( (string) $reaction );
	$allowed  = maglist_child_reaction_keys();
	if ( '' !== $reaction && ! in_array( $reaction, $allowed, true ) ) {
		return new WP_Error( 'na_react_invalid_key', __( 'अमान्य प्रतिक्रिया।', 'maglist-child' ), array( 'status' => 400 ) );
	}

	$rate_key = maglist_child_reaction_rate_transient_key( $post_id );
	$hits     = (int) get_transient( $rate_key );
	if ( $hits >= 20 ) {
		return new WP_Error( 'na_react_rate_limited', __( 'धेरै प्रयास भयो। केही बेरपछि फेरि प्रयास गर्नुहोस्।', 'maglist-child' ), array( 'status' => 429 ) );
	}

	$vote_key  = maglist_child_reaction_vote_transient_key( $post_id );
	$previous  = (string) get_transient( $vote_key );
	if ( $previous && ! in_array( $previous, $allowed, true ) ) {
		$previous = '';
	}

	// Toggle off when clicking the same reaction again.
	if ( '' !== $reaction && $reaction === $previous ) {
		$reaction = '';
	}

	$counts = maglist_child_get_reaction_counts( $post_id );

	if ( $previous && isset( $counts[ $previous ] ) ) {
		$counts[ $previous ] = max( 0, (int) $counts[ $previous ] - 1 );
	}

	if ( '' !== $reaction ) {
		$counts[ $reaction ] = (int) $counts[ $reaction ] + 1;
		set_transient( $vote_key, $reaction, MONTH_IN_SECONDS );
	} else {
		delete_transient( $vote_key );
	}

	maglist_child_update_reaction_counts( $post_id, $counts );

	set_transient( $rate_key, $hits + 1, HOUR_IN_SECONDS );

	return array(
		'counts'   => $counts,
		'selected' => $reaction,
	);
}

/**
 * Register REST routes.
 */
function maglist_child_register_reaction_routes() {
	register_rest_route(
		'maglist-child/v1',
		'/reactions/(?P<id>\d+)',
		array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => 'maglist_child_rest_get_reactions',
				'permission_callback' => '__return_true',
				'args'                => array(
					'id' => array(
						'type'              => 'integer',
						'required'          => true,
						'sanitize_callback' => 'absint',
					),
				),
			),
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => 'maglist_child_rest_post_reaction',
				'permission_callback' => '__return_true',
				'args'                => array(
					'id'       => array(
						'type'              => 'integer',
						'required'          => true,
						'sanitize_callback' => 'absint',
					),
					'reaction' => array(
						'type'              => 'string',
						'required'          => false,
						'default'           => '',
						'sanitize_callback' => 'sanitize_key',
					),
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'maglist_child_register_reaction_routes' );

/**
 * GET reaction counts (+ this visitor's selection).
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response
 */
function maglist_child_rest_get_reactions( WP_REST_Request $request ) {
	$post_id = (int) $request['id'];
	$allowed = maglist_child_reaction_keys();
	$prev    = (string) get_transient( maglist_child_reaction_vote_transient_key( $post_id ) );
	if ( $prev && ! in_array( $prev, $allowed, true ) ) {
		$prev = '';
	}

	return rest_ensure_response(
		array(
			'counts'   => maglist_child_get_reaction_counts( $post_id ),
			'selected' => $prev,
		)
	);
}

/**
 * POST a reaction change.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function maglist_child_rest_post_reaction( WP_REST_Request $request ) {
	$nonce = $request->get_header( 'X-WP-Nonce' );
	if ( ! $nonce ) {
		$nonce = $request->get_param( '_wpnonce' );
	}
	if ( ! $nonce || ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
		return new WP_Error( 'na_react_bad_nonce', __( 'सुरक्षा जाँच असफल।', 'maglist-child' ), array( 'status' => 403 ) );
	}

	$result = maglist_child_apply_reaction( (int) $request['id'], (string) $request['reaction'] );
	if ( is_wp_error( $result ) ) {
		return $result;
	}

	return rest_ensure_response( $result );
}
