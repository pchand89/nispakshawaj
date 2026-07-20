<?php
/**
 * Grid-view card for a category archive feed item.
 *
 * @package Maglist_Child
 *
 * @var array $args {
 *   @type WP_Post $post Post.
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
<article <?php post_class( 'na-cat-grid-item' . ( $has_thumb ? '' : ' na-cat-grid-item--no-thumb' ), $post ); ?>>
	<a class="na-cat-grid-item__link" href="<?php echo esc_url( get_permalink( $post ) ); ?>">
		<?php if ( $has_thumb ) : ?>
			<span class="na-cat-grid-item__thumb">
				<?php echo maglist_child_get_thumbnail( $post->ID, 'maglist-child-card' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</span>
		<?php endif; ?>
		<span class="na-cat-grid-item__title"><?php echo esc_html( get_the_title( $post ) ); ?></span>
	</a>
</article>
