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
?>
<article <?php post_class( 'na-cat-grid-item', $post ); ?>>
	<a class="na-cat-grid-item__link" href="<?php echo esc_url( get_permalink( $post ) ); ?>">
		<span class="na-cat-grid-item__thumb">
			<?php echo maglist_child_get_thumbnail( $post->ID, 'maglist-child-card', true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</span>
		<span class="na-cat-grid-item__title"><?php echo esc_html( get_the_title( $post ) ); ?></span>
	</a>
</article>
