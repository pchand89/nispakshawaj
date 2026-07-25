<?php
/**
 * Single post — Ratopati-style article layout.
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<section class="na-single">
	<div class="na-container na-single__inner">
		<div class="na-single__layout">
			<div class="na-single__main">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/single/article' );
				endwhile;
				?>
			</div>
			<?php get_template_part( 'template-parts/single/sidebar' ); ?>
		</div>
		<?php get_template_part( 'template-parts/single/related' ); ?>
	</div>
</section>
<?php
get_footer();
