<?php
/**
 * Single post sidebar: sidebar-ad + Maglist sidebar widgets + भर्खरै + ट्रेन्डिङ.
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_id = get_the_ID();

$recent = new WP_Query(
	array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => 8,
		'post__not_in'        => array( $current_id ),
		'ignore_sticky_posts' => 1,
		'no_found_rows'       => true,
	)
);

$trending_args = array(
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'posts_per_page'      => 8,
	'post__not_in'        => array( $current_id ),
	'ignore_sticky_posts' => 1,
	'no_found_rows'       => true,
	'orderby'             => 'comment_count',
	'order'               => 'DESC',
);
$trending      = new WP_Query( $trending_args );
if ( ! $trending->have_posts() ) {
	$trending_args['orderby'] = 'date';
	unset( $trending_args['order'] );
	$trending = new WP_Query( $trending_args );
}
?>
<aside id="secondary" class="na-single__sidebar widget-area" aria-label="<?php echo esc_attr__( 'साइडबार', 'maglist-child' ); ?>">
	<?php maglist_child_widget_area( 'sidebar-ad', 'na-ad-slot na-ad-sidebar', true ); ?>

	<div class="na-single-widgets">
		<?php if ( is_active_sidebar( 'maglist_sidebar' ) ) : ?>
			<?php
			$sidebar = apply_filters( Maglist_Theme::fn_prefix( 'sidebar' ), 'maglist_sidebar' );
			dynamic_sidebar( $sidebar );
			?>
		<?php else : ?>
			<?php
			Maglist_Theme::the_default_search();
			Maglist_Theme::the_default_recent_post();
			Maglist_Theme::the_default_archive();
			?>
		<?php endif; ?>
	</div>

	<?php if ( $recent->have_posts() ) : ?>
		<div class="na-single-sideblock">
			<h2 class="na-single-sideblock__title"><?php esc_html_e( 'भर्खरै', 'maglist-child' ); ?></h2>
			<ul class="na-single-sideblock__list">
				<?php
				while ( $recent->have_posts() ) :
					$recent->the_post();
					?>
					<li>
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</li>
				<?php endwhile; ?>
			</ul>
		</div>
		<?php
		wp_reset_postdata();
	endif;
	?>

	<?php if ( $trending->have_posts() ) : ?>
		<div class="na-single-sideblock na-single-sideblock--trending">
			<h2 class="na-single-sideblock__title"><?php esc_html_e( 'ट्रेन्डिङ', 'maglist-child' ); ?></h2>
			<ol class="na-single-sideblock__list na-single-sideblock__list--numbered">
				<?php
				$rank = 0;
				while ( $trending->have_posts() ) :
					$trending->the_post();
					++$rank;
					?>
					<li>
						<span class="na-single-sideblock__rank" aria-hidden="true"><?php echo esc_html( maglist_child_to_nepali_digits( (string) $rank ) ); ?>.</span>
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</li>
				<?php endwhile; ?>
			</ol>
		</div>
		<?php
		wp_reset_postdata();
	endif;
	?>
</aside>
