<?php
/**
 * Fix 404s on Nepali (and other non-ASCII) permalinks, and stop WordPress
 * from falling back to numeric post IDs as slugs.
 *
 * Softaculous often stores slugs as literal percent-encoding ("%e0%a4%b8...").
 * Encoding a full Nepali title that way regularly exceeds WP's varchar(200)
 * `post_name` column, truncates mid-sequence (e.g. "...%e0%a"), and Apache
 * returns 400 Bad Request.
 *
 * We store Unicode Devanagari slugs (spaces → hyphens, max 180 chars) instead.
 * Incoming Softaculous-style percent paths are decoded on `request` so old
 * URLs still resolve.
 *
 * SO Pinyin Slugs strips Devanagari to "" — disabled here (Chinese-only plugin).
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** @var int Safe max length for post_name (column is varchar(200)). */
define( 'MAGLIST_CHILD_SLUG_MAX_LEN', 180 );

/**
 * Disable SO Pinyin Slugs' sanitize_title filter (breaks Devanagari/percent slugs).
 */
function maglist_child_disable_pinyin_slug_filter() {
	if ( function_exists( 'sops_getPinyinSlug' ) ) {
		remove_filter( 'sanitize_title', 'sops_getPinyinSlug', 1 );
	}
}
add_action( 'plugins_loaded', 'maglist_child_disable_pinyin_slug_filter', 20 );
add_action( 'init', 'maglist_child_disable_pinyin_slug_filter', 0 );

/**
 * Build a Unicode-safe post slug from a title (no percent-encoding).
 *
 * @param string $title Raw title.
 * @return string
 */
function maglist_child_unicode_slug( $title ) {
	$title = trim( wp_strip_all_tags( (string) $title ) );
	if ( $title === '' ) {
		return '';
	}

	// Collapse whitespace to hyphens (avoids %20 in URLs).
	$title = preg_replace( '/[\s\x{00A0}]+/u', '-', $title );
	// Keep letters, combining marks (Devanagari matras/virama), numbers, hyphens.
	$title = preg_replace( '/[^\p{L}\p{M}\p{N}\-]+/u', '', $title );
	$title = preg_replace( '/-+/', '-', $title );
	$title = trim( (string) $title, '-' );

	if ( $title === '' ) {
		return '';
	}

	if ( function_exists( 'mb_substr' ) ) {
		$title = mb_substr( $title, 0, MAGLIST_CHILD_SLUG_MAX_LEN, 'UTF-8' );
	} else {
		$title = substr( $title, 0, MAGLIST_CHILD_SLUG_MAX_LEN );
	}

	return trim( $title, '-' );
}

/**
 * Decode Softaculous-style percent-encoded path segments to Unicode.
 *
 * @param string $slug Pagename, post name, or category_name (may include "/").
 * @return string
 */
function maglist_child_decode_softaculous_slug( $slug ) {
	if ( ! is_string( $slug ) || $slug === '' ) {
		return $slug;
	}

	$parts = explode( '/', $slug );
	foreach ( $parts as &$part ) {
		if ( $part === '' || preg_match( '/[^\x00-\x7F]/', $part ) ) {
			continue;
		}
		if ( ! preg_match( '/%[0-9a-f]{2}/i', $part ) ) {
			continue;
		}
		$decoded = rawurldecode( $part );
		if ( is_string( $decoded ) && $decoded !== '' && preg_match( '/[^\x00-\x7F]/', $decoded ) ) {
			$part = $decoded;
		}
	}
	unset( $part );

	return implode( '/', $parts );
}

/**
 * When saving Unicode titles, keep a real Devanagari slug instead of "".
 * For queries, core also strips non-ASCII — preserve the slug so name= matches.
 *
 * @param string $title     Sanitized title so far.
 * @param string $raw_title Original title before sanitization.
 * @param string $context   Sanitization context (save|query|…).
 * @return string
 */
function maglist_child_preserve_unicode_slug( $title, $raw_title = '', $context = 'save' ) {
	$source = ( is_string( $raw_title ) && $raw_title !== '' ) ? $raw_title : $title;
	if ( ! is_string( $source ) || $source === '' ) {
		return $title;
	}

	// Query: WP_Query runs sanitize_title_for_query which would wipe Devanagari.
	if ( 'query' === $context ) {
		if ( preg_match( '/[^\x00-\x7F]/', $source ) ) {
			return $source;
		}
		if ( preg_match( '/%[0-9a-f]{2}/i', $source ) ) {
			$decoded = maglist_child_decode_softaculous_slug( $source );
			if ( is_string( $decoded ) && $decoded !== '' && preg_match( '/[^\x00-\x7F]/', $decoded ) ) {
				return $decoded;
			}
		}
		return $title;
	}

	if ( ! preg_match( '/[^\x00-\x7F]/', $source ) ) {
		return $title;
	}

	$slug = maglist_child_unicode_slug( $source );
	return $slug !== '' ? $slug : $title;
}
add_filter( 'sanitize_title', 'maglist_child_preserve_unicode_slug', 20, 3 );

