<?php
/**
 * Page template — category hubs use the archive layout; other pages use a
 * Ratopati-style static content layout (centered title + narrow content).
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hub_category = maglist_child_get_page_category_hub();

if ( $hub_category ) {
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
	return;
}

get_header();
?>
<section class="na-page">
	<div class="na-container na-page__inner">
		<?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/page/content' );
		endwhile;
		?>
	</div>
</section>
<?php
get_footer();
