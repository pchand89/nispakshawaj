<?php
/**
 * Shared Ratopati-style homepage body.
 *
 * Included by both front-page.php and template-home-ratopati.php so the exact
 * same markup/behaviour is used no matter which "Front page displays" setting
 * (Settings > Reading) the site owner chooses.
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Category blocks shown below the hero. Edit this array (or hook the filter
 * below) to change which categories appear, how many posts each pulls, and
 * how many columns each row uses.
 *
 * 'slug' can be left as '' for a generic "Latest" block. Each block's heading
 * auto-derives from the real WordPress category name unless you add an
 * explicit 'label' key to override it (see category-block.php).
 *
 * IMPORTANT: on nispakshawaj.com, category slugs are stored as raw
 * Devanagari/Nepali text (not English transliterations) - e.g. 'राजनिती',
 * not 'politics'. maglist_child_resolve_category() (inc/homepage-helpers.php)
 * tries several encodings/fallbacks, but the slugs below must still match
 * what actually exists under Posts > Categories on this site.
 */
$maglist_child_home_categories = apply_filters(
	'maglist_child_home_categories',
	array(
		array(
			'slug'    => 'राजनिती', // Politics.
			'count'   => 6,
			'columns' => 3,
		),
		array(
			'slug'    => 'समाज', // Society.
			'count'   => 6,
			'columns' => 3,
		),
		array(
			'slug'    => 'व्यवसाय', // Business/Economy.
			'count'   => 4,
			'columns' => 2,
		),
		array(
			'slug'    => 'खेलकुद', // Sports.
			'count'   => 4,
			'columns' => 2,
		),
	)
);
?>

<section class="ratopati-home">

	<?php maglist_child_widget_area( 'home-top-banner', 'ratopati-ad-slot ratopati-ad-top' ); ?>

	<div class="ratopati-container">

		<?php get_template_part( 'template-parts/home/hero' ); ?>

		<?php maglist_child_widget_area( 'home-mid-grid-ad-1', 'ratopati-ad-slot ratopati-ad-mid' ); ?>

		<?php
		foreach ( $maglist_child_home_categories as $maglist_child_cat_index => $maglist_child_cat_args ) :

			if ( ! maglist_child_category_exists( $maglist_child_cat_args['slug'] ) ) {
				continue; // Category doesn't exist on this site yet - skip it silently.
			}

			get_template_part( 'template-parts/home/category-block', null, $maglist_child_cat_args );

			// Drop the second ad slot roughly halfway through the category rows.
			if ( 1 === $maglist_child_cat_index ) {
				maglist_child_widget_area( 'home-mid-grid-ad-2', 'ratopati-ad-slot ratopati-ad-mid' );
			}
		endforeach;
		?>

		<?php maglist_child_widget_area( 'home-sidebar-row', 'ratopati-widget-row' ); ?>

	</div><!-- .ratopati-container -->

</section><!-- .ratopati-home -->

<?php
/**
 * Floating "ताजा / लोकप्रिय" tab widget - rendered once, fixed-positioned via
 * CSS, so it deliberately sits outside of .ratopati-container.
 */
get_template_part( 'template-parts/home/fixed-tab' );
