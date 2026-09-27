<?php
/**
 * Search results — same lead + feed layout as a category archive.
 *
 * @package Maglist_Child
 */

get_header();

$posts      = maglist_child_collect_main_query_posts();
$query_text = get_search_query();
$found      = isset( $GLOBALS['wp_query'] ) ? (int) $GLOBALS['wp_query']->found_posts : count( $posts );

if ( '' !== $query_text && $found > 0 ) {
	$subtitle = maglist_child_to_nepali_digits( (string) $found ) . ' समाचार';
} else {
	$subtitle = '';
}

get_template_part(
	'template-parts/category/archive',
	null,
	array(
		'title'       => '' !== $query_text ? $query_text : 'खोज',
		'posts'       => $posts,
		'query'       => null,
		'subtitle'    => $subtitle,
		'show_search' => true,
	)
);

get_footer();
