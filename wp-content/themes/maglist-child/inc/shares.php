<?php
/**
 * Single-post share counts (post meta + REST API).
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allowed share network keys.
 *
 * @return string[]
 */
function maglist_child_share_keys() {
	return array( 'facebook', 'x', 'whatsapp', 'linkedin', 'email', 'copy' );
}

/**
 * Post meta key for share totals.
 *
 * @return string
 */
function maglist_child_shares_meta_key() {
	return '_na_share_counts';
}

/**
 * Normalized share counts for a post (per network + total).
 *
 * Facebook uses max(button clicks, Graph API share_count) when sync is available.
 *
 * @param int $post_id Post ID.
 * @return array{facebook:int,x:int,whatsapp:int,linkedin:int,email:int,copy:int,total:int}
 */
function maglist_child_get_share_counts( $post_id ) {
	$post_id = absint( $post_id );
	$keys    = maglist_child_share_keys();
	$counts  = array_fill_keys( $keys, 0 );
	$counts['total'] = 0;

	if ( ! $post_id ) {
		return $counts;
	}

	$stored = get_post_meta( $post_id, maglist_child_shares_meta_key(), true );
	if ( is_array( $stored ) ) {
		foreach ( $keys as $key ) {
			if ( isset( $stored[ $key ] ) ) {
				$counts[ $key ] = max( 0, (int) $stored[ $key ] );
			}
		}
	}

	// Prefer Facebook Graph engagement when synced (Online Khabar-style stored total).
	if ( function_exists( 'maglist_child_facebook_share_meta_key' ) ) {
		$fb_api = (int) get_post_meta( $post_id, maglist_child_facebook_share_meta_key(), true );
		if ( $fb_api > (int) $counts['facebook'] ) {
			$counts['facebook'] = $fb_api;
		}
	}

	$total = 0;
	foreach ( $keys as $key ) {
		$total += (int) $counts[ $key ];
	}
	$counts['total'] = $total;

	return $counts;
}

/**
 * Raw click-tracking counts (ignores Facebook Graph sync).
 *
 * @param int $post_id Post ID.
 * @return array<string,int>
 */
function maglist_child_get_share_click_counts( $post_id ) {
	$post_id = absint( $post_id );
	$keys    = maglist_child_share_keys();
	$counts  = array_fill_keys( $keys, 0 );

	if ( ! $post_id ) {
		return $counts;
	}

	$stored = get_post_meta( $post_id, maglist_child_shares_meta_key(), true );
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
 * Persist click-tracking share counts (not Facebook Graph meta).
 *
 * @param int               $post_id Post ID.
 * @param array<string,int> $counts  Counts map.
 */
function maglist_child_update_share_counts( $post_id, $counts ) {
	$post_id = absint( $post_id );
	if ( ! $post_id ) {
		return;
	}

	$clean = array();
	foreach ( maglist_child_share_keys() as $key ) {
		$clean[ $key ] = isset( $counts[ $key ] ) ? max( 0, (int) $counts[ $key ] ) : 0;
	}

	update_post_meta( $post_id, maglist_child_shares_meta_key(), $clean );
}

/**
 * Fingerprint for share rate-limiting.
 *
 * @return string
 */
function maglist_child_share_visitor_key() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? (string) wp_unslash( $_SERVER['REMOTE_ADDR'] ) : '';
	$ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? (string) wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) : '';
	return substr( hash( 'sha256', $ip . '|' . $ua . '|' . wp_salt( 'nonce' ) ), 0, 32 );
}

/**
 * Record a share click for a network.
 *
 * @param int    $post_id Post ID.
 * @param string $network Network key.
 * @return array|\WP_Error { counts }
 */
function maglist_child_record_share( $post_id, $network ) {
	$post_id = absint( $post_id );
	if ( ! $post_id || 'publish' !== get_post_status( $post_id ) ) {
		return new WP_Error( 'na_share_invalid_post', __( 'अमान्य पोस्ट।', 'maglist-child' ), array( 'status' => 404 ) );
	}

	$network = sanitize_key( (string) $network );
	$allowed = maglist_child_share_keys();
	if ( ! in_array( $network, $allowed, true ) ) {
		return new WP_Error( 'na_share_invalid_network', __( 'अमान्य सेयर माध्यम।', 'maglist-child' ), array( 'status' => 400 ) );
	}

	$visitor  = maglist_child_share_visitor_key();
	$rate_key = 'na_share_rate_' . $post_id . '_' . $visitor;
	$hits     = (int) get_transient( $rate_key );
	if ( $hits >= 40 ) {
		return new WP_Error( 'na_share_rate_limited', __( 'धेरै प्रयास भयो। केही बेरपछि फेरि प्रयास गर्नुहोस्।', 'maglist-child' ), array( 'status' => 429 ) );
	}

	// Avoid double-counting rapid repeat clicks on the same network.
	$dup_key = 'na_share_dup_' . $post_id . '_' . $network . '_' . $visitor;
	if ( get_transient( $dup_key ) ) {
		return array(
			'counts'  => maglist_child_get_share_counts( $post_id ),
			'duplicate' => true,
		);
	}

	$counts             = maglist_child_get_share_click_counts( $post_id );
	$counts[ $network ] = (int) $counts[ $network ] + 1;
	maglist_child_update_share_counts( $post_id, $counts );

	set_transient( $dup_key, 1, 2 * MINUTE_IN_SECONDS );
	set_transient( $rate_key, $hits + 1, HOUR_IN_SECONDS );

	return array(
		'counts'    => maglist_child_get_share_counts( $post_id ),
		'duplicate' => false,
	);
}

/**
 * Register REST routes for share counts.
 */
function maglist_child_register_share_routes() {
	register_rest_route(
		'maglist-child/v1',
		'/shares/(?P<id>\d+)',
		array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => 'maglist_child_rest_get_shares',
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
				'callback'            => 'maglist_child_rest_post_share',
				'permission_callback' => '__return_true',
				'args'                => array(
					'id'      => array(
						'type'              => 'integer',
						'required'          => true,
						'sanitize_callback' => 'absint',
					),
					'network' => array(
						'type'              => 'string',
						'required'          => true,
						'sanitize_callback' => 'sanitize_key',
					),
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'maglist_child_register_share_routes' );

/**
 * GET share counts.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response
 */
function maglist_child_rest_get_shares( WP_REST_Request $request ) {
	return rest_ensure_response(
		array(
			'counts' => maglist_child_get_share_counts( (int) $request['id'] ),
		)
	);
}

/**
 * POST a share event.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function maglist_child_rest_post_share( WP_REST_Request $request ) {
	$nonce = $request->get_header( 'X-WP-Nonce' );
	if ( ! $nonce ) {
		$nonce = $request->get_param( '_wpnonce' );
	}
	if ( ! $nonce || ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
		return new WP_Error( 'na_share_bad_nonce', __( 'सुरक्षा जाँच असफल।', 'maglist-child' ), array( 'status' => 403 ) );
	}

	$result = maglist_child_record_share( (int) $request['id'], (string) $request['network'] );
	if ( is_wp_error( $result ) ) {
		return $result;
	}

	return rest_ensure_response( $result );
}
