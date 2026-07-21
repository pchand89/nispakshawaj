<?php
/**
 * Featured lead story for a category archive.
 *
 * @package Maglist_Child
 *
 * @var array $args {
 *   @type WP_Post $post Lead post.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post = isset( $args['post'] ) ? $args['post'] : null;
if ( ! $post instanceof WP_Post ) {
	return;
}

$has_thumb = has_post_thumbnail( $post );
?>
<article <?php post_class( 'na-cat-lead' . ( $has_thumb ? '' : ' na-cat-lead--no-thumb' ), $post ); ?>>
	<?php if ( $has_thumb ) : ?>
		<a class="na-cat-lead__thumb" href="<?php echo esc_url( get_permalink( $post ) ); ?>">
			<?php echo maglist_child_get_thumbnail( $post->ID, 'maglist-child-hero' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</a>
	<?php endif; ?>

	<div class="na-cat-lead__body">
		<h2 class="na-cat-lead__title">
			<a href="<?php echo esc_url( get_permalink( $post ) ); ?>">
				<?php echo esc_html( get_the_title( $post ) ); ?>
			</a>
		</h2>
		<p class="na-cat-lead__excerpt">
			<?php echo esc_html( wp_trim_words( get_the_excerpt( $post ), 36 ) ); ?>
		</p>
	</div>
</article>