/**
 * Remap rewrite query vars: decode Softaculous percent paths to Unicode.
 *
 * @param array $query_vars Parsed request vars.
 * @return array
 */
function maglist_child_fix_encoded_slugs( $query_vars ) {
	$keys = array( 'name', 'pagename', 'category_name', 'tag', 'attachment' );

	foreach ( $keys as $key ) {
		if ( empty( $query_vars[ $key ] ) || ! is_string( $query_vars[ $key ] ) ) {
			continue;
		}
		$query_vars[ $key ] = maglist_child_decode_softaculous_slug( $query_vars[ $key ] );
	}

	return $query_vars;
}
add_filter( 'request', 'maglist_child_fix_encoded_slugs', 1 );

/**
 * Build a permalink-safe slug from a post title.
 *
 * @param string $title   Post title.
 * @param int    $post_id Optional post ID for uniqueness.
 * @return string
 */
function maglist_child_make_post_slug( $title, $post_id = 0 ) {
	$title = trim( wp_strip_all_tags( (string) $title ) );
	if ( $title === '' ) {
		return '';
	}

	if ( preg_match( '/[^\x00-\x7F]/', $title ) ) {
		$slug = maglist_child_unicode_slug( $title );
	} else {
		remove_filter( 'sanitize_title', 'maglist_child_preserve_unicode_slug', 20 );
		$slug = sanitize_title( $title );
		add_filter( 'sanitize_title', 'maglist_child_preserve_unicode_slug', 20, 3 );
	}

	$slug = trim( (string) $slug, '-' );
	if ( $slug === '' || ! $post_id ) {
		return $slug;
	}

	$post = get_post( $post_id );
	if ( ! $post instanceof WP_Post ) {
		return $slug;
	}

	return wp_unique_post_slug( $slug, $post_id, $post->post_status, $post->post_type, (int) $post->post_parent );
}

/**
 * Whether a stored post_name should be regenerated from the title.
 *
 * @param WP_Post $post Post object.
 * @return bool
 */
function maglist_child_slug_needs_repair( WP_Post $post ) {
	$name = (string) $post->post_name;

	if ( $name === '' ) {
		return true;
	}

	// WP ID fallback: /2026/07/17/3766/
	if ( preg_match( '/^[0-9]+$/', $name ) && (string) (int) $post->ID === $name ) {
		return true;
	}

	// Softaculous percent-encoding, or a varchar(200)-truncated broken sequence.
	if ( false !== strpos( $name, '%' ) ) {
		return true;
	}

	return false;
}

/**
 * Batched repair: numeric / percent / truncated slugs → Unicode title slugs.
 */
function maglist_child_maybe_repair_post_slugs() {
	$flag = 'maglist_child_unicode_slugs_repaired';
	if ( get_option( $flag, '' ) === '1' ) {
		return;
	}

	if ( wp_doing_ajax() || wp_doing_cron() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}

	global $wpdb;

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$ids = $wpdb->get_col(
		"SELECT ID FROM {$wpdb->posts}
		WHERE post_type = 'post'
		  AND post_status IN ('publish','draft','pending','future','private')
		  AND (
		    post_name = ''
		    OR ( post_name REGEXP '^[0-9]+$' AND post_name = CAST(ID AS CHAR) )
		    OR post_name LIKE '%\\%%'
		  )
		ORDER BY ID DESC
		LIMIT 40"
	);

	if ( empty( $ids ) ) {
		update_option( $flag, '1', false );
		delete_option( 'maglist_child_numeric_slugs_repaired' );
		flush_rewrite_rules( false );
		return;
	}

	foreach ( $ids as $post_id ) {
		$post = get_post( (int) $post_id );
		if ( ! $post instanceof WP_Post || ! maglist_child_slug_needs_repair( $post ) ) {
			continue;
		}

		$new_slug = maglist_child_make_post_slug( $post->post_title, (int) $post->ID );
		if ( $new_slug === '' || $new_slug === $post->post_name || preg_match( '/^[0-9]+$/', $new_slug ) ) {
			continue;
		}

		wp_update_post(
			array(
				'ID'        => (int) $post->ID,
				'post_name' => $new_slug,
			)
		);
	}
}
add_action( 'init', 'maglist_child_maybe_repair_post_slugs', 30 );