<?php
/**
 * Sync Facebook engagement share_count into stored share totals (Graph API).
 *
 * Configure via Customizer (Maglist Child → Facebook Share Sync) or constants:
 *   MAGLIST_CHILD_FB_APP_ID
 *   MAGLIST_CHILD_FB_APP_SECRET
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Meta: last Facebook Graph share_count for a post.
 *
 * @return string
 */
function maglist_child_facebook_share_meta_key() {
	return '_na_share_facebook_api';
}

/**
 * Meta: unix timestamp of last successful FB sync.
 *
 * @return string
 */
function maglist_child_facebook_synced_meta_key() {
	return '_na_share_facebook_synced';
}

/**
 * Facebook app credentials (constants > theme_mod > filter).
 *
 * @return array{app_id:string,app_secret:string}
 */
function maglist_child_facebook_app_credentials() {
	$app_id = defined( 'MAGLIST_CHILD_FB_APP_ID' )
		? (string) MAGLIST_CHILD_FB_APP_ID
		: (string) get_theme_mod( 'maglist_child_fb_app_id', '' );

	$app_secret = defined( 'MAGLIST_CHILD_FB_APP_SECRET' )
		? (string) MAGLIST_CHILD_FB_APP_SECRET
		: (string) get_theme_mod( 'maglist_child_fb_app_secret', '' );

	/**
	 * Filter Facebook app credentials used for share sync.
	 *
	 * @param array $credentials { app_id, app_secret }.
	 */
	$credentials = apply_filters(
		'maglist_child_facebook_app_credentials',
		array(
			'app_id'     => trim( $app_id ),
			'app_secret' => trim( $app_secret ),
		)
	);

	return array(
		'app_id'     => isset( $credentials['app_id'] ) ? (string) $credentials['app_id'] : '',
		'app_secret' => isset( $credentials['app_secret'] ) ? (string) $credentials['app_secret'] : '',
	);
}

/**
 * Whether Facebook share sync is configured and enabled.
 *
 * @return bool
 */
function maglist_child_facebook_share_sync_enabled() {
	$creds = maglist_child_facebook_app_credentials();
	$on    = (bool) get_theme_mod( 'maglist_child_fb_share_sync', true );

	return $on && '' !== $creds['app_id'] && '' !== $creds['app_secret'];
}

/**
 * Minimum seconds between Facebook syncs for one post.
 *
 * @return int
 */
function maglist_child_facebook_share_sync_ttl() {
	/**
	 * Filter sync TTL in seconds (default 6 hours).
	 *
	 * @param int $ttl Seconds.
	 */
	return max( HOUR_IN_SECONDS, (int) apply_filters( 'maglist_child_facebook_share_sync_ttl', 6 * HOUR_IN_SECONDS ) );
}

/**
 * Fetch Facebook engagement.share_count for a URL via Graph API.
 *
 * @param string $url Absolute post URL.
 * @return int|\WP_Error
 */
function maglist_child_fetch_facebook_share_count( $url ) {
	$url = esc_url_raw( $url );
	if ( ! $url ) {
		return new WP_Error( 'na_fb_bad_url', 'Invalid URL.' );
	}

	if ( ! maglist_child_facebook_share_sync_enabled() ) {
		return new WP_Error( 'na_fb_not_configured', 'Facebook app credentials missing.' );
	}

	$creds = maglist_child_facebook_app_credentials();
	$token = $creds['app_id'] . '|' . $creds['app_secret'];

	$endpoint = add_query_arg(
		array(
			'id'           => $url,
			'fields'       => 'engagement',
			'access_token' => $token,
		),
		'https://graph.facebook.com/v21.0/'
	);

	$response = wp_remote_get(
		$endpoint,
		array(
			'timeout' => 8,
			'headers' => array(
				'Accept' => 'application/json',
			),
		)
	);

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$code = (int) wp_remote_retrieve_response_code( $response );
	$body = json_decode( (string) wp_remote_retrieve_body( $response ), true );

	if ( $code < 200 || $code >= 300 || ! is_array( $body ) ) {
		$message = is_array( $body ) && isset( $body['error']['message'] )
			? (string) $body['error']['message']
			: 'Facebook Graph request failed.';
		return new WP_Error( 'na_fb_http_error', $message, array( 'status' => $code ) );
	}

	$share_count = 0;
	if ( isset( $body['engagement']['share_count'] ) ) {
		$share_count = max( 0, (int) $body['engagement']['share_count'] );
	}

	return $share_count;
}

/**
 * Sync one post's Facebook share_count into meta (used by display totals).
 *
 * @param int  $post_id Post ID.
 * @param bool $force   Ignore TTL.
 * @return int|\WP_Error Synced FB share count.
 */
