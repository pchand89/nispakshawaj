<?php
/**
 * Layout: colored sports/news band — lead left + 2×2 grid right on tinted bg.
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$posts = isset( $args['posts'] ) ? $args['posts'] : array();
if ( empty( $posts ) ) {
	return;
}

$lead = array_shift( $posts );
$grid = array_slice( $posts, 0, 4 );
$maglist_child_label         = isset( $args['label'] ) ? $args['label'] : '';
$maglist_child_category_link = isset( $args['category_link'] ) ? $args['category_link'] : '';
$band_class = isset( $args['band'] ) ? sanitize_html_class( $args['band'] ) : 'navy';
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

		<div class="na-sports-band">
			<a class="na-sports-band__lead" href="<?php echo esc_url( get_permalink( $lead ) ); ?>">
				<?php if ( has_post_thumbnail( $lead ) ) : ?>
					<?php echo maglist_child_get_thumbnail( $lead->ID, 'maglist-child-hero' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php endif; ?>
				<span class="na-sports-band__lead-title"><?php echo esc_html( get_the_title( $lead ) ); ?></span>
			</a>

			<div class="na-sports-band__grid">
				<?php foreach ( $grid as $grid_post ) : ?>
					<a class="na-sports-band__card" href="<?php echo esc_url( get_permalink( $grid_post ) ); ?>">
						<?php if ( has_post_thumbnail( $grid_post ) ) : ?>
							<?php echo maglist_child_get_thumbnail( $grid_post->ID, 'maglist-child-card' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php endif; ?>
						<span class="na-sports-band__card-title"><?php echo esc_html( get_the_title( $grid_post ) ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
