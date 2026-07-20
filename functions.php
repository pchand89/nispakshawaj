<?php
/**
 * Maglist Child (Ratopati Home) functions and definitions.
 *
 * This file only ever ADDS behaviour on top of the Maglist parent theme.
 * Nothing here overrides or edits parent classes/files directly - everything
 * is sandboxed inside this child theme folder.
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Disallow direct access.
}

define( 'MAGLIST_CHILD_VERSION', '1.1.0' );
define( 'MAGLIST_CHILD_DIR', get_stylesheet_directory() );
define( 'MAGLIST_CHILD_URI', get_stylesheet_directory_uri() );

/**
 * Homepage helper functions (thumbnails, time-ago, category queries, widget areas).
 * Kept in its own file so functions.php stays readable.
 */
require MAGLIST_CHILD_DIR . '/inc/homepage-helpers.php';

/**
 * Load child theme text domain (parent theme already loads its own
 * "maglist" text domain on after_setup_theme, this only handles our strings).
 */
function maglist_child_load_textdomain() {
	load_child_theme_textdomain( 'maglist-child', MAGLIST_CHILD_DIR . '/languages' );
}
add_action( 'after_setup_theme', 'maglist_child_load_textdomain' );

/**
 * Register custom, uniformly cropped image sizes used across the homepage grid
 * so every thumbnail (hero + category cards) lines up cleanly regardless of the
 * original upload's aspect ratio.
 */
function maglist_child_image_sizes() {
	add_image_size( 'maglist-child-hero', 900, 560, true );  // Main feature story.
	add_image_size( 'maglist-child-card', 420, 280, true );  // Category grid cards.
}
add_action( 'after_setup_theme', 'maglist_child_image_sizes' );

/**
 * Enqueue parent + child styles/scripts.
 *
 * Notes on dependencies:
 * - 'main-style'  is the handle the parent theme uses for its own style.css
 * - 'theme-style' is the handle the parent theme uses for assets/css/main.css
 * Both are registered by Maglist_Theme::scripts() (parent functions.php) which
 * always runs before this callback because it hooks into the same action at
 * the default priority (10) while this one runs at priority 20.
 */
function maglist_child_enqueue_assets() {
	$theme_version = MAGLIST_CHILD_VERSION;

	// Google Fonts tuned for legible Nepali (Devanagari) + Latin text.
	wp_enqueue_style(
		'maglist-child-google-fonts',
		'https://fonts.googleapis.com/css2?family=Mukta:wght@400;500;600;700&family=Noto+Serif+Devanagari:wght@500;600;700&display=swap',
		array(),
		null
	);

	// Child theme sitewide overrides (typography, category accents, hover states).
	wp_enqueue_style(
		'maglist-child-style',
		get_stylesheet_uri(),
		array( 'main-style', 'theme-style', 'maglist-child-google-fonts' ),
		$theme_version
	);

	// Ratopati-style homepage grid layout (only meaningfully used on the homepage,
	// but safe/cheap to load everywhere so it never 404s if reused elsewhere).
	wp_enqueue_style(
		'maglist-child-ratopati-home',
		MAGLIST_CHILD_URI . '/assets/css/ratopati-home.css',
		array( 'maglist-child-style' ),
		$theme_version
	);

	if ( is_front_page() || is_page_template( 'template-home-ratopati.php' ) ) {
		wp_enqueue_script(
			'maglist-child-ratopati-home',
			MAGLIST_CHILD_URI . '/assets/js/ratopati-home.js',
			array(),
			$theme_version,
			true
		);

		// Let sites change how many columns the "Home Sidebar Row" widgets lay
		// out in via the `maglist_child_sidebar_row_columns` filter, without
		// having to edit any CSS file.
		wp_add_inline_style(
			'maglist-child-ratopati-home',
			sprintf(
				':root{--ratopati-widget-row-columns:%d;}',
				absint( maglist_child_sidebar_row_columns() )
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'maglist_child_enqueue_assets', 20 );

/**
 * Register the drag-and-drop homepage widget areas.
 * These show up under Appearance > Widgets and can hold any standard widget,
 * Custom HTML block, or third-party ad-code widget.
 */
function maglist_child_register_sidebars() {
	$areas = array(
		'home-top-banner'    => array(
			'name'        => esc_html__( 'Home Top Banner Ad', 'maglist-child' ),
			'description' => esc_html__( 'Full-width banner/ad slot shown right below the header, above the hero section.', 'maglist-child' ),
		),
		'home-mid-grid-ad-1'  => array(
			'name'        => esc_html__( 'Home Mid-Grid Ad Slot 1', 'maglist-child' ),
			'description' => esc_html__( 'Ad slot placed between the hero section and the category blocks.', 'maglist-child' ),
		),
		'home-mid-grid-ad-2'  => array(
			'name'        => esc_html__( 'Home Mid-Grid Ad Slot 2', 'maglist-child' ),
			'description' => esc_html__( 'Ad slot placed further down the homepage, between category blocks.', 'maglist-child' ),
		),
		'home-sidebar-row'    => array(
			'name'        => esc_html__( 'Home Sidebar Row', 'maglist-child' ),
			'description' => esc_html__( 'A row of drag-and-drop widgets (recent posts, custom HTML, ads, etc.) shown near the bottom of the homepage.', 'maglist-child' ),
		),
	);

	foreach ( $areas as $id => $area ) {
		register_sidebar(
			array(
				'name'          => $area['name'],
				'id'            => $id,
				'description'   => $area['description'],
				'before_widget' => '<div id="%1$s" class="widget ratopati-widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title ratopati-widget-title">',
				'after_title'   => '</h3>',
			)
		);
	}
}
add_action( 'widgets_init', 'maglist_child_register_sidebars' );

/**
 * Let the "Home Sidebar Row" area lay its widgets out in responsive columns
 * automatically, however many widgets an admin drags into it.
 */
function maglist_child_sidebar_row_columns() {
	return apply_filters( 'maglist_child_sidebar_row_columns', 3 );
}
