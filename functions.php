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
    $category = get_category_by_slug( $cat_slug );
    if ( ! $category ) {
        $category = get_category_by_slug( urlencode( $cat_slug ) );
    }
    if ( ! $category ) {
        $category = get_category_by_slug( sanitize_title( $cat_slug ) );
    }
    if ( ! $category ) {
        $category = get_term_by( 'name', $cat_slug, 'category' );
    }

    if ( $category ) {
        $args = array(
            'cat'            => $category->term_id,
            'posts_per_page' => $count,
            'post_status'    => 'publish',
            'no_found_rows'  => true,
            'post__not_in'   => $exclude,
        );
    } else {
        $args = array(
            'category_name'  => $cat_slug,
            'posts_per_page' => $count,
            'post_status'    => 'publish',
            'no_found_rows'  => true,
            'post__not_in'   => $exclude,
        );
    }
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
    $current_time = current_time( 'U' );
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
 * Get the default thumbnail URL if no featured image.
 *
 * Prefers a local theme asset (so the fallback keeps working even if the
 * production media library changes) and falls back to the known-good
 * production logo URL if that asset hasn't been added yet.
 *
 * @return string URL to default thumbnail
 */
function nispaksha_default_thumb() {
    $local_path = get_stylesheet_directory() . '/img/default-thumb.png';
    if ( file_exists( $local_path ) ) {
        return get_stylesheet_directory_uri() . '/img/default-thumb.png';
    }
    return 'https://www.nispakshawaj.com/wp-content/uploads/2024/06/LogoNewTextBorder-1.png';
}

/**
 * Resolve a display thumbnail URL for a post: featured image ➔ first
 * <img> found in the content ➔ site default thumbnail. Never returns
 * an empty string, so <img src=""> never happens.
 *
 * @param int|null $post_id Post ID (defaults to current post in the loop).
 * @param string   $size    Registered image size to request.
 * @return string
 */
function nispaksha_get_thumb_url( $post_id = null, $size = 'nispaksha-card' ) {
    if ( $post_id && has_post_thumbnail( $post_id ) ) {
        $url = get_the_post_thumbnail_url( $post_id, $size );
        if ( ! $url ) {
            $url = get_the_post_thumbnail_url( $post_id, 'full' );
        }
        if ( ! $url ) {
            $url = get_the_post_thumbnail_url( $post_id, 'medium' );
        }
        if ( $url ) {
            return $url;
        }
    }

    // Extract first image from post content if featured image is not set
    if ( $post_id ) {
        $post = get_post( $post_id );
        if ( $post && ! empty( $post->post_content ) ) {
            if ( preg_match( '/<img.+?src=["\']([^"\']+)["\']/i', $post->post_content, $matches ) ) {
                if ( ! empty( $matches[1] ) ) {
                    return $matches[1];
                }
            }
        }
    }

    return nispaksha_default_thumb();
}

/**
 * Estimate reading time for a post in minutes (Nepali label), based on a
 * ~200 word-per-minute average reading speed.
 *
 * @param int|null $post_id Post ID (defaults to current post in the loop).
 * @return string
 */
function nispaksha_reading_time( $post_id = null ) {
    $post = get_post( $post_id );
    if ( ! $post ) {
        return '';
    }
    $word_count = str_word_count( wp_strip_all_tags( $post->post_content ) );
    $minutes    = max( 1, (int) ceil( $word_count / 200 ) );
    return $minutes . ' मिनेट पढ्ने समय';
}

/**
 * Render a simple Home > Category > Title breadcrumb trail with
 * BreadcrumbList schema markup for SEO.
 */
