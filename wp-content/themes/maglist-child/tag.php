<?php
/**
 * Tag archive template — same layout as category archives.
 *
 * @package Maglist_Child
 */

get_header();

$posts = maglist_child_collect_main_query_posts();

get_template_part(
	'template-parts/category/archive',
	null,
	array(
		'title' => single_tag_title( '', false ),
		'posts' => $posts,
		'query' => null,
	)
);

get_footer();
