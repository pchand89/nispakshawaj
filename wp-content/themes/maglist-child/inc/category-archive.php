<?php
/**
 * Category archive helpers (shared by category.php and category-hub pages).
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the current request should load the category-archive assets/layout.
 *
 * @return bool
 */
function maglist_child_is_category_layout() {
	if ( is_category() ) {
		return true;
	}

	return (bool) maglist_child_get_page_category_hub();
}

/**
 * If the current page is a "category hub" (menu pages whose slug/title maps
 * to a category term), return that term; otherwise false.
 *
 * @return WP_Term|false
 */
function maglist_child_get_page_category_hub() {
	if ( ! is_page() ) {
		return false;
	}

	$page = get_queried_object();
	if ( ! $page instanceof WP_Post ) {
		return false;
	}

	$candidates = array_filter(
		array(
			$page->post_name,
			rawurldecode( $page->post_name ),
			$page->post_title,
		)
	);

	foreach ( $candidates as $candidate ) {
		$category = maglist_child_resolve_category( $candidate );
		if ( $category ) {
			return $category;
		}

		// Duplicate pages often end in -2 / -3.
		$base = preg_replace( '/-\d+$/', '', $candidate );
		if ( $base && $base !== $candidate ) {
			$category = maglist_child_resolve_category( $base );
			if ( $category ) {
				return $category;
			}
			$category = maglist_child_resolve_category( rawurldecode( $base ) );
			if ( $category ) {
				return $category;
			}
		}
	}

	return false;
}

/**
 * Current page number for archive / hub pagination.
 *
 * @return int
 */
function maglist_child_category_paged() {
	$paged = (int) get_query_var( 'paged' );
	if ( $paged < 1 ) {
		$paged = (int) get_query_var( 'page' );
	}
	return max( 1, $paged );
}

/**
 * Paginated query for a category hub page.
 *
 * @param int $term_id Category term ID.
 * @return WP_Query
 */
function maglist_child_get_category_archive_query( $term_id ) {
	return new WP_Query(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'cat'                 => (int) $term_id,
			'posts_per_page'      => 20,
			'paged'               => maglist_child_category_paged(),
			'ignore_sticky_posts' => 1,
		)
	);
}

/**
 * Collect posts from the main query as an array of WP_Post objects.
 *
 * @return WP_Post[]
 */
function maglist_child_collect_main_query_posts() {
	$posts = array();

	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			$posts[] = get_post();
		}
		rewind_posts();
	}

	return $posts;
}

/**
 * Render pagination for the main query or a custom hub query.
 *
 * @param WP_Query|null $query Optional custom query (hub pages).
 */
function maglist_child_category_pagination( $query = null ) {
	global $wp_query;

	$swap = null;
	if ( $query instanceof WP_Query ) {
		$swap     = $wp_query;
		$wp_query = $query; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	}

	the_posts_pagination(
		array(
			'mid_size'  => 2,
			'prev_text' => esc_html__( '← अघिल्लो', 'maglist-child' ),
			'next_text' => esc_html__( 'अर्को →', 'maglist-child' ),
			'class'     => 'na-cat-pagination',
		)
	);

	if ( $swap instanceof WP_Query ) {
		$wp_query = $swap; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	}
}
