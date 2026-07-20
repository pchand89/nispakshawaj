<?php
/**
 * Nispaksha Awaj Child Theme — functions.php
 *
 * Theme setup, enqueues, helper functions for the custom homepage.
 *
 * @package Nispaksha_Child
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * ============================================
 * 1. ENQUEUE STYLES & SCRIPTS
 * ============================================
 */
add_action( 'wp_enqueue_scripts', 'nispaksha_child_enqueue', 20 );
function nispaksha_child_enqueue() {
    // Parent theme stylesheet
    wp_enqueue_style(
        'maglist-parent-style',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme( 'maglist' )->get( 'Version' )
    );

    // Child theme stylesheet (loads after parent)
    wp_enqueue_style(
        'nispaksha-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( 'maglist-parent-style' ),
        filemtime( get_stylesheet_directory() . '/style.css' )
    );

    // Google Fonts — Mukta (Nepali) + Inter (English)
    wp_enqueue_style(
        'nispaksha-google-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Mukta:wght@400;500;600;700;800&display=swap',
        array(),
        null
    );

    // Font Awesome for icons
    wp_enqueue_style(
        'nispaksha-fontawesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
        array(),
        '6.5.1'
    );

    // Custom JS
    wp_enqueue_script(
        'nispaksha-custom-js',
        get_stylesheet_directory_uri() . '/js/custom.js',
        array(),
        filemtime( get_stylesheet_directory() . '/js/custom.js' ),
        true
    );
}

/**
 * ============================================
 * 2. THEME SETUP
 * ============================================
 */
add_action( 'after_setup_theme', 'nispaksha_child_setup' );
function nispaksha_child_setup() {
    // Custom logo support
    add_theme_support( 'custom-logo', array(
        'height'      => 80,
        'width'       => 300,
        'flex-height' => true,
        'flex-width'  => true,
    ) );

    // Post thumbnails
    add_theme_support( 'post-thumbnails' );

    // Custom image sizes for news cards
    add_image_size( 'nispaksha-hero', 800, 500, true );
    add_image_size( 'nispaksha-card', 400, 250, true );
    add_image_size( 'nispaksha-thumb', 150, 150, true );
    add_image_size( 'nispaksha-horizontal', 280, 180, true );

    // Register navigation menus
    register_nav_menus( array(
        'nispaksha-primary'  => __( 'Primary Navigation', 'nispaksha-child' ),
        'nispaksha-mobile'   => __( 'Mobile Navigation', 'nispaksha-child' ),
        'nispaksha-footer'   => __( 'Footer Navigation', 'nispaksha-child' ),
    ) );

    // Title tag
    add_theme_support( 'title-tag' );

    // HTML5
    add_theme_support( 'html5', array(
        'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
    ) );
}

/**
 * ============================================
 * 3. REGISTER WIDGET AREAS
 * ============================================
 */
add_action( 'widgets_init', 'nispaksha_child_widgets' );
function nispaksha_child_widgets() {
    register_sidebar( array(
        'name'          => __( 'Homepage Sidebar', 'nispaksha-child' ),
        'id'            => 'nispaksha-home-sidebar',
        'description'   => __( 'Sidebar widgets for the homepage.', 'nispaksha-child' ),
        'before_widget' => '<div id="%1$s" class="nispaksha-sidebar-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<div class="nispaksha-sidebar-widget__header">',
        'after_title'   => '</div>',
    ) );

    register_sidebar( array(
        'name'          => __( 'Footer Column 1', 'nispaksha-child' ),
        'id'            => 'nispaksha-footer-1',
        'before_widget' => '<div id="%1$s" class="nispaksha-footer__widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="nispaksha-footer__title">',
        'after_title'   => '</h3>',
    ) );
}

/**
 * ============================================
 * 4. HELPER FUNCTIONS
 * ============================================
 */

/**
 * Get posts from a specific category
 *
 * @param string $cat_slug Category slug
 * @param int    $count    Number of posts
 * @param array  $exclude  Post IDs to exclude
 * @return WP_Query
 */
function nispaksha_get_category_posts( $cat_slug, $count = 6, $exclude = array() ) {
    $args = array(
        'category_name'  => $cat_slug,
        'posts_per_page' => $count,
        'post_status'    => 'publish',
        'no_found_rows'  => true,
        'post__not_in'   => $exclude,
    );
    return new WP_Query( $args );
}

/**
 * Get breaking news posts (latest posts auto-populated)
 *
 * @param int $count Number of posts
 * @return WP_Query
 */
function nispaksha_get_breaking_news( $count = 10 ) {
    $args = array(
        'posts_per_page' => $count,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,
    );
    return new WP_Query( $args );
}

/**
 * Get trending posts (by comment count)
 *
 * @param int $count Number of posts
 * @return WP_Query
 */
function nispaksha_get_trending_posts( $count = 8 ) {
    $args = array(
        'posts_per_page' => $count,
        'post_status'    => 'publish',
        'orderby'        => 'comment_count',
        'order'          => 'DESC',
        'date_query'     => array(
            array(
                'after' => '7 days ago',
            ),
        ),
        'no_found_rows'  => true,
    );

    $query = new WP_Query( $args );

    // Fallback: if not enough trending posts, get recent posts
    if ( $query->post_count < 3 ) {
        $args['orderby']    = 'date';
        $args['date_query'] = array();
        $query = new WP_Query( $args );
    }

    return $query;
}

