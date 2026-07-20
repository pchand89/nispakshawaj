<?php
/**
 * Floating "ताजा / लोकप्रिय" (Latest / Trending) tab widget.
 *
 * On ratopati.com this is a fixed button pinned near the bottom of the
 * viewport that slides open an overlay panel with two tabs: a plain list of
 * the newest posts ("ताजा") and a numbered 1-8 "most popular" ranking
 * ("लोकप्रिय"). It is NOT part of the normal content flow / grid - it's a
 * persistent, always-available panel, so it is rendered once and fixed via
 * CSS regardless of scroll position.
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$maglist_child_taja_query    = new WP_Query(
	array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => 8,
		'ignore_sticky_posts' => 1,
		'no_found_rows'       => true,
	)
);
$maglist_child_lokpriya_query = maglist_child_get_popular_query( 8 );
?>

<div class="rp-fixed-tab" data-maglist-child-fixed-tab>

	<div class="rp-fixed-tab__buttons">
		<button type="button" class="rp-fixed-tab__btn is-active" data-tab-target="taja">
			<?php esc_html_e( 'ताजा', 'maglist-child' ); ?>
		</button>
		<button type="button" class="rp-fixed-tab__btn" data-tab-target="lokpriya">
			<?php esc_html_e( 'लोकप्रिय', 'maglist-child' ); ?>
		</button>
	</div>

	<div class="rp-fixed-tab__panel">

		<button type="button" class="rp-fixed-tab__close" aria-label="<?php esc_attr_e( 'Close', 'maglist-child' ); ?>">&times;</button>

		<div class="rp-fixed-tab__tabs">
			<button type="button" class="rp-fixed-tab__tab is-active" data-tab-target="taja"><?php esc_html_e( 'ताजा', 'maglist-child' ); ?></button>
			<button type="button" class="rp-fixed-tab__tab" data-tab-target="lokpriya"><?php esc_html_e( 'लोकप्रिय', 'maglist-child' ); ?></button>
		</div>

		<div class="rp-fixed-tab__content is-active" data-tab-content="taja">
			<ul class="rp-fixed-tab__list">
				<?php if ( $maglist_child_taja_query->have_posts() ) : ?>
					<?php
					while ( $maglist_child_taja_query->have_posts() ) :
						$maglist_child_taja_query->the_post();
						?>
						<li>
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</li>
						<?php
					endwhile;
					wp_reset_postdata();
					?>
				<?php else : ?>
					<li class="rp-fixed-tab__empty"><?php esc_html_e( 'No recent posts yet.', 'maglist-child' ); ?></li>
				<?php endif; ?>
			</ul>
		</div>

		<div class="rp-fixed-tab__content" data-tab-content="lokpriya">
			<ul class="rp-fixed-tab__list rp-fixed-tab__list--numbered">
				<?php if ( $maglist_child_lokpriya_query->have_posts() ) : ?>
					<?php
					$maglist_child_rank = 0;
					while ( $maglist_child_lokpriya_query->have_posts() ) :
						$maglist_child_lokpriya_query->the_post();
						$maglist_child_rank++;
						?>
						<li>
							<span class="rp-fixed-tab__rank"><?php echo esc_html( $maglist_child_rank ); ?>.</span>
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</li>
						<?php
					endwhile;
					wp_reset_postdata();
					?>
				<?php else : ?>
					<li class="rp-fixed-tab__empty"><?php esc_html_e( 'No posts yet.', 'maglist-child' ); ?></li>
				<?php endif; ?>
			</ul>
		</div>

	</div><!-- .rp-fixed-tab__panel -->

</div><!-- .rp-fixed-tab -->
