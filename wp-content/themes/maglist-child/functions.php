<?php
/**
 * Maglist Child (Home Layout) functions and definitions.
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

define( 'MAGLIST_CHILD_VERSION', '1.9.67' );
define( 'MAGLIST_CHILD_DIR', get_stylesheet_directory() );
define( 'MAGLIST_CHILD_URI', get_stylesheet_directory_uri() );

/**
 * Maglist Customizer logo widths used by the child header (same on every page).
 *
 * @return array{desktop:int,tablet:int,mobile:int}
 */
function maglist_child_logo_widths() {
	$desktop = absint( get_theme_mod( 'maglist-logo-size-desktop', 300 ) );
	$tablet  = absint( get_theme_mod( 'maglist-logo-size-tablet', 260 ) );
	$mobile  = absint( get_theme_mod( 'maglist-logo-size-mobile', 200 ) );

	return array(
		'desktop' => $desktop >= 80 ? $desktop : 300,
		'tablet'  => $tablet >= 60 ? $tablet : 260,
		'mobile'  => $mobile >= 40 ? $mobile : 200,
	);
}

/**
 * Homepage helper functions (thumbnails, time-ago, category queries, widget areas).
 * Kept in its own file so functions.php stays readable.
 */
require MAGLIST_CHILD_DIR . '/inc/homepage-helpers.php';

/**
 * Category archive helpers (hub detection, pagination, query).
 */
require MAGLIST_CHILD_DIR . '/inc/category-archive.php';

/**
 * Single post helpers (banner disable, related query, date+time).
 */
require MAGLIST_CHILD_DIR . '/inc/single-post.php';

/**
 * DB-backed reaction counts + REST API.
 */
require MAGLIST_CHILD_DIR . '/inc/reactions.php';

/**
 * DB-backed share counts + REST API.
 */
require MAGLIST_CHILD_DIR . '/inc/shares.php';

/**
 * Facebook Graph share_count sync into stored totals.
 */
require MAGLIST_CHILD_DIR . '/inc/share-facebook-sync.php';

/**
 * Map Unicode path segments to Softaculous percent-encoded slugs (fixes 404s).
 */
require MAGLIST_CHILD_DIR . '/inc/permalink-slug-fix.php';

/**
 * One-time Ad Inserter selector retarget (old Maglist banner → child ad slots).
 */
require MAGLIST_CHILD_DIR . '/inc/ad-inserter-retarget.php';

/**
 * Footer helpers (category / useful / social link lists).
 */
require MAGLIST_CHILD_DIR . '/inc/footer.php';

/**
 * Static page helpers (Ratopati-style about/contact/privacy layout).
 */