function nispaksha_breadcrumbs() {
    $items = array(
        array( 'name' => 'गृहपृष्ठ', 'url' => home_url( '/' ) ),
    );

    if ( is_singular( 'post' ) ) {
        $cat = nispaksha_get_primary_category( get_the_ID() );
        if ( $cat ) {
            $items[] = array( 'name' => $cat->name, 'url' => get_category_link( $cat->term_id ) );
        }
        $items[] = array( 'name' => wp_strip_all_tags( get_the_title() ), 'url' => '' );
    } elseif ( is_category() ) {
        $items[] = array( 'name' => single_cat_title( '', false ), 'url' => '' );
    } elseif ( is_search() ) {
        $items[] = array( 'name' => 'खोज परिणाम: ' . get_search_query(), 'url' => '' );
    } elseif ( is_archive() ) {
        $items[] = array( 'name' => wp_strip_all_tags( get_the_archive_title() ), 'url' => '' );
    }

    if ( count( $items ) < 2 ) {
        return;
    }

    $list_items = array();
    echo '<nav class="rp-breadcrumb" aria-label="Breadcrumb"><ol>';
    foreach ( $items as $index => $item ) {
        $position = $index + 1;
        if ( $item['url'] ) {
            echo '<li><a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['name'] ) . '</a></li>';
        } else {
            echo '<li aria-current="page">' . esc_html( $item['name'] ) . '</li>';
        }
        $list_items[] = array(
            '@type'    => 'ListItem',
            'position' => $position,
            'name'     => $item['name'],
        ) + ( $item['url'] ? array( 'item' => $item['url'] ) : array() );
    }
    echo '</ol></nav>';

    $schema = array(
        '@context'        => 'https://schema.org',
        '@type'            => 'BreadcrumbList',
        'itemListElement' => $list_items,
    );
    echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>';
}

/**
 * ============================================
 * 5. SEO: OPEN GRAPH / TWITTER CARDS / JSON-LD
 * ============================================
 * Skips output entirely if a dedicated SEO plugin (Yoast, Rank Math, AIOSEO)
 * is active, since those already provide these tags and duplicates confuse
 * social crawlers.
 */
function nispaksha_has_seo_plugin() {
    return defined( 'WPSEO_VERSION' )
        || class_exists( 'RankMath' )
        || defined( 'AIOSEO_VERSION' );
}

add_action( 'wp_head', 'nispaksha_output_social_meta', 1 );
function nispaksha_output_social_meta() {
    if ( nispaksha_has_seo_plugin() ) {
        return;
    }

    if ( is_singular( 'post' ) ) {
        $title       = get_the_title();
        $description = has_excerpt() ? get_the_excerpt() : wp_trim_words( wp_strip_all_tags( get_the_content() ), 35 );
        $image       = nispaksha_get_thumb_url( get_the_ID(), 'nispaksha-hero' );
        $url         = get_permalink();
        $type        = 'article';
    } else {
        $title       = get_bloginfo( 'name' ) . ' — ' . get_bloginfo( 'description' );
        $description = get_bloginfo( 'description' );
        $image       = nispaksha_default_thumb();
        $url         = home_url( '/' );
        $type        = 'website';
    }
    ?>
    <meta property="og:type" content="<?php echo esc_attr( $type ); ?>">
    <meta property="og:title" content="<?php echo esc_attr( $title ); ?>">
    <meta property="og:description" content="<?php echo esc_attr( wp_strip_all_tags( $description ) ); ?>">
    <meta property="og:url" content="<?php echo esc_url( $url ); ?>">
    <meta property="og:image" content="<?php echo esc_url( $image ); ?>">
    <meta property="og:site_name" content="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo esc_attr( $title ); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr( wp_strip_all_tags( $description ) ); ?>">
    <meta name="twitter:image" content="<?php echo esc_url( $image ); ?>">
    <?php
    if ( is_singular( 'post' ) ) {
        $schema = array(
            '@context'         => 'https://schema.org',
            '@type'            => 'NewsArticle',
            'headline'         => $title,
            'description'      => wp_strip_all_tags( $description ),
            'image'            => array( $image ),
            'datePublished'    => get_the_date( 'c' ),
            'dateModified'     => get_the_modified_date( 'c' ),
            'mainEntityOfPage' => $url,
            'author'           => array(
                '@type' => 'Person',
                'name'  => get_the_author(),
            ),
            'publisher'        => array(
                '@type' => 'Organization',
                'name'  => get_bloginfo( 'name' ),
                'logo'  => array(
                    '@type' => 'ImageObject',
                    'url'   => nispaksha_default_thumb(),
                ),
            ),
        );
        echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>' . "\n";
    }
}

/**
 * ============================================
 * 6. CUSTOMIZER OPTIONS
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
