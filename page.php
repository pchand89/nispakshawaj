<?php
/**
 * Page template — category hub pages use the archive layout; others
 * fall through to the Maglist parent page.php.
 *
 * @package Maglist_Child
 */

$hub_category = maglist_child_get_page_category_hub();

if ( ! $hub_category ) {
	require get_template_directory() . '/page.php';
	return;
}

get_header();

$query = maglist_child_get_category_archive_query( $hub_category->term_id );

get_template_part(
	'template-parts/category/archive',
	null,
	array(
		'title' => $hub_category->name,
		'posts' => $query->posts,
		'query' => $query,
	)
);

wp_reset_postdata();

get_footer();
