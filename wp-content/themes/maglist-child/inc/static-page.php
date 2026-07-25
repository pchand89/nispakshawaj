<?php
/**
 * Static page helpers (Ratopati-style content pages).
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Disable Maglist's inner banner on normal pages (category hubs keep their own layout).
 *
 * @param bool $disable Whether the banner is already disabled.
 * @return bool
 */
function maglist_child_disable_page_inner_banner( $disable ) {
	if ( is_page() && ! maglist_child_get_page_category_hub() ) {
		return true;
	}
	return (bool) $disable;
}
add_filter( 'maglist_disable_inner_banner_content', 'maglist_child_disable_page_inner_banner' );

/**
 * Whether the current page should use the simplified static content layout.
 *
 * @return bool
 */
function maglist_child_is_static_page_layout() {
	return is_page() && ! maglist_child_get_page_category_hub() && ! is_front_page();
}

/**
 * Map a page to a known useful-link type for fallback content.
 *
 * @param WP_Post|null $page Page object.
 * @return string One of about|contact|privacy|terms|ads|'' .
 */
function maglist_child_get_static_page_type( $page = null ) {
	if ( ! $page instanceof WP_Post ) {
		$page = get_queried_object();
	}
	if ( ! $page instanceof WP_Post ) {
		return '';
	}

	$haystack = mb_strtolower(
		$page->post_name . ' ' . $page->post_title,
		'UTF-8'
	);

	$map = array(
		'about'   => array( 'about', 'hamro-barema', 'हाम्रो', 'बारेमा' ),
		'contact' => array( 'contact', 'samparka', 'सम्पर्क' ),
		'privacy' => array( 'privacy', 'goponiyata', 'गोपनीयता' ),
		'terms'   => array( 'terms', 'condition', 'सर्त', 'नियम' ),
		'ads'     => array( 'advert', 'ads', 'bijyapan', 'विज्ञापन' ),
	);

	foreach ( $map as $type => $needles ) {
		foreach ( $needles as $needle ) {
			if ( false !== mb_strpos( $haystack, mb_strtolower( $needle, 'UTF-8' ), 0, 'UTF-8' ) ) {
				return $type;
			}
		}
	}

	return '';
}

/**
 * Render page body: editor content, or a short Nepali fallback for known utility pages.
 */
function maglist_child_the_static_page_content() {
	$raw = get_post();
	$raw = ( $raw instanceof WP_Post ) ? trim( (string) $raw->post_content ) : '';

	if ( '' !== $raw ) {
		the_content();
		return;
	}

	$type = maglist_child_get_static_page_type();
	if ( ! $type ) {
		return;
	}

	get_template_part( 'template-parts/page/fallback', $type );
}