/**
 * Get featured posts for the hero section
 * Uses sticky posts first, then falls back to latest
 *
 * @param int $count Number of posts
 * @return WP_Query
 */
function nispaksha_get_featured_posts( $count = 5 ) {
    $sticky = get_option( 'sticky_posts' );

    if ( ! empty( $sticky ) ) {
        $args = array(
            'post__in'       => $sticky,
            'posts_per_page' => $count,
            'post_status'    => 'publish',
            'no_found_rows'  => true,
        );
        $query = new WP_Query( $args );

        if ( $query->post_count >= $count ) {
            return $query;
        }
    }

    // Fallback to latest posts
    return new WP_Query( array(
        'posts_per_page' => $count,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,
    ) );
}

/**
 * Display Nepali relative time ago
 *
 * @param int $post_id Post ID (optional, uses current post if not provided)
 * @return string
 */
function nispaksha_time_ago( $post_id = null ) {
    $post_time = get_the_time( 'U', $post_id );
    $current_time = current_time( 'timestamp' );
    $diff = $current_time - $post_time;

    if ( $diff < 60 ) {
        return 'भर्खरै';
    } elseif ( $diff < 3600 ) {
        $mins = floor( $diff / 60 );
        return $mins . ' मिनेट अगाडि';
    } elseif ( $diff < 86400 ) {
        $hours = floor( $diff / 3600 );
        return $hours . ' घण्टा अगाडि';
    } elseif ( $diff < 604800 ) {
        $days = floor( $diff / 86400 );
        return $days . ' दिन अगाडि';
    } else {
        return get_the_date( 'Y-m-d', $post_id );
    }
}

/**
 * Get the first category of a post
 *
 * @param int $post_id Post ID
 * @return object|false Category object or false
 */
function nispaksha_get_primary_category( $post_id = null ) {
    $categories = get_the_category( $post_id );
    if ( ! empty( $categories ) ) {
        // Skip "Uncategorized"
        foreach ( $categories as $cat ) {
            if ( $cat->slug !== 'uncategorized' ) {
                return $cat;
            }
        }
        return $categories[0];
    }
    return false;
}

/**
 * Get the default thumbnail URL if no featured image
 *
 * @return string URL to default thumbnail
 */
function nispaksha_default_thumb() {
    return get_stylesheet_directory_uri() . '/img/default-thumb.png';
}

/**
 * Safely get post thumbnail URL with fallback
 *
 * @param int    $post_id Post ID
 * @param string $size    Image size
 * @return string URL
 */
function nispaksha_get_thumb_url( $post_id = null, $size = 'nispaksha-card' ) {
    if ( has_post_thumbnail( $post_id ) ) {
        return get_the_post_thumbnail_url( $post_id, $size );
    }
    return 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="400" height="250" viewBox="0 0 400 250"%3E%3Crect fill="%23E5E7EB" width="400" height="250"/%3E%3Ctext fill="%239CA3AF" font-family="sans-serif" font-size="16" text-anchor="middle" x="200" y="130"%3Eनिश्पक्ष आवाज%3C/text%3E%3C/svg%3E';
}

/**
 * ============================================
 * 5. CUSTOMIZER OPTIONS
 * ============================================
 */
add_action( 'customize_register', 'nispaksha_customizer_settings' );
function nispaksha_customizer_settings( $wp_customize ) {

    // Nispaksha Section
    $wp_customize->add_section( 'nispaksha_options', array(
        'title'    => __( 'निश्पक्ष आवाज Settings', 'nispaksha-child' ),
        'priority' => 30,
    ) );

    // Breaking news toggle
    $wp_customize->add_setting( 'nispaksha_breaking_news', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ) );
    $wp_customize->add_control( 'nispaksha_breaking_news', array(
        'label'   => __( 'Show Breaking News Ticker', 'nispaksha-child' ),
        'section' => 'nispaksha_options',
        'type'    => 'checkbox',
    ) );

    // Facebook URL
    $wp_customize->add_setting( 'nispaksha_facebook', array(
        'default'           => 'https://www.facebook.com/nispakshawaj',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'nispaksha_facebook', array(
        'label'   => __( 'Facebook URL', 'nispaksha-child' ),
        'section' => 'nispaksha_options',
        'type'    => 'url',
    ) );

    // Twitter URL
    $wp_customize->add_setting( 'nispaksha_twitter', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'nispaksha_twitter', array(
        'label'   => __( 'Twitter/X URL', 'nispaksha-child' ),
        'section' => 'nispaksha_options',
        'type'    => 'url',
    ) );

    // YouTube URL
    $wp_customize->add_setting( 'nispaksha_youtube', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'nispaksha_youtube', array(
        'label'   => __( 'YouTube URL', 'nispaksha-child' ),
        'section' => 'nispaksha_options',
        'type'    => 'url',
    ) );
}
