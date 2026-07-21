<?php
/**
 * Fix 404s on Nepali (and other non-ASCII) permalinks.
 *
 * Two host-export quirks stack here:
 * 1. Softaculous often stores slugs as literal percent-encoding
 *    ("%e0%a4%b8..." instead of "समाचार").
 * 2. The SO Pinyin Slugs plugin hooks sanitize_title at priority 1 and
 *    strips those slugs to "" — WordPress then queries post_name = '' and
 *    every news/menu URL 404s. That plugin is for Chinese pinyin, not Nepali.
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
 * Convert a path segment (or hierarchical path) to Softaculous-style
 * lowercase percent-encoding when it contains non-ASCII characters.
 *
 * @param string $slug Pagename, post name, or category_name (may include "/").
 * @return string
 */
function maglist_child_softaculous_slug( $slug ) {
	if ( ! is_string( $slug ) || $slug === '' ) {
		return $slug;
	}

	$parts = explode( '/', $slug );
	foreach ( $parts as &$part ) {
		if ( $part === '' ) {
			continue;
		}
		// Already ASCII (including percent-encoded Softaculous slugs) — leave alone.
		if ( ! preg_match( '/[^\x00-\x7F]/', $part ) ) {
			continue;
		}
		$part = strtolower( rawurlencode( $part ) );
	}
	unset( $part );

	return implode( '/', $parts );
}

/**
 * Remap rewrite query vars so Unicode paths match percent-encoded DB slugs.
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
		$query_vars[ $key ] = maglist_child_softaculous_slug( $query_vars[ $key ] );
	}

	return $query_vars;
}
add_filter( 'request', 'maglist_child_fix_encoded_slugs', 1 );
