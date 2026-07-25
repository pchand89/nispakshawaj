<?php
/**
 * Author archive template — same layout as category archives.
 *
 * @package Maglist_Child
 */

get_header();

$author = get_queried_object();
$title  = ( $author instanceof WP_User ) ? $author->display_name : get_the_archive_title();
$posts  = maglist_child_collect_main_query_posts();

get_template_part(
	'template-parts/category/archive',
	null,
	array(
		'title' => $title,
		'posts' => $posts,
		'query' => null,
	)
);

get_footer();
