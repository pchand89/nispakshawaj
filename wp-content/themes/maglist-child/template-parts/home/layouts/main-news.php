<?php
/**
 * Layout: large overlay lead + two stacked overlays, then a row of
 * thumb+title items. Used for समाचार (matches the dark-band composition
 * on a light section background).
 *
 * Expects $args: posts (WP_Post[]), label, category_link.
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$posts = isset( $args['posts'] ) ? $args['posts'] : array();
if ( count( $posts ) < 3 ) {
	return;
}

$lead   = array_shift( $posts );
$stack  = array_slice( $posts, 0, 2 );
$bottom = array_slice( $posts, 2, 4 );
$maglist_child_label         = isset( $args['label'] ) ? $args['label'] : '';
$maglist_child_category_link = isset( $args['category_link'] ) ? $args['category_link'] : '';
?>
<div class="na-section na-layout-main-news">
	<?php require get_stylesheet_directory() . '/template-parts/home/section-header.php'; ?>

	<div class="na-main-news">
		<div class="na-main-news__feature">
			<a class="na-main-news__lead" href="<?php echo esc_url( get_permalink( $lead ) ); ?>">
				<?php if ( has_post_thumbnail( $lead->ID ) ) : ?>
					<?php echo maglist_child_get_thumbnail( $lead->ID, 'maglist-child-hero' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php endif; ?>
				<span class="na-main-news__overlay-title"><?php echo esc_html( get_the_title( $lead ) ); ?></span>
			</a>

			<div class="na-main-news__stack">
				<?php foreach ( $stack as $stack_post ) : ?>
					<a class="na-main-news__stack-item" href="<?php echo esc_url( get_permalink( $stack_post ) ); ?>">
						<?php if ( has_post_thumbnail( $stack_post->ID ) ) : ?>
							<?php echo maglist_child_get_thumbnail( $stack_post->ID, 'maglist-child-card' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php endif; ?>
						<span class="na-main-news__overlay-title"><?php echo esc_html( get_the_title( $stack_post ) ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>

		<?php if ( ! empty( $bottom ) ) : ?>
			<div class="na-main-news__bottom">
				<?php foreach ( $bottom as $bottom_post ) : ?>
					<a class="na-main-news__row" href="<?php echo esc_url( get_permalink( $bottom_post ) ); ?>">
						<?php if ( has_post_thumbnail( $bottom_post->ID ) ) : ?>
							<span class="na-main-news__row-thumb">
								<?php echo maglist_child_get_thumbnail( $bottom_post->ID, 'maglist-child-card' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</span>
						<?php endif; ?>
						<span class="na-main-news__row-title"><?php echo esc_html( get_the_title( $bottom_post ) ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
