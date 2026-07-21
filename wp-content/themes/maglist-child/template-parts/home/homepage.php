<?php
/**
 * Homepage body — section order and layouts modelled on the reference
 * news homepage screenshots (stacked hero, then varied category bands/grids).
 *
 * Each non-band category row gets its own content + sticky sidebar-ad column
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
 *                             Omit to auto-assign home-sidebar-ad-N for non-band rows.
 *                             Set false to skip the rail for that row.
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

$maglist_child_band_layouts = array( 'dark-band', 'sports-band' );
$maglist_child_sidebar_n    = 0;
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

		// Explicit false disables the rail; otherwise auto-number non-band rows.
		$sidebar_id = null;
		if ( array_key_exists( 'sidebar_ad', $maglist_child_section ) ) {
			$sidebar_id = $maglist_child_section['sidebar_ad'] ? (string) $maglist_child_section['sidebar_ad'] : null;
		} elseif ( ! $is_band ) {
			++$maglist_child_sidebar_n;
			$sidebar_id = 'home-sidebar-ad-' . $maglist_child_sidebar_n;
		}

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
