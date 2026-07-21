<?php
/**
 * Layout: large overlay lead on the left + two columns of thumb+title list rows.
 * Used for समाज-style rows.
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

$lead  = array_shift( $posts );
$col_a = array_slice( $posts, 0, 5 );
$col_b = array_slice( $posts, 5, 5 );
$maglist_child_label         = isset( $args['label'] ) ? $args['label'] : '';
$maglist_child_category_link = isset( $args['category_link'] ) ? $args['category_link'] : '';
?>
<div class="na-section na-layout-overlay-lists">
	<?php require get_stylesheet_directory() . '/template-parts/home/section-header.php'; ?>

	<div class="na-overlay-lists">
		<a class="na-overlay-lists__lead" href="<?php echo esc_url( get_permalink( $lead ) ); ?>">
			<?php if ( has_post_thumbnail( $lead ) ) : ?>
				<?php echo maglist_child_get_thumbnail( $lead->ID, 'maglist-child-hero' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php endif; ?>
			<span class="na-overlay-lists__lead-title"><?php echo esc_html( get_the_title( $lead ) ); ?></span>
		</a>

		<div class="na-overlay-lists__cols">
			<?php foreach ( array( $col_a, $col_b ) as $col_posts ) : ?>
				<ul class="na-overlay-lists__list">
					<?php foreach ( $col_posts as $list_post ) : ?>
						<li>
							<a href="<?php echo esc_url( get_permalink( $list_post ) ); ?>" class="na-list-row">
								<?php if ( has_post_thumbnail( $list_post ) ) : ?>
									<span class="na-list-row__thumb">
										<?php echo maglist_child_get_thumbnail( $list_post->ID, 'maglist-child-card' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</span>
								<?php endif; ?>
								<span class="na-list-row__title"><?php echo esc_html( get_the_title( $list_post ) ); ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endforeach; ?>
		</div>
	</div>
</div>
