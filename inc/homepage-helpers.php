<?php
/**
 * Homepage helper functions.
 *
 * Small, reusable helpers used by front-page.php, template-home-ratopati.php
 * and the template parts in /template-parts/home/. Kept plain-function style
 * (not a class) to stay simple and fully independent from parent theme code.
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve a category term from a slug, with fallbacks for slugs that contain
 * non-ASCII characters (e.g. raw Devanagari/Nepali slugs like "राजनिती").
 * Depending on how the term was created/imported, WordPress may have stored
 * it raw, URL-encoded, or only matchable by its display name - so this tries
 * each in turn instead of assuming one particular storage format.
 *
 * @param string $cat_slug Category slug (raw, unencoded).
 * @return WP_Term|false
 */
function maglist_child_resolve_category( $cat_slug ) {
	if ( empty( $cat_slug ) ) {
		return false;
	}

	$category = get_category_by_slug( $cat_slug );

	if ( ! $category ) {
		$category = get_category_by_slug( urlencode( $cat_slug ) );
	}

	if ( ! $category ) {
		$category = get_category_by_slug( sanitize_title( $cat_slug ) );
	}

	if ( ! $category ) {
		$category = get_term_by( 'name', $cat_slug, 'category' );
	}

	return $category ? $category : false;
}

/**
 * Run a WP_Query for a homepage category block.
 *
 * Queries by resolved term ID (`cat`) rather than `category_name` (slug),
 * because `category_name` matching is sensitive to exactly how the slug is
 * stored/encoded - once `maglist_child_resolve_category()` has found the
 * right term, its ID is the reliable way to fetch its posts.
 *
 * @param string $cat_slug    Category slug. Empty string = latest posts sitewide.
 * @param int    $count       Number of posts to pull.
 * @param array  $exclude_ids Post IDs to exclude (e.g. ones already used in the hero).
 * @return WP_Query
 */
function maglist_child_get_category_query( $cat_slug = '', $count = 6, $exclude_ids = array() ) {
	$args = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => $count,
		'ignore_sticky_posts' => 1,
		'no_found_rows'       => true,
	);

	if ( $cat_slug ) {
		$category = maglist_child_resolve_category( $cat_slug );

		if ( $category ) {
			$args['cat'] = $category->term_id;
		} else {
			// Nothing resolved - fall back to a literal slug match rather than
			// silently returning sitewide "latest" posts under this category's heading.
			$args['category_name'] = $cat_slug;
		}
	}

	if ( ! empty( $exclude_ids ) ) {
		$args['post__not_in'] = $exclude_ids;
	}

	/**
	 * Filter the WP_Query args for a homepage category block before it runs.
	 * Handy if you want to add a tag filter, change ordering, etc. without
	 * touching template files.
	 */
	$args = apply_filters( 'maglist_child_category_query_args', $args, $cat_slug );

	return new WP_Query( $args );
}

/**
 * Check whether a category slug actually exists on this site, so a homepage
 * block can be skipped gracefully instead of rendering an empty section.
 *
 * @param string $cat_slug Category slug.
 * @return bool
 */
function maglist_child_category_exists( $cat_slug ) {
	if ( empty( $cat_slug ) ) {
		return true; // "Latest posts" block, no category required.
	}
	return (bool) maglist_child_resolve_category( $cat_slug );
}

/**
 * Resolve the URL to use for the "no featured image" placeholder.
 * Prefers a local child-theme asset (so the fallback keeps working even if
 * the production media library changes) and falls back to the known-good
 * production logo URL if that asset hasn't been added to the theme yet.
 *
 * @return string
 */
function maglist_child_default_thumb_url() {
	$local_path = MAGLIST_CHILD_DIR . '/assets/img/default-image.jpg';

	if ( file_exists( $local_path ) ) {
		return MAGLIST_CHILD_URI . '/assets/img/default-image.jpg';
	}

	return apply_filters(
		'maglist_child_default_thumb_url',
		'https://www.nispakshawaj.com/wp-content/uploads/2024/06/LogoNewTextBorder-1.png'
	);
}

/**
 * Output a uniformly cropped post thumbnail with a safe placeholder fallback.
 *
 * @param int    $post_id Post ID.
 * @param string $size    Registered image size.
 * @return string HTML <img> markup.
 */
function maglist_child_get_thumbnail( $post_id, $size = 'maglist-child-card' ) {
	if ( has_post_thumbnail( $post_id ) ) {
		return get_the_post_thumbnail(
			$post_id,
			$size,
			array(
				'class'   => 'ratopati-img',
				'loading' => 'lazy',
				'alt'     => get_the_title( $post_id ),
			)
		);
	}

	return sprintf(
		'<img src="%1$s" alt="%2$s" class="ratopati-img ratopati-img--placeholder" loading="lazy">',
		esc_url( maglist_child_default_thumb_url() ),
		esc_attr( get_the_title( $post_id ) )
	);
}

/**
 * Query for the "लोकप्रिय" (trending/popular) tab of the floating fixed-tab
 * widget. There's no page-view tracking plugin guaranteed to be active on
 * this site, so comment count is used as a reasonable, dependency-free proxy
 * for "popular" - falls back to plain latest posts if nothing has comments
 * yet (e.g. on a fresh install), so the tab is never empty.
 *
 * @param int $count Number of posts to pull.
 * @return WP_Query
 */
function maglist_child_get_popular_query( $count = 8 ) {
	$args = apply_filters(
		'maglist_child_popular_query_args',
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => $count,
			'ignore_sticky_posts' => 1,
			'no_found_rows'       => true,
			'orderby'             => 'comment_count',
			'order'               => 'DESC',
		)
	);

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		$args['orderby'] = 'date';
		unset( $args['order'] );
		$query = new WP_Query( $args );
	}

	return $query;
}

/**
 * Human readable "X mins/hours/days ago" string for a post.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function maglist_child_time_ago( $post_id ) {
	$published = get_the_time( 'U', $post_id );
	$now       = current_time( 'timestamp' ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested

	return sprintf(
		/* translators: %s: human readable time difference, e.g. "5 mins" */
		esc_html__( '%s ago', 'maglist-child' ),
		human_time_diff( $published, $now )
	);
}

/**
 * Small category "badge" for the hero + card thumbnails.
 *
 * @param int $post_id Post ID.
 * @return string HTML markup, or empty string if the post has no category.
 */
function maglist_child_category_badge( $post_id ) {
	$categories = get_the_category( $post_id );

	if ( empty( $categories ) ) {
		return '';
	}

	return sprintf(
		'<span class="ratopati-badge">%s</span>',
		esc_html( $categories[0]->name )
	);
}

/**
 * Print a homepage widget area if - and only if - it actually has widgets in it,
 * so empty ad slots never leave behind stray markup/whitespace on the page.
 *
 * @param string $sidebar_id    Registered sidebar/widget-area ID.
 * @param string $wrapper_class Extra class(es) for the wrapping <div>.
 */
function maglist_child_widget_area( $sidebar_id, $wrapper_class = '' ) {
	if ( ! is_active_sidebar( $sidebar_id ) ) {
		return;
	}

	printf( '<div class="%s" id="%s">', esc_attr( $wrapper_class ), esc_attr( $sidebar_id ) );
	dynamic_sidebar( $sidebar_id );
	echo '</div>';
}
