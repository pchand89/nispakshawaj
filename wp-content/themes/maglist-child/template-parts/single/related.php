<?php
/**
 * छुटाउनुभयो कि? related posts strip.
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$related = maglist_child_get_related_posts( get_the_ID(), 8 );
if ( empty( $related ) ) {
	return;
}
?>
<section class="na-single-related" aria-label="<?php echo esc_attr__( 'छुटाउनुभयो कि ?', 'maglist-child' ); ?>">
	<h2 class="na-single-related__title"><?php esc_html_e( 'छुटाउनुभयो कि ?', 'maglist-child' ); ?></h2>
	<div class="na-single-related__grid">
		<?php foreach ( $related as $related_post ) : ?>
			<article class="na-single-related__item">
				<a class="na-single-related__link" href="<?php echo esc_url( get_permalink( $related_post ) ); ?>">
					<span class="na-single-related__thumb">
						<?php echo maglist_child_get_thumbnail( $related_post->ID, 'maglist-child-card', true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</span>
					<span class="na-single-related__headline"><?php echo esc_html( get_the_title( $related_post ) ); ?></span>
				</a>
			</article>
		<?php endforeach; ?>
	</div>
</section>
