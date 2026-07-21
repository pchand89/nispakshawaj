<?php
/**
 * Layout: full-bleed dark band (मनोरञ्जन style).
 * Top: large overlay + two stacked overlays. Bottom: 4 thumb+title rows.
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

$feature = array_shift( $posts );
$stack   = array_slice( $posts, 0, 2 );
$bottom  = array_slice( $posts, 2, 4 );
$maglist_child_label         = isset( $args['label'] ) ? $args['label'] : '';
$maglist_child_category_link = isset( $args['category_link'] ) ? $args['category_link'] : '';
$band_class = isset( $args['band'] ) ? sanitize_html_class( $args['band'] ) : 'purple';
?>
<section class="na-band na-band--<?php echo esc_attr( $band_class ); ?>">
	<div class="na-container">
		<div class="na-section__header na-section__header--on-dark">
			<h2 class="na-section__title">
				<?php if ( $maglist_child_category_link ) : ?>
					<a href="<?php echo esc_url( $maglist_child_category_link ); ?>"><?php echo esc_html( $maglist_child_label ); ?></a>
				<?php else : ?>
					<?php echo esc_html( $maglist_child_label ); ?>
				<?php endif; ?>
			</h2>
			<?php if ( $maglist_child_category_link ) : ?>
				<a class="na-section__more" href="<?php echo esc_url( $maglist_child_category_link ); ?>">
					<?php esc_html_e( 'थप समाचार', 'maglist-child' ); ?> &#8599;
				</a>
			<?php endif; ?>
		</div>

		<div class="na-dark-feature">
			<a class="na-dark-feature__main" href="<?php echo esc_url( get_permalink( $feature ) ); ?>">
				<?php if ( has_post_thumbnail( $feature ) ) : ?>
					<?php echo maglist_child_get_thumbnail( $feature->ID, 'maglist-child-hero' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php endif; ?>
				<span class="na-dark-feature__title"><?php echo esc_html( get_the_title( $feature ) ); ?></span>
			</a>

			<div class="na-dark-feature__stack">
				<?php foreach ( $stack as $stack_post ) : ?>
					<a class="na-dark-feature__stack-item" href="<?php echo esc_url( get_permalink( $stack_post ) ); ?>">
						<?php if ( has_post_thumbnail( $stack_post ) ) : ?>
							<?php echo maglist_child_get_thumbnail( $stack_post->ID, 'maglist-child-card' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php endif; ?>
						<span class="na-dark-feature__title"><?php echo esc_html( get_the_title( $stack_post ) ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>

		<?php if ( ! empty( $bottom ) ) : ?>
			<div class="na-dark-feature__bottom">
				<?php foreach ( $bottom as $bottom_post ) : ?>
					<a class="na-list-row na-list-row--on-dark" href="<?php echo esc_url( get_permalink( $bottom_post ) ); ?>">
						<?php if ( has_post_thumbnail( $bottom_post ) ) : ?>
							<span class="na-list-row__thumb">
								<?php echo maglist_child_get_thumbnail( $bottom_post->ID, 'maglist-child-card' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</span>
						<?php endif; ?>
						<span class="na-list-row__title"><?php echo esc_html( get_the_title( $bottom_post ) ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