require MAGLIST_CHILD_DIR . '/inc/static-page.php';

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
	// Poppins is included because the reference news layout's own production CSS
	// (build/css/app.min.css) specifically sets font-family:poppins on its
	// homepage section headings (.sec-title h2) - everything else on their
	// site uses Mukta.
	wp_enqueue_style(
		'maglist-child-google-fonts',
		'https://fonts.googleapis.com/css2?family=Mukta:wght@400;500;600;700;800&family=Noto+Serif+Devanagari:wght@500;600;700&family=Poppins:wght@600;700&display=swap',
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

	// Child theme header/topbar/nav/footer - sitewide, since header.php and
	// footer.php replace the parent theme's own header/footer on every page type.
	wp_enqueue_style(
		'maglist-child-site-header',
		MAGLIST_CHILD_URI . '/assets/css/site-header.css',
		array( 'maglist-child-style' ),
		$theme_version
	);

	// Lock header logo to Maglist Customizer sizes on every template (home = category hubs).
	$logo = maglist_child_logo_widths();
	wp_add_inline_style(
		'maglist-child-site-header',
		sprintf(
			'.na-header__logo .custom-logo-link{display:inline-block;line-height:0;}' .
			'.na-header__logo img,.na-header__logo .custom-logo{width:%1$dpx!important;max-width:100%%!important;height:auto!important;max-height:none!important;object-fit:contain!important;}' .
			'@media (max-width:991px){.na-header__logo img,.na-header__logo .custom-logo{width:%2$dpx!important;}}' .
			'@media (max-width:575px){.na-header__logo img,.na-header__logo .custom-logo{width:%3$dpx!important;}}',
			$logo['desktop'],
			$logo['tablet'],
			$logo['mobile']
		)
	);

	wp_enqueue_script(
		'maglist-child-site-header',
		MAGLIST_CHILD_URI . '/assets/js/site-header.js',
		array(),
		$theme_version,
		true
	);

	// Homepage grid layout (only meaningfully used on the homepage,
	// but safe/cheap to load everywhere so it never 404s if reused elsewhere).
	wp_enqueue_style(
		'maglist-child-home-layout',
		MAGLIST_CHILD_URI . '/assets/css/home-layout.css',
		array( 'maglist-child-style' ),
		$theme_version
	);

	if ( is_front_page() || is_page_template( 'template-home.php' ) ) {
		wp_enqueue_script(
			'maglist-child-home-layout',
			MAGLIST_CHILD_URI . '/assets/js/home-layout.js',
			array(),
			$theme_version,
			true
		);

		// Let sites change how many columns the "Home Sidebar Row" widgets lay
		// out in via the `maglist_child_sidebar_row_columns` filter, without
		// having to edit any CSS file.
		wp_add_inline_style(
			'maglist-child-home-layout',
			sprintf(
				':root{--na-widget-row-columns:%d;}',
				absint( maglist_child_sidebar_row_columns() )
			)
		);
	}

	// Category archive layout — load CSS sitewide (same pattern as home-layout)
	// so concatenators/page caches always include it; JS only where needed.
	wp_enqueue_style(
		'maglist-child-category-archive',
		MAGLIST_CHILD_URI . '/assets/css/category-archive.css',
		array( 'maglist-child-home-layout' ),
		$theme_version
	);

	if ( maglist_child_is_category_layout() ) {
		wp_enqueue_script(
			'maglist-child-category-archive',
			MAGLIST_CHILD_URI . '/assets/js/category-archive.js',
			array(),
			$theme_version,
			true
		);
	}

	// Ratopati-style single post layout.
	wp_enqueue_style(
		'maglist-child-single-post',
		MAGLIST_CHILD_URI . '/assets/css/single-post.css',
		array( 'maglist-child-category-archive' ),
		$theme_version
	);

	// Ratopati-style static pages (about / contact / privacy / terms).
	wp_enqueue_style(
		'maglist-child-static-page',
		MAGLIST_CHILD_URI . '/assets/css/static-page.css',
		array( 'maglist-child-style' ),
		$theme_version
	);

	if ( is_singular( 'post' ) ) {
		wp_enqueue_script(
			'maglist-child-single-post',
			MAGLIST_CHILD_URI . '/assets/js/single-post.js',
			array(),
			$theme_version,
			true
		);

		$post_id = (int) get_queried_object_id();
		wp_localize_script(
			'maglist-child-single-post',
			'naSinglePost',
			array(
				'restUrl'     => esc_url_raw( rest_url( 'maglist-child/v1' ) ),
				'restNonce'   => wp_create_nonce( 'wp_rest' ),
				'postId'      => $post_id,
				'counts'      => maglist_child_get_reaction_counts( $post_id ),
				'selected'    => (string) get_transient( maglist_child_reaction_vote_transient_key( $post_id ) ),
				'shareCounts' => maglist_child_get_share_counts( $post_id ),
				'i18n'        => array(
					'error' => __( 'प्रतिक्रिया बचत हुन सकेन।', 'maglist-child' ),
				),
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'maglist_child_enqueue_assets', 20 );

/**
 * Show 20 posts per page on category, tag, and author archives (dense news feed).
 *
 * @param WP_Query $query Main query.
 */
function maglist_child_category_posts_per_page( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( ! $query->is_category() && ! $query->is_tag() && ! $query->is_author() ) {
		return;
	}
	$query->set( 'posts_per_page', 20 );
}
add_action( 'pre_get_posts', 'maglist_child_category_posts_per_page' );

/**
 * Make the custom logo's HTML width/height/sizes match the Customizer display
 * width so home and hub pages (e.g. /समाचार/) cannot diverge via the native
 * 600px attachment attributes or `sizes="…, 600px"`.
 *
 * @param array $attr Image attributes.
 * @return array
 */
function maglist_child_custom_logo_image_attributes( $attr ) {
	$logo   = maglist_child_logo_widths();
	$width  = $logo['desktop'];
	$height = 0;

	$custom_logo_id = (int) get_theme_mod( 'custom_logo' );
	if ( $custom_logo_id ) {
		$meta = wp_get_attachment_metadata( $custom_logo_id );
		if ( ! empty( $meta['width'] ) && ! empty( $meta['height'] ) && (int) $meta['width'] > 0 ) {
			$height = (int) round( $width * ( (int) $meta['height'] / (int) $meta['width'] ) );
		}
	}
	if ( $height < 1 ) {
		$height = (int) round( $width * ( 136 / 600 ) );
	}

	$attr['width']  = (string) $width;
	$attr['height'] = (string) $height;
	$attr['sizes']  = sprintf(
		'(max-width: 575px) %1$dpx, (max-width: 991px) %2$dpx, %3$dpx',
		$logo['mobile'],
		$logo['tablet'],
		$logo['desktop']
	);

	return $attr;
}
add_filter( 'get_custom_logo_image_attributes', 'maglist_child_custom_logo_image_attributes' );

/**
 * Register child theme footer menu locations.
 * Assign menus under Appearance > Menus. Parent Maglist still provides
 * `social-menu-footer` for the follow column.
 */
function maglist_child_register_nav_menus() {
	register_nav_menus(
		array(
			'footer'            => esc_html__( 'Footer Useful Links (उपयोगी लिंक)', 'maglist-child' ),
			'footer-categories' => esc_html__( 'Footer Categories (समाचार विधा)', 'maglist-child' ),
		)
	);
}
add_action( 'after_setup_theme', 'maglist_child_register_nav_menus' );

/**
 * Register the drag-and-drop homepage widget areas.
 * These show up under Appearance > Widgets and can hold any standard widget,
 * Custom HTML block, or third-party ad-code widget.
 */
function maglist_child_register_sidebars() {
	$areas = array(
		'footer-about'        => array(
			'name'        => esc_html__( 'Footer About / Contact (unused)', 'maglist-child' ),
			'description' => esc_html__( 'Deprecated — about/contact copy is hardcoded in footer.php. Safe to leave empty.', 'maglist-child' ),
		),
		'home-above-header'   => array(
			'name'        => esc_html__( 'Above Header Leaderboard Ad', 'maglist-child' ),
			'description' => esc_html__( 'Sitewide leaderboard banner shown above the top bar on every page (728×90 / 970×90).', 'maglist-child' ),
		),
		'home-top-banner'     => array(
			'name'        => esc_html__( 'Home Top Banner Ad', 'maglist-child' ),
			'description' => esc_html__( 'Full-width banner/ad slot shown right below the header, above the hero section.', 'maglist-child' ),
		),
		'home-breaking-ad-1'  => array(
			'name'        => esc_html__( 'Home Breaking Ad Slot 1', 'maglist-child' ),
			'description' => esc_html__( 'Ad slot shown after the first breaking story on the homepage.', 'maglist-child' ),
		),
		'home-breaking-ad-2'  => array(
			'name'        => esc_html__( 'Home Breaking Ad Slot 2', 'maglist-child' ),
			'description' => esc_html__( 'Ad slot shown after the second breaking story on the homepage.', 'maglist-child' ),
		),
		'home-breaking-ad-3'  => array(
			'name'        => esc_html__( 'Home Breaking Ad Slot 3', 'maglist-child' ),
			'description' => esc_html__( 'Ad slot shown after the third breaking story on the homepage.', 'maglist-child' ),
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
		'home-sidebar-ad-1'   => array(
			'name'        => esc_html__( 'Home Sidebar Ad 1 (समाचार)', 'maglist-child' ),
			'description' => esc_html__( 'Sticky sidebar beside the समाचार section on the homepage.', 'maglist-child' ),
		),
		'home-sidebar-ad-2'   => array(
			'name'        => esc_html__( 'Home Sidebar Ad 2 (राजनिती)', 'maglist-child' ),
			'description' => esc_html__( 'Sticky sidebar beside the राजनिती section on the homepage.', 'maglist-child' ),
		),
		'home-sidebar-ad-3'   => array(
			'name'        => esc_html__( 'Home Sidebar Ad 3 (समाज)', 'maglist-child' ),
			'description' => esc_html__( 'Sticky sidebar beside the समाज section on the homepage.', 'maglist-child' ),
		),
		'home-sidebar-ad-4'   => array(
			'name'        => esc_html__( 'Home Sidebar Ad 4 (शिक्षा / साहित्य)', 'maglist-child' ),
			'description' => esc_html__( 'Sticky sidebar beside the शिक्षा / साहित्य section on the homepage.', 'maglist-child' ),
		),
		'home-sidebar-ad-5'   => array(
			'name'        => esc_html__( 'Home Sidebar Ad 5 (व्यवसाय)', 'maglist-child' ),
			'description' => esc_html__( 'Sticky sidebar beside the व्यवसाय section on the homepage.', 'maglist-child' ),
		),
		'home-sidebar-ad-6'   => array(
			'name'        => esc_html__( 'Home Sidebar Ad 6 (स्थानीय)', 'maglist-child' ),
			'description' => esc_html__( 'Sticky sidebar beside the स्थानीय तह/विकास section on the homepage.', 'maglist-child' ),
		),
		'sidebar-ad'          => array(
			'name'        => esc_html__( 'Sidebar Ad', 'maglist-child' ),
			'description' => esc_html__( 'Ad slot above the main Sidebar widgets on singles, category, tag, and author archives.', 'maglist-child' ),
		),
	);

	foreach ( $areas as $id => $area ) {
		register_sidebar(
			array(
				'name'          => $area['name'],
				'id'            => $id,
				'description'   => $area['description'],
				'before_widget' => '<div id="%1$s" class="widget na-widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title na-widget-title">',
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
