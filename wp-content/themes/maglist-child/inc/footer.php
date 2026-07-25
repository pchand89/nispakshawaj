<?php
/**
 * Footer helpers — category list, useful links, social defaults.
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Categories for the footer's "समाचार विधा" column.
 * Prefers terms that match the homepage section slugs; falls back to top-level cats.
 *
 * @return array<int, array{label:string,url:string}>
 */
function maglist_child_get_footer_categories() {
	$slugs = array(
		'समाचार',
		'राजनिती',
		'समाज',
		'मनोरञ्जन',
		'खेलकुद',
		'शिक्षा / साहित्य',
		'व्यवसाय',
		'स्थानीय तह/ विकास',
		'भिडियो',
	);

	$items = array();
	foreach ( $slugs as $slug ) {
		$term = get_term_by( 'slug', $slug, 'category' );
		if ( ! $term || is_wp_error( $term ) ) {
			$term = get_term_by( 'name', $slug, 'category' );
		}
		if ( ! $term || is_wp_error( $term ) ) {
			continue;
		}
		$link = get_term_link( $term );
		if ( is_wp_error( $link ) ) {
			continue;
		}
		$items[] = array(
			'label' => $term->name,
			'url'   => $link,
		);
	}

	if ( empty( $items ) ) {
		$terms = get_terms(
			array(
				'taxonomy'   => 'category',
				'hide_empty' => true,
				'parent'     => 0,
				'number'     => 10,
			)
		);
		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				if ( (int) $term->term_id === (int) get_option( 'default_category' ) ) {
					continue;
				}
				$link = get_term_link( $term );
				if ( is_wp_error( $link ) ) {
					continue;
				}
				$items[] = array(
					'label' => $term->name,
					'url'   => $link,
				);
			}
		}
	}

	/**
	 * Filter footer category links.
	 *
	 * @param array $items List of label/url pairs.
	 */
	return apply_filters( 'maglist_child_footer_categories', $items );
}

/**
 * Resolve a page URL by trying several path slugs (Nepali/English).
 *
 * @param string[] $paths Candidate page paths.
 * @return string Empty string when none found.
 */
function maglist_child_footer_page_url( array $paths ) {
	foreach ( $paths as $path ) {
		$page = get_page_by_path( $path );
		if ( $page instanceof WP_Post ) {
			return get_permalink( $page );
		}
	}
	return '';
}

/**
 * Useful links for the footer's "उपयोगी लिंक" column when no menu is assigned.
 *
 * @return array<int, array{label:string,url:string}>
 */
function maglist_child_get_footer_useful_links() {
	$candidates = array(
		array(
			'label'    => __( 'हाम्रो बारेमा', 'maglist-child' ),
			'paths'    => array( 'hamro-barema', 'about', 'about-us', 'हाम्रो-बारेमा' ),
			'fallback' => '/about/',
		),
		array(
			'label'    => __( 'सम्पर्क', 'maglist-child' ),
			'paths'    => array( 'samparka', 'contact', 'contact-us', 'सम्पर्क' ),
			'fallback' => '/contact/',
		),
		array(
			'label'    => __( 'विज्ञापनको लागि', 'maglist-child' ),
			'paths'    => array( 'advertisement', 'ads', 'bijyapan', 'विज्ञापन' ),
			'fallback' => '/advertisement/',
		),
		array(
			'label'    => __( 'गोपनीयता नीति', 'maglist-child' ),
			'paths'    => array( 'privacy-policy', 'goponiyata-niti', 'गोपनीयता-नीति' ),
			'fallback' => '/privacy-policy/',
		),
		array(
			'label'    => __( 'सर्त तथा नियम', 'maglist-child' ),
			'paths'    => array( 'terms', 'terms-and-conditions', 'terms-conditions' ),
			'fallback' => '/terms/',
		),
	);

	$items = array();
	foreach ( $candidates as $candidate ) {
		$url = maglist_child_footer_page_url( $candidate['paths'] );
		if ( ! $url ) {
			$url = home_url( $candidate['fallback'] );
		}
		$items[] = array(
			'label' => $candidate['label'],
			'url'   => $url,
		);
	}

	/**
	 * Filter footer useful links.
	 *
	 * @param array $items List of label/url pairs.
	 */
	return apply_filters( 'maglist_child_footer_useful_links', $items );
}

/**
 * Default social profiles when the Maglist "Footer social menu" is empty.
 * Filter `maglist_child_footer_social_links` to set real URLs.
 *
 * @return array<int, array{label:string,url:string,icon:string}>
 */
function maglist_child_get_footer_social_links() {
	$defaults = array(
		array(
			'label' => 'Facebook',
			'url'   => 'https://www.facebook.com/nispakshawaj',
			'icon'  => 'fa fa-facebook',
		),
		array(
			'label' => 'YouTube',
			'url'   => 'https://www.youtube.com/@nispakshawaj',
			'icon'  => 'fa fa-youtube-play',
		),
		array(
			'label' => 'X',
			'url'   => 'https://x.com/nispakshawaj',
			'icon'  => 'fa fa-twitter',
		),
	);

	/**
	 * Filter footer social links. Return an empty array to hide defaults.
	 *
	 * @param array $defaults Social items.
	 */
	$links = apply_filters( 'maglist_child_footer_social_links', $defaults );

	return array_values(
		array_filter(
			(array) $links,
			static function ( $item ) {
				return is_array( $item ) && ! empty( $item['url'] ) && ! empty( $item['icon'] );
			}
		)
	);
}
