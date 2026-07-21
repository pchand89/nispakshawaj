<?php
/**
 * Video section — full-bleed black band with one large overlay lead
 * and a vertical stack of smaller thumb+title items (TV-style layout).
 * Pulls from the "भिडियो" category.
 *
 * Expects $args: array( 'slug' => 'भिडियो', 'count' => 4, 'label' => '...' ).
 * Default count is 4 = 1 lead + 3 side items.
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$maglist_child_video_slug  = isset( $args['slug'] ) ? (string) $args['slug'] : '';
$maglist_child_video_count = isset( $args['count'] ) ? absint( $args['count'] ) : 4;

if ( ! maglist_child_category_exists( $maglist_child_video_slug ) ) {
	return;
}

$maglist_child_video_query = maglist_child_get_category_query( $maglist_child_video_slug, $maglist_child_video_count );

if ( ! $maglist_child_video_query->have_posts() ) {
	return;
}

$maglist_child_video_posts = $maglist_child_video_query->posts;
$maglist_child_video_lead  = array_shift( $maglist_child_video_posts );
$maglist_child_video_side  = array_slice( $maglist_child_video_posts, 0, 3 );

$maglist_child_video_term  = maglist_child_resolve_category( $maglist_child_video_slug );
$maglist_child_video_link  = $maglist_child_video_term ? get_category_link( $maglist_child_video_term ) : '';
$maglist_child_video_label = isset( $args['label'] ) ? $args['label'] : ( $maglist_child_video_term ? $maglist_child_video_term->name : esc_html__( 'भिडियो', 'maglist-child' ) );

wp_reset_postdata();
?>

<section class="na-band na-band--black na-video-band">
	<div class="na-container">
		<div class="na-section na-video">

			<div class="na-section__header na-section__header--on-dark">
				<h2 class="na-section__title">
					<?php if ( $maglist_child_video_link ) : ?>
						<a href="<?php echo esc_url( $maglist_child_video_link ); ?>"><?php echo esc_html( $maglist_child_video_label ); ?></a>
					<?php else : ?>
						<?php echo esc_html( $maglist_child_video_label ); ?>
					<?php endif; ?>
				</h2>

				<?php if ( $maglist_child_video_link ) : ?>
					<a class="na-section__more" href="<?php echo esc_url( $maglist_child_video_link ); ?>">
						<?php esc_html_e( 'थप समाचार', 'maglist-child' ); ?> &#8599;
					</a>
				<?php endif; ?>
			</div><!-- .na-section__header -->

			<div class="na-video__grid">
				<?php if ( $maglist_child_video_lead instanceof WP_Post ) : ?>
					<a
						href="<?php echo esc_url( get_permalink( $maglist_child_video_lead ) ); ?>"
						class="na-video__main<?php echo has_post_thumbnail( $maglist_child_video_lead ) ? '' : ' na-video__main--no-thumb'; ?>"
					>
						<?php if ( has_post_thumbnail( $maglist_child_video_lead ) ) : ?>
							<?php echo maglist_child_get_thumbnail( $maglist_child_video_lead->ID, 'maglist-child-hero' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php endif; ?>
						<span class="na-video__main-title"><?php echo esc_html( get_the_title( $maglist_child_video_lead ) ); ?></span>
					</a>
				<?php endif; ?>

				<?php if ( ! empty( $maglist_child_video_side ) ) : ?>
					<div class="na-video__side">
						<?php foreach ( $maglist_child_video_side as $maglist_child_side_post ) : ?>
							<a
								href="<?php echo esc_url( get_permalink( $maglist_child_side_post ) ); ?>"
								class="na-video__item<?php echo has_post_thumbnail( $maglist_child_side_post ) ? '' : ' na-video__item--no-thumb'; ?>"
							>
								<?php if ( has_post_thumbnail( $maglist_child_side_post ) ) : ?>
									<span class="na-video__item-thumb">
										<?php echo maglist_child_get_thumbnail( $maglist_child_side_post->ID, 'maglist-child-card' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</span>
								<?php endif; ?>
								<span class="na-video__item-title"><?php echo esc_html( get_the_title( $maglist_child_side_post ) ); ?></span>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div><!-- .na-video__grid -->

		</div><!-- .na-section.na-video -->
	</div>
</section>
