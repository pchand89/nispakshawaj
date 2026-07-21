<?php
/**
 * Layout: lead image (title below) on the left + 2×2 thumbnail grid on the right.
 * Used for राजनीति / समाचार-style rows.
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

$lead   = array_shift( $posts );
$grid   = array_slice( $posts, 0, 4 );
$maglist_child_label         = isset( $args['label'] ) ? $args['label'] : '';
$maglist_child_category_link = isset( $args['category_link'] ) ? $args['category_link'] : '';
?>
<div class="na-section na-layout-lead-grid">
	<?php require get_stylesheet_directory() . '/template-parts/home/section-header.php'; ?>

	<div class="na-lead-grid">
		<article class="na-lead-grid__lead">
			<?php
			setup_postdata( $GLOBALS['post'] = $lead ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
			?>
			<?php if ( has_post_thumbnail( $lead ) ) : ?>
				<a class="na-lead-grid__lead-thumb" href="<?php echo esc_url( get_permalink( $lead ) ); ?>">
					<?php echo maglist_child_get_thumbnail( $lead->ID, 'maglist-child-hero' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			<?php endif; ?>
			<h3 class="na-lead-grid__lead-title">
				<a href="<?php echo esc_url( get_permalink( $lead ) ); ?>"><?php echo esc_html( get_the_title( $lead ) ); ?></a>
			</h3>
		</article>

		<div class="na-lead-grid__side">
			<?php foreach ( $grid as $side_post ) : ?>
				<article class="na-lead-grid__card">
					<?php if ( has_post_thumbnail( $side_post ) ) : ?>
						<a class="na-lead-grid__card-thumb" href="<?php echo esc_url( get_permalink( $side_post ) ); ?>">
							<?php echo maglist_child_get_thumbnail( $side_post->ID, 'maglist-child-card' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
					<?php endif; ?>
					<h3 class="na-lead-grid__card-title">
						<a href="<?php echo esc_url( get_permalink( $side_post ) ); ?>"><?php echo esc_html( get_the_title( $side_post ) ); ?></a>
					</h3>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</div>
<?php
wp_reset_postdata();
