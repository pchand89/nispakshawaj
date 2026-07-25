<?php
/**
 * Single post article column (title → meta → share → hero → body → tags → reactions → author → comments).
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'na-single-article' ); ?>>
	<header class="na-single-article__header">
		<?php
		$categories = get_the_category();
		if ( ! empty( $categories ) ) :
			$primary = $categories[0];
			?>
			<a class="na-single-article__cat" href="<?php echo esc_url( get_category_link( $primary->term_id ) ); ?>">
				<?php echo esc_html( $primary->name ); ?>
			</a>
		<?php endif; ?>

		<h1 class="na-single-article__title"><?php the_title(); ?></h1>

		<?php get_template_part( 'template-parts/single/meta' ); ?>
		<?php get_template_part( 'template-parts/single/share' ); ?>
	</header>

	<?php get_template_part( 'template-parts/single/hero' ); ?>

	<div class="na-single-article__content entry-content">
		<?php
		the_content();
		wp_link_pages(
			array(
				'before' => '<nav class="na-single-article__pages"><span class="na-single-article__pages-label">' . esc_html__( 'पृष्ठहरू:', 'maglist-child' ) . '</span>',
				'after'  => '</nav>',
			)
		);
		?>
	</div>

	<?php
	$tags = get_the_tags();
	if ( $tags ) :
		?>
		<div class="na-single-article__tags">
			<?php foreach ( $tags as $tag ) : ?>
				<a class="na-single-article__tag" href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>">
					<?php echo esc_html( $tag->name ); ?>
				</a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<?php get_template_part( 'template-parts/single/reactions' ); ?>
	<?php get_template_part( 'template-parts/single/author' ); ?>

	<?php if ( comments_open() || get_comments_number() ) : ?>
		<section class="na-single-comments" aria-label="<?php echo esc_attr__( 'प्रतिक्रिया', 'maglist-child' ); ?>">
			<h2 class="na-single-comments__title"><?php esc_html_e( 'प्रतिक्रिया', 'maglist-child' ); ?></h2>
			<?php comments_template(); ?>
		</section>
	<?php endif; ?>
</article>
