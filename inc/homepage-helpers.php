<?php
/**
 * Homepage helper functions.
 *
 * Small, reusable helpers used by front-page.php, template-home.php
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
 * Output a post thumbnail, or an empty string when the post has no featured
 * image. We deliberately do NOT fall back to a placeholder/logo - posts
 * without a featured image should render as text-only.
 *
 * @param int    $post_id Post ID.
 * @param string $size    Registered image size.
 * @return string HTML <img> markup, or '' if the post has no featured image.
 */
function maglist_child_get_thumbnail( $post_id, $size = 'maglist-child-card' ) {
	if ( ! has_post_thumbnail( $post_id ) ) {
		return '';
	}

	return get_the_post_thumbnail(
		$post_id,
		$size,
		array(
			'class'   => 'na-img',
			'loading' => 'lazy',
			'alt'     => get_the_title( $post_id ),
		)
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
 * Canonical Nepali BS display format sitewide.
 * Example: सोमबार, ०४ साउन २०८३
 */
define( 'MAGLIST_CHILD_BS_DATE_FORMAT', 'l, d F Y' );

/**
 * Post date as Nepali Bikram Sambat (same format everywhere).
 *
 * @param int $post_id Post ID.
 * @return string
 */
function maglist_child_time_ago( $post_id ) {
	return maglist_child_nepali_bs_date( $post_id );
}

/**
 * Whether a PHP/WP date format is machine-oriented (datetime attrs, timestamps, etc.).
 *
 * @param string|null $format Date format.
 * @return bool
 */
function maglist_child_is_machine_date_format( $format ) {
	if ( null === $format || '' === $format ) {
		return false;
	}

	$machine = array(
		'U',
		'c',
		'r',
		'Y-m-d',
		'Y-m-d H:i:s',
		'Ymd',
		DATE_W3C,
		DATE_ATOM,
		DATE_ISO8601,
		DATE_RFC2822,
		DATE_RFC3339,
		DATE_RSS,
	);

	if ( in_array( $format, $machine, true ) ) {
		return true;
	}

	// Pure clock-time formats (no calendar day/month/year tokens).
	if ( ! preg_match( '/[DjFlMSWYnz]/', $format ) ) {
		return true;
	}

	return false;
}

/**
 * Convert an AD Y-m-d calendar day to Nepali BS display text.
 *
 * @param int    $year   Gregorian year.
 * @param int    $month  Gregorian month.
 * @param int    $day    Gregorian day.
 * @param string $format NDC format string.
 * @return string Empty string on failure.
 */
function maglist_child_format_ad_ymd_as_bs( $year, $month, $day, $format = 'l, d F Y' ) {
	$year  = (int) $year;
	$month = (int) $month;
	$day   = (int) $day;

	if ( $year < 1 || $month < 1 || $day < 1 || ! function_exists( 'ndc_eng_to_nep_date' ) ) {
		return '';
	}

	$format = apply_filters( 'maglist_child_nepali_bs_date_format', $format, 0 );

	$converted = ndc_eng_to_nep_date(
		array(
			'year'  => $year,
			'month' => $month,
			'day'   => $day,
		),
		'nep_char',
		$format
	);

	if ( ! is_array( $converted ) || empty( $converted['result'] ) ) {
		return '';
	}

	// Prefer the common Nepali "बार" spelling used on news sites.
	return str_replace(
		array( 'आइतवार', 'सोमवार' ),
		array( 'आइतबार', 'सोमबार' ),
		$converted['result']
	);
}

/**
 * Format a post's publish day in Nepali BS.
 *
 * @param int    $post_id Post ID.
 * @param string $format  NDC/PHP-style format.
 * @return string
 */
function maglist_child_nepali_bs_date( $post_id, $format = 'l, d F Y' ) {
	$post_id = absint( $post_id );
	if ( ! $post_id ) {
		return '';
	}

	// Avoid NDC / our own get_the_date filters.
	$ymd = get_post_time( 'Y-m-d', false, $post_id, true );
	if ( ! $ymd || ! is_string( $ymd ) ) {
		return '';
	}

	$parts = array_map( 'intval', explode( '-', $ymd ) );
	if ( count( $parts ) < 3 ) {
		return '';
	}

	return maglist_child_format_ad_ymd_as_bs( $parts[0], $parts[1], $parts[2], $format );
}

/**
 * Format a post's modified day in Nepali BS.
 *
 * @param int    $post_id Post ID.
 * @param string $format  NDC/PHP-style format.
 * @return string
 */
function maglist_child_nepali_bs_modified_date( $post_id, $format = 'l, d F Y' ) {
	$post_id = absint( $post_id );
	if ( ! $post_id ) {
		return '';
	}

	$ymd = get_post_modified_time( 'Y-m-d', false, $post_id, true );
	if ( ! $ymd || ! is_string( $ymd ) ) {
		return '';
	}

	$parts = array_map( 'intval', explode( '-', $ymd ) );
	if ( count( $parts ) < 3 ) {
		return '';
	}

	return maglist_child_format_ad_ymd_as_bs( $parts[0], $parts[1], $parts[2], $format );
}

/**
 * Today's date in Nepali BS, same format as post dates.
 *
 * @param string $format NDC/PHP-style format.
 * @return string
 */
function maglist_child_today_nepali_bs_date( $format = 'l, d F Y' ) {
	$ymd = explode( '-', current_time( 'Y-m-d' ) );
	if ( count( $ymd ) < 3 ) {
		return date_i18n( 'l, j F Y' );
	}

	$bs = maglist_child_format_ad_ymd_as_bs( (int) $ymd[0], (int) $ymd[1], (int) $ymd[2], $format );
	return $bs ? $bs : date_i18n( 'l, j F Y' );
}

/**
 * Force human-readable get_the_date() output to Nepali BS everywhere
 * (single posts, Maglist meta, related posts, widgets, etc.).
 *
 * @param string       $the_date Formatted date.
 * @param string       $format   Requested format.
 * @param int|WP_Post  $post     Post object or ID.
 * @return string
 */
function maglist_child_filter_the_date( $the_date, $format, $post ) {
	if ( maglist_child_is_machine_date_format( $format ) ) {
		return $the_date;
	}

	$post_id = $post instanceof WP_Post ? (int) $post->ID : absint( $post );
	if ( ! $post_id ) {
		return $the_date;
	}

	$bs = maglist_child_nepali_bs_date( $post_id );
	return $bs ? $bs : $the_date;
}
add_filter( 'get_the_date', 'maglist_child_filter_the_date', 20, 3 );

/**
 * Same BS format for modified dates when shown to readers.
 *
 * @param string       $the_date Formatted date.
 * @param string       $format   Requested format.
 * @param int|WP_Post  $post     Post object or ID.
 * @return string
 */
function maglist_child_filter_the_modified_date( $the_date, $format, $post ) {
	if ( maglist_child_is_machine_date_format( $format ) ) {
		return $the_date;
	}

	$post_id = $post instanceof WP_Post ? (int) $post->ID : absint( $post );
	if ( ! $post_id ) {
		return $the_date;
	}

	$bs = maglist_child_nepali_bs_modified_date( $post_id );
	return $bs ? $bs : $the_date;
}
add_filter( 'get_the_modified_date', 'maglist_child_filter_the_modified_date', 20, 3 );

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
		'<span class="na-badge">%s</span>',
		esc_html( $categories[0]->name )
	);
}

