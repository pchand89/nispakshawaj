<?php
/**
 * Single post helpers (banner disable, date+time, related query).
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Place category for a post's byline: a district, else सुदूरपश्चिम / राष्ट्रिय / अन्तर्राष्ट्रिय.
 *
 * Uses the assigned category first, then the dateline at the start of the story.
 *
 * @param int $post_id Post ID.
 * @return WP_Term|null
 */
function maglist_child_post_place_term( $post_id ) {
	$post_id = absint( $post_id );
	$post    = get_post( $post_id );
	if ( ! $post instanceof WP_Post ) {
		return null;
	}

	$terms = get_the_terms( $post_id, 'category' );
	$terms = is_array( $terms ) ? $terms : array();

	$sudur      = function_exists( 'maglist_child_resolve_category' ) ? maglist_child_resolve_category( 'सुदूरपश्चिम' ) : null;
	$sudur_id   = $sudur instanceof WP_Term ? (int) $sudur->term_id : 0;
	$districts  = array();
	$broad_rank = array(
		'सुदूरपश्चिम'  => 1,
		'राष्ट्रिय'     => 2,
		'अन्तर्राष्ट्रिय' => 3,
	);
	$broad      = array();

	foreach ( $terms as $term ) {
		if ( ! $term instanceof WP_Term ) {
			continue;
		}
		if ( $sudur_id && (int) $term->parent === $sudur_id ) {
			$districts[] = $term;
		}
		if ( isset( $broad_rank[ $term->name ] ) ) {
			$broad[ $broad_rank[ $term->name ] ] = $term;
		}
	}

	if ( count( $districts ) === 1 ) {
		return $districts[0];
	}

	if ( count( $districts ) > 1 && function_exists( 'maglist_child_category_plan' ) ) {
		$plan  = maglist_child_category_plan();
		$slug  = maglist_child_dateline_place( $post->post_content, $plan['places'] );
		$slug  = $slug ? $slug : maglist_child_title_place( $post->post_title, $plan['places'] );
		$match = $slug && function_exists( 'maglist_child_resolve_category' ) ? maglist_child_resolve_category( $slug ) : null;
		if ( $match instanceof WP_Term ) {
			foreach ( $districts as $district ) {
				if ( (int) $district->term_id === (int) $match->term_id ) {
					return $district;
				}
			}
		}
		return $districts[0];
	}

	if ( $broad ) {
		ksort( $broad );
		return reset( $broad );
	}

	if ( ! function_exists( 'maglist_child_category_plan' ) || ! function_exists( 'maglist_child_resolve_category' ) ) {
		return null;
	}

	$plan = maglist_child_category_plan();
	$slug = maglist_child_dateline_place( $post->post_content, $plan['places'] );
	$slug = $slug ? $slug : maglist_child_title_place( $post->post_title, $plan['places'] );
	if ( ! $slug ) {
		return null;
	}

	$term = maglist_child_resolve_category( $slug );
	return $term instanceof WP_Term ? $term : null;
}

/**
 * Byline name. The desk account reads as संवाददाता; a named author keeps their name.
 *
 * @param int $author_id User ID.
 * @return string
 */
function maglist_child_post_byline_label( $author_id ) {
	$name = (string) get_the_author_meta( 'display_name', $author_id );
	$site = (string) get_bloginfo( 'name' );
	if ( $name === '' || $name === $site || 'निश्पक्ष आवाज' === $name ) {
		return 'संवाददाता';
	}
	return $name;
}

/**
 * Never show Maglist's inner banner (title-over-image) on single posts.
 *
 * @param bool $disable Whether the banner is already disabled.
 * @return bool
 */
function maglist_child_disable_single_inner_banner( $disable ) {
	if ( is_singular( 'post' ) ) {
		return true;
	}
	return (bool) $disable;
}
add_filter( 'maglist_disable_inner_banner_content', 'maglist_child_disable_single_inner_banner' );

/**
 * Convert ASCII digits to Nepali Devanagari digits.
 *
 * @param string $text Input text.
 * @return string
 */
function maglist_child_to_nepali_digits( $text ) {
	return strtr(
		(string) $text,
		array(
			'0' => '०',
			'1' => '१',
			'2' => '२',
			'3' => '३',
			'4' => '४',
			'5' => '५',
			'6' => '६',
			'7' => '७',
			'8' => '८',
			'9' => '९',
		)
	);
}

/**
 * Nepali BS date string for a post (no time).
 *
 * @param int $post_id Post ID.
 * @return string
 */
function maglist_child_single_date( $post_id ) {
	$post_id = absint( $post_id );
	$date    = maglist_child_nepali_bs_date( $post_id );
	if ( '' === $date ) {
		$date = get_the_date( '', $post_id );
	}
	return (string) $date;
}

/**
 * Clock time for a post in Nepali digits. Example: १५ : ०२
 *
 * Time is parsed from post_date directly so filtered get_post_time()
 * cannot inject a Unix timestamp into the hour slot.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function maglist_child_single_time( $post_id ) {
	$post_id = absint( $post_id );
	$post    = get_post( $post_id );
	if ( ! $post instanceof WP_Post || empty( $post->post_date ) ) {
		return '';
	}

	// Local MySQL datetime: YYYY-MM-DD HH:MM:SS
	$hour   = 0;
	$minute = 0;
	if ( preg_match( '/^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2})/', $post->post_date, $m ) ) {
		$hour   = (int) $m[4];
		$minute = (int) $m[5];
	}

	return maglist_child_to_nepali_digits(
		sprintf( '%02d : %02d', $hour, $minute )
	);
}

/**
 * Nepali BS date plus clock time for single-post meta.
 * Example: शनिबार, ०९ साउन २०८३, १५ : ०२
 *
 * @param int $post_id Post ID.
 * @return string
 */
function maglist_child_single_datetime( $post_id ) {
	$date = maglist_child_single_date( $post_id );
	$time = maglist_child_single_time( $post_id );
	if ( '' === $time ) {
		return $date;
	}
	return trim( $date . ', ' . $time );
}

/**
 * Same-category related posts for the “छुटाउनुभयो कि?” strip.
 *
 * @param int $post_id Post ID.
 * @param int $count   Number of posts.
 * @return WP_Post[]
 */
function maglist_child_get_related_posts( $post_id, $count = 8 ) {
	$post_id = absint( $post_id );
	$cats    = wp_get_post_categories( $post_id );
	$args    = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => (int) $count,
		'post__not_in'        => array( $post_id ),
		'ignore_sticky_posts' => 1,
		'no_found_rows'       => true,
	);

	if ( ! empty( $cats ) ) {
		$args['category__in'] = $cats;
	}

	$query = new WP_Query( $args );
	return $query->posts;
}
