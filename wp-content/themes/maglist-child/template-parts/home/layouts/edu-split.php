<?php
/**
 * Layout: 3×2 card grid on the left + side lead + headline list on the right.
 * Used for शिक्षा / साहित्य-style rows.
 *
 * Expects $args: posts (WP_Post[]), label, category_link.
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

$grid      = array_slice( $posts, 0, 6 );
$remainder = array_slice( $posts, 6 );
$side_lead = ! empty( $remainder ) ? array_shift( $remainder ) : null;
$side_list = array_slice( $remainder, 0, 4 );

$maglist_child_label         = isset( $args['label'] ) ? $args['label'] : '';
$maglist_child_category_link = isset( $args['category_link'] ) ? $args['category_link'] : '';
?>
<div class="na-section na-layout-edu-split">
	<?php require get_stylesheet_directory() . '/template-parts/home/section-header.php'; ?>

	<div class="na-edu-split">
		<div class="na-edu-split__grid">
			<?php foreach ( $grid as $grid_post ) : ?>
				<article class="na-edu-split__card">
					<?php if ( has_post_thumbnail( $grid_post ) ) : ?>
						<a class="na-edu-split__card-thumb" href="<?php echo esc_url( get_permalink( $grid_post ) ); ?>">
							<?php echo maglist_child_get_thumbnail( $grid_post->ID, 'maglist-child-card' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
					<?php endif; ?>
					<h3 class="na-edu-split__card-title">
						<a href="<?php echo esc_url( get_permalink( $grid_post ) ); ?>"><?php echo esc_html( get_the_title( $grid_post ) ); ?></a>
					</h3>
				</article>
			<?php endforeach; ?>
		</div>

		<?php if ( $side_lead instanceof WP_Post ) : ?>
			<div class="na-edu-split__side">
				<article class="na-edu-split__side-lead">
					<?php if ( has_post_thumbnail( $side_lead ) ) : ?>
						<a class="na-edu-split__side-thumb" href="<?php echo esc_url( get_permalink( $side_lead ) ); ?>">
							<?php echo maglist_child_get_thumbnail( $side_lead->ID, 'maglist-child-card' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
					<?php endif; ?>
					<h3 class="na-edu-split__side-title">
						<a href="<?php echo esc_url( get_permalink( $side_lead ) ); ?>"><?php echo esc_html( get_the_title( $side_lead ) ); ?></a>
					</h3>
				</article>

				<?php if ( ! empty( $side_list ) ) : ?>
					<ul class="na-edu-split__list">
						<?php foreach ( $side_list as $list_post ) : ?>
							<li>
								<a href="<?php echo esc_url( get_permalink( $list_post ) ); ?>">
									<?php echo esc_html( get_the_title( $list_post ) ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