function maglist_child_sync_facebook_share_for_post( $post_id, $force = false ) {
	$post_id = absint( $post_id );
	if ( ! $post_id || 'publish' !== get_post_status( $post_id ) ) {
		return new WP_Error( 'na_fb_bad_post', 'Invalid post.' );
	}

	if ( ! maglist_child_facebook_share_sync_enabled() ) {
		return new WP_Error( 'na_fb_not_configured', 'Facebook app credentials missing.' );
	}

	$last = (int) get_post_meta( $post_id, maglist_child_facebook_synced_meta_key(), true );
	if ( ! $force && $last && ( time() - $last ) < maglist_child_facebook_share_sync_ttl() ) {
		return (int) get_post_meta( $post_id, maglist_child_facebook_share_meta_key(), true );
	}

	$lock = 'na_fb_sync_lock_' . $post_id;
	if ( get_transient( $lock ) ) {
		return (int) get_post_meta( $post_id, maglist_child_facebook_share_meta_key(), true );
	}
	set_transient( $lock, 1, 2 * MINUTE_IN_SECONDS );

	$url    = get_permalink( $post_id );
	$result = maglist_child_fetch_facebook_share_count( $url );

	// Retry without trailing slash if the first attempt hard-fails.
	if ( is_wp_error( $result ) && is_string( $url ) ) {
		$alt = untrailingslashit( $url );
		if ( $alt !== $url ) {
			$retry = maglist_child_fetch_facebook_share_count( $alt );
			if ( ! is_wp_error( $retry ) ) {
				$result = $retry;
			}
		}
	}

	delete_transient( $lock );

	if ( is_wp_error( $result ) ) {
		return $result;
	}

	update_post_meta( $post_id, maglist_child_facebook_share_meta_key(), (int) $result );
	update_post_meta( $post_id, maglist_child_facebook_synced_meta_key(), time() );

	return (int) $result;
}

/**
 * Cron / scheduled callback for a single post.
 *
 * @param int $post_id Post ID.
 */
function maglist_child_cron_sync_facebook_share_post( $post_id ) {
	maglist_child_sync_facebook_share_for_post( (int) $post_id, false );
}
add_action( 'maglist_child_sync_facebook_share_post', 'maglist_child_cron_sync_facebook_share_post' );

/**
 * Batch sync recent posts.
 */
function maglist_child_cron_sync_facebook_shares_batch() {
	if ( ! maglist_child_facebook_share_sync_enabled() ) {
		return;
	}

	$query = new WP_Query(
		array(
			'post_type'              => 'post',
			'post_status'            => 'publish',
			'posts_per_page'         => 25,
			'orderby'                => 'modified',
			'order'                  => 'DESC',
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'date_query'             => array(
				array(
					'column' => 'post_modified_gmt',
					'after'  => '30 days ago',
				),
			),
		)
	);

	foreach ( $query->posts as $post_id ) {
		maglist_child_sync_facebook_share_for_post( (int) $post_id, false );
		// Be gentle with Graph rate limits.
		usleep( 200000 );
	}
}
add_action( 'maglist_child_sync_facebook_shares_batch', 'maglist_child_cron_sync_facebook_shares_batch' );

/**
 * Schedule recurring batch sync.
 */
function maglist_child_schedule_facebook_share_sync() {
	if ( ! maglist_child_facebook_share_sync_enabled() ) {
		$timestamp = wp_next_scheduled( 'maglist_child_sync_facebook_shares_batch' );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, 'maglist_child_sync_facebook_shares_batch' );
		}
		return;
	}

	if ( ! wp_next_scheduled( 'maglist_child_sync_facebook_shares_batch' ) ) {
		wp_schedule_event( time() + HOUR_IN_SECONDS, 'twicedaily', 'maglist_child_sync_facebook_shares_batch' );
	}
}
add_action( 'init', 'maglist_child_schedule_facebook_share_sync' );

/**
 * On single posts, queue a background sync if stale.
 */
function maglist_child_maybe_queue_facebook_share_sync() {
	if ( is_admin() || ! is_singular( 'post' ) || ! maglist_child_facebook_share_sync_enabled() ) {
		return;
	}

	$post_id = (int) get_queried_object_id();
	if ( ! $post_id ) {
		return;
	}

	$last = (int) get_post_meta( $post_id, maglist_child_facebook_synced_meta_key(), true );
	if ( $last && ( time() - $last ) < maglist_child_facebook_share_sync_ttl() ) {
		return;
	}

	$args = array( $post_id );
	if ( ! wp_next_scheduled( 'maglist_child_sync_facebook_share_post', $args ) ) {
		wp_schedule_single_event( time() + 15, 'maglist_child_sync_facebook_share_post', $args );
	}
}
add_action( 'template_redirect', 'maglist_child_maybe_queue_facebook_share_sync', 20 );

/**
 * Customizer: Facebook app credentials for share sync.
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function maglist_child_share_sync_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'maglist_child_fb_share',
		array(
			'title'       => __( 'Facebook Share Sync', 'maglist-child' ),
			'description' => __( 'Pull Facebook share_count from Graph API into post share totals (like major news portals). Create a free app at developers.facebook.com and paste App ID + App Secret. Click tracking still counts other networks.', 'maglist-child' ),
			'priority'    => 165,
		)
	);

	$wp_customize->add_setting(
		'maglist_child_fb_share_sync',
		array(
			'default'           => true,
			'sanitize_callback' => static function ( $value ) {
				return (bool) $value;
			},
		)
	);
	$wp_customize->add_control(
		'maglist_child_fb_share_sync',
		array(
			'label'   => __( 'Enable Facebook share sync', 'maglist-child' ),
			'section' => 'maglist_child_fb_share',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'maglist_child_fb_app_id',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'maglist_child_fb_app_id',
		array(
			'label'   => __( 'Facebook App ID', 'maglist-child' ),
			'section' => 'maglist_child_fb_share',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'maglist_child_fb_app_secret',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'maglist_child_fb_app_secret',
		array(
			'label'       => __( 'Facebook App Secret', 'maglist-child' ),
			'description' => __( 'Keep this private. Prefer defining MAGLIST_CHILD_FB_APP_ID / MAGLIST_CHILD_FB_APP_SECRET in wp-config.php on production.', 'maglist-child' ),
			'section'     => 'maglist_child_fb_share',
			'type'        => 'password',
		)
	);
}
add_action( 'customize_register', 'maglist_child_share_sync_customize_register' );
