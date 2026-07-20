<?php
/**
 * Homepage body — section order and layouts modelled on the reference
 * news homepage screenshots (stacked hero, then varied category bands/grids).
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Homepage sections. Each item maps a WordPress category to a layout variant.
 * Filter with `maglist_child_home_sections` to reorder/retarget without editing this file.
 */
$maglist_child_home_sections = apply_filters(
	'maglist_child_home_sections',
	array(
		array(
			'slug'   => 'समाचार',
			'count'  => 7, // 1 lead + 6 list.
			'layout' => 'main-news',
		),
		array(
			'slug'   => 'राजनिती',
			'count'  => 5,
			'layout' => 'lead-grid',
		),
		array(
			'slug'   => 'समाज',
			'count'  => 11,
			'layout' => 'overlay-lists',
		),
		array(
			'slug'   => 'मनोरञ्जन',
			'count'  => 7,
			'layout' => 'dark-band',
			'band'   => 'purple',
		),
		array(
			'slug'   => 'खेलकुद',
			'count'  => 5,
			'layout' => 'sports-band',
			'band'   => 'navy',
		),
		array(
			'slug'   => 'शिक्षा / साहित्य',
			'count'  => 11, // up to 6 grid + 1 side lead + 4 list.
			'layout' => 'edu-split',
		),
		array(
			'slug'   => 'व्यवसाय',
			'count'  => 5,
			'layout' => 'lead-grid',
		),
		array(
			'slug'   => 'स्थानीय तह/ विकास',
			'count'  => 5,
			'layout' => 'lead-grid',
		),
	)
);
?>

<section class="na-home">

	<?php maglist_child_widget_area( 'home-top-banner', 'na-ad-slot na-ad-top', true ); ?>

	<div class="na-container">
		<?php get_template_part( 'template-parts/home/hero' ); ?>
		<?php get_template_part( 'template-parts/home/exclusive-block' ); ?>
		<?php maglist_child_widget_area( 'home-mid-grid-ad-1', 'na-ad-slot na-ad-mid', true ); ?>
	</div>

	<?php
	foreach ( $maglist_child_home_sections as $maglist_child_section_i => $maglist_child_section ) :
		$is_band = in_array( $maglist_child_section['layout'], array( 'dark-band', 'sports-band' ), true );

		if ( ! $is_band ) {
			echo '<div class="na-container">';
		}

		get_template_part( 'template-parts/home/category-block', null, $maglist_child_section );

		if ( ! $is_band ) {
			echo '</div>';
		}

		if ( 1 === $maglist_child_section_i ) {
			echo '<div class="na-container">';
			maglist_child_widget_area( 'home-mid-grid-ad-2', 'na-ad-slot na-ad-mid', true );
			echo '</div>';
		}
	endforeach;
	?>

	<div class="na-container">
		<?php maglist_child_widget_area( 'home-sidebar-row', 'na-widget-row' ); ?>
	</div>

</section>

<?php
get_template_part(
	'template-parts/home/video-block',
	null,
	array(
		'slug'  => 'भिडियो',
		'count' => 4, // 1 lead + 3 side items.
	)
);
?>

<?php get_template_part( 'template-parts/home/fixed-tab' ); ?>
