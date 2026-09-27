<?php
/**
 * Visible Yoast breadcrumbs, plus one-time news SEO defaults.
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Print the breadcrumb trail. Yoast builds it; we only show it.
 */
function maglist_child_the_breadcrumbs() {
	if ( is_front_page() || ! function_exists( 'yoast_breadcrumb' ) ) {
		return;
	}

	yoast_breadcrumb(
		'<nav class="na-breadcrumbs" aria-label="' . esc_attr__( 'ब्रेडक्रम्ब', 'maglist-child' ) . '">',
		'</nav>'
	);
}

/**
 * Home crumb in Nepali, even if Yoast still says "Home".
 *
 * @param array $links Breadcrumb links.
 * @return array
 */
function maglist_child_breadcrumb_home_label( $links ) {
	if ( isset( $links[0]['text'] ) ) {
		$links[0]['text'] = 'गृहपृष्ठ';
	}

	foreach ( $links as $index => $link ) {
		if ( isset( $link['text'] ) ) {
			$links[ $index ]['text'] = preg_replace( '/^Archives for\s+/u', '', (string) $link['text'] );
		}
	}

	$last = count( $links ) - 1;
	if ( $last >= 0 && isset( $links[ $last ]['text'] ) ) {
		if ( is_search() ) {
			$query = get_search_query();
			$links[ $last ]['text'] = '' !== $query ? $query : 'खोज';
		} elseif ( is_404() ) {
			$links[ $last ]['text'] = 'पृष्ठ भेटिएन';
		}
	}

	return $links;
}
add_filter( 'wpseo_breadcrumb_links', 'maglist_child_breadcrumb_home_label' );

/**
 * Nepali browser title for search results and missing pages.
 *
 * @param string $title Document title.
 * @return string
 */
function maglist_child_search_404_title( $title ) {
	if ( is_search() ) {
		$query = get_search_query();
		$label = '' !== $query ? $query : 'खोज';
		return $label . ' - ' . get_bloginfo( 'name' );
	}

	if ( is_404() ) {
		return 'पृष्ठ भेटिएन - ' . get_bloginfo( 'name' );
	}

	return $title;
}
add_filter( 'wpseo_title', 'maglist_child_search_404_title', 20 );

/**
 * Apply news-portal Yoast defaults once.
 *
 * Sets NewsArticle, a Nepali home crumb, the organization logo, and replaces
 * the homepage description only while it still mentions the old "अर्थतन्त्र" line.
 */
function maglist_child_ensure_yoast_news_settings() {
	if ( get_option( 'maglist_child_yoast_news_v2' ) ) {
		return;
	}

	$titles = get_option( 'wpseo_titles' );
	if ( ! is_array( $titles ) ) {
		return;
	}

	$titles['breadcrumbs-enable']       = true;
	$titles['breadcrumbs-home']         = 'गृहपृष्ठ';
	$titles['schema-article-type-post'] = 'NewsArticle';
	$titles['post_types-post-maintax']  = 'category';

	if ( empty( $titles['company_name'] ) || 'Nispaksha Awaj' === $titles['company_name'] ) {
		$titles['company_name'] = 'निश्पक्ष आवाज';
	}

	$logo_id = (int) get_theme_mod( 'custom_logo' );
	if ( $logo_id && empty( $titles['company_logo_id'] ) ) {
		$titles['company_logo_id'] = $logo_id;
		$logo_url                  = wp_get_attachment_url( $logo_id );
		if ( $logo_url && empty( $titles['company_logo'] ) ) {
			$titles['company_logo'] = $logo_url;
		}
	}

	$home_desc = isset( $titles['metadesc-home-wpseo'] ) ? (string) $titles['metadesc-home-wpseo'] : '';
	if ( false !== strpos( $home_desc, 'अर्थतन्त्र' ) ) {
		$titles['metadesc-home-wpseo'] = 'निश्पक्ष आवाज सुदूरपश्चिमको समाचार पोर्टल हो। कञ्चनपुर लगायत जिल्ला, राष्ट्रिय समाचार, राजनीति, समाज, खेलकुद र मनोरञ्जनका ताजा अपडेट।';
	}

	update_option( 'wpseo_titles', $titles );
	update_option( 'maglist_child_yoast_news_v2', '1', false );
}
add_action( 'init', 'maglist_child_ensure_yoast_news_settings', 20 );