/**
 * Print a widget area wrapper.
 *
 * By default only prints when the sidebar has widgets. Pass $always_render = true
 * for ad-slot anchors that Ad Inserter (or other scripts) must target even when empty;
 * empty wrappers stay hidden via `.na-ad-slot:empty { display: none }`.
 *
 * @param string $sidebar_id     Registered sidebar/widget-area ID.
 * @param string $wrapper_class  Extra class(es) for the wrapping <div>.
 * @param bool   $always_render  Always output the wrapper (for HTML-selector hooks).
 */
function maglist_child_widget_area( $sidebar_id, $wrapper_class = '', $always_render = false ) {
	$active = is_active_sidebar( $sidebar_id );

	if ( ! $active && ! $always_render ) {
		return;
	}

	printf( '<div class="%s" id="%s">', esc_attr( $wrapper_class ), esc_attr( $sidebar_id ) );

	/**
	 * Server-render Ad Inserter into the sidebar-ad slot so the ad does not depend
	 * on client-side HTML replacement (which was easy to misconfigure / miss).
	 */
	if ( 'sidebar-ad' === $sidebar_id ) {
		maglist_child_render_sidebar_ad_inserter();
	}

	if ( $active ) {
		dynamic_sidebar( $sidebar_id );
	}
	echo '</div>';
}

/**
 * Ad Inserter block number used for the sidebar-ad slot (filterable).
 *
 * @return int Block number, or 0 to skip.
 */
function maglist_child_sidebar_ad_inserter_block() {
	return (int) apply_filters( 'maglist_child_sidebar_ad_inserter_block', 4 );
}

/**
 * Echo Ad Inserter sidebar creative when the plugin is available.
 */
function maglist_child_render_sidebar_ad_inserter() {
	if ( ! function_exists( 'adinserter' ) ) {
		return;
	}

	$block = maglist_child_sidebar_ad_inserter_block();
	if ( $block < 1 ) {
		return;
	}

	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Ad Inserter returns intentional ad HTML.
	echo adinserter( $block );
}
