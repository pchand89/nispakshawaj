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
