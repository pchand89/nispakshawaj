<?php
/**
 * Article view counts for the लोकप्रिय tab.
 *
 * The homepage is cached, so the count is recorded from the article page
 * via a small REST call, once per reader per story.
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Post meta key for the view total.
 *
 * @return string
 */
function maglist_child_views_meta_key() {
	return 'na_views';
}

/**
 * Whether this request looks like a bot or a link preview.
 *
 * @return bool
 */
function maglist_child_views_is_bot() {
	$ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? (string) wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) : '';
	if ( $ua === '' ) {
		return true;
	}
	return (bool) preg_match( '/bot|crawl|spider|slurp|facebookexternalhit|preview|whatsapp|telegram/i', $ua );
}

/**
 * Add one view, ignoring a repeat from the same reader for six hours.
 *
 * @param int $post_id Post ID.
 * @return int New total, or the current total when the hit is ignored.
 */
function maglist_child_record_post_view( $post_id ) {
	$post_id = absint( $post_id );
	$post    = get_post( $post_id );
	if ( ! $post instanceof WP_Post || 'post' !== $post->post_type || 'publish' !== $post->post_status ) {
		return 0;
	}

	if ( maglist_child_views_is_bot() ) {
		return (int) get_post_meta( $post_id, maglist_child_views_meta_key(), true );
	}

	$visitor = function_exists( 'maglist_child_share_visitor_key' )
		? maglist_child_share_visitor_key()
		: substr( hash( 'sha256', (string) ( $_SERVER['REMOTE_ADDR'] ?? '' ) ), 0, 32 );
	$dedupe  = 'na_view_' . $post_id . '_' . $visitor;
	$current = (int) get_post_meta( $post_id, maglist_child_views_meta_key(), true );

	if ( get_transient( $dedupe ) ) {
		return $current;
	}

	$current++;
	update_post_meta( $post_id, maglist_child_views_meta_key(), $current );
	set_transient( $dedupe, 1, 6 * HOUR_IN_SECONDS );

	return $current;
}

/**
 * REST: record one article view.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response
 */
function maglist_child_rest_record_view( WP_REST_Request $request ) {
	$post_id = absint( $request->get_param( 'id' ) );
	$total   = maglist_child_record_post_view( $post_id );

	return rest_ensure_response(
		array(
			'id'    => $post_id,
			'views' => $total,
		)
	);
}

/**
 * Register the view-count route.
 */
function maglist_child_register_view_route() {
	register_rest_route(
		'maglist-child/v1',
		'/views/(?P<id>\d+)',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'maglist_child_rest_record_view',
			'permission_callback' => '__return_true',
			'args'                => array(
				'id' => array(
					'type'              => 'integer',
					'required'          => true,
					'sanitize_callback' => 'absint',
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'maglist_child_register_view_route' );
