<?php
/**
 * Missing page — a short message, the search form, and recent stories.
 *
 * @package Maglist_Child
 */

get_header();
?>
<section class="na-missing">
	<div class="na-container na-missing__inner">
		<?php maglist_child_the_breadcrumbs(); ?>
		<h1 class="na-missing__title"><?php esc_html_e( 'पृष्ठ भेटिएन', 'maglist-child' ); ?></h1>
		<p class="na-missing__text"><?php esc_html_e( 'तपाईंले खोल्नुभएको पृष्ठ उपलब्ध छैन। तलबाट खोज्नुहोस्, वा गृहपृष्ठमा फर्कनुहोस्।', 'maglist-child' ); ?></p>
		<div class="na-inline-search">
			<?php get_search_form(); ?>
		</div>
		<p class="na-missing__home-wrap">
			<a class="na-missing__home" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'गृहपृष्ठ', 'maglist-child' ); ?></a>
		</p>
		<?php get_template_part( 'template-parts/category/sidebar-recent' ); ?>
	</div>
</section>
<?php
get_footer();
