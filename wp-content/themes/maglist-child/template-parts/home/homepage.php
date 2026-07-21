<?php
/**
 * Homepage body — section order and layouts modelled on the reference
 * news homepage screenshots (stacked hero, then varied category bands/grids).
 *
 * Each non-band category row gets a fixed sticky sidebar-ad column
 * (Ratopati’s per-section `dn__news--wrap` / `dn__side--add` pattern).
 * Full-bleed bands stay edge-to-edge without a side rail.
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Homepage sections. Each item maps a WordPress category to a layout variant.
 * Filter with `maglist_child_home_sections` to reorder/retarget without editing this file.
 *
 * Optional keys:
 *   sidebar_ad (string|false) Widget-area ID for this row’s sticky rail.
 *                             Set false to skip the rail for that row.
 */
$maglist_child_home_sections = apply_filters(
	'maglist_child_home_sections',
	array(
		array(
			'slug'       => 'समाचार',
			'count'      => 7, // 1 lead + 2 stack + 4 bottom.
			'layout'     => 'main-news',
			'sidebar_ad' => 'home-sidebar-ad-1',
		),
		array(
			'slug'       => 'राजनिती',
			'count'      => 5,
			'layout'     => 'lead-grid',
			'sidebar_ad' => 'home-sidebar-ad-2',
		),
		array(
			'slug'       => 'समाज',
			'count'      => 11,
			'layout'     => 'overlay-lists',
			'sidebar_ad' => 'home-sidebar-ad-3',
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
			'slug'       => 'शिक्षा / साहित्य',
			'count'      => 11, // up to 6 grid + 1 side lead + 4 list.
			'layout'     => 'edu-split',
			'sidebar_ad' => 'home-sidebar-ad-4',
		),
		array(
			'slug'       => 'व्यवसाय',
			'count'      => 5,
			'layout'     => 'lead-grid',
			'sidebar_ad' => 'home-sidebar-ad-5',
		),
		array(
			'slug'       => 'स्थानीय तह/ विकास',
			'count'      => 5,
			'layout'     => 'lead-grid',
			'sidebar_ad' => 'home-sidebar-ad-6',
		),
	)
);

$maglist_child_band_layouts = array( 'dark-band', 'sports-band' );
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
		$is_band = in_array( $maglist_child_section['layout'], $maglist_child_band_layouts, true );

		// Render the section first so empty categories never leave an orphan ad rail.
		ob_start();
		get_template_part( 'template-parts/home/category-block', null, $maglist_child_section );
		$maglist_child_section_html = trim( ob_get_clean() );

		if ( '' === $maglist_child_section_html ) {
			continue;
		}

		$sidebar_id = null;
		if ( array_key_exists( 'sidebar_ad', $maglist_child_section ) && $maglist_child_section['sidebar_ad'] ) {
			$sidebar_id = (string) $maglist_child_section['sidebar_ad'];
		}

		// Always open the rail when a slot is configured so Widgets and Ad Inserter
		// anchors both render (do not gate on is_active_sidebar — AI-only slots
		// would otherwise never appear).
		if ( $sidebar_id ) :
			?>
			<div class="na-container na-home__layout">
				<div class="na-home__main">
					<?php echo $maglist_child_section_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- template HTML ?>
				</div>
				<aside class="na-home__sidebar" aria-label="<?php echo esc_attr__( 'Advertisement', 'maglist-child' ); ?>">
					<?php maglist_child_widget_area( $sidebar_id, 'na-ad-slot na-ad-sidebar', true ); ?>
				</aside>
			</div>
			<?php
		elseif ( $is_band ) :
			echo $maglist_child_section_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- template HTML
		else :
			echo '<div class="na-container">';
			echo $maglist_child_section_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- template HTML
			echo '</div>';
		endif;

		if ( 1 === $maglist_child_section_i ) :
			echo '<div class="na-container">';
			maglist_child_widget_area( 'home-mid-grid-ad-2', 'na-ad-slot na-ad-mid', true );
			echo '</div>';
		endif;
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
