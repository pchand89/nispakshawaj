<?php
/**
 * Custom Header Template — Ratopati Style
 *
 * Centered logo header with utility bar, dark mode toggle, search overlay,
 * and primary category navigation bar.
 *
 * @package Nispaksha_Child
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php bloginfo( 'description' ); ?>">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
<a class="skip-link visually-hidden" href="#primary">Skip to content</a>

<?php // ===== RATOPATI TOP UTILITY BAR ===== ?>
<div class="ratopati-top-links">
    <div class="ratopati-container">
        <div class="ratopati-top-links__date">
            <i class="far fa-calendar-alt"></i>
            <?php echo esc_html( date_i18n( 'l, j F Y' ) ); ?>
        </div>
        <div class="ratopati-top-links__items">
            <a href="<?php echo esc_url( home_url( '/category/समाचार/' ) ); ?>">
                <i class="fas fa-bolt"></i> ताजा समाचार
            </a>
            <a href="<?php echo esc_url( home_url( '/category/राजनिती/' ) ); ?>">
                <i class="fas fa-landmark"></i> राजनीति
            </a>
            <a href="<?php echo esc_url( home_url( '/category/खेलकुद/' ) ); ?>">
                <i class="fas fa-trophy"></i> खेलकुद
            </a>
        </div>
    </div>
</div>

<?php // ===== RATOPATI MAIN HEADER ===== ?>
<header class="ratopati-header" role="banner">
    <div class="ratopati-container">
        <div class="ratopati-header__left">
            <button class="ratopati-hamburger" id="mobile-menu-toggle" aria-label="Toggle Navigation">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>

        <div class="ratopati-header__logo">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php bloginfo( 'name' ); ?>">
                <img src="https://www.nispakshawaj.com/wp-content/uploads/2024/06/LogoNewTextBorder-1.png"
                     alt="<?php bloginfo( 'name' ); ?>" />
            </a>
        </div>

        <div class="ratopati-header__right">
            <button class="ratopati-search-trigger" id="search-toggle" aria-label="Toggle Search">
                <i class="fas fa-search"></i>
            </button>
            <button class="ratopati-theme-toggle" id="dark-mode-toggle" aria-label="Toggle Dark Mode">
                <span id="dark-mode-icon">🌙</span> <span id="dark-mode-text">डार्क</span>
            </button>
        </div>
    </div>
</header>

<?php // ===== RATOPATI SEARCH OVERLAY ===== ?>
<div class="ratopati-search-overlay" id="search-overlay">
    <div class="ratopati-container">
        <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="ratopati-search-form">
            <input type="search" placeholder="यहाँ खोज्नुहोस्..." value="<?php echo get_search_query(); ?>" name="s" />
            <button type="submit">खोज्नुहोस्</button>
        </form>
    </div>
</div>

<?php // ===== RATOPATI PRIMARY NAVIGATION BAR ===== ?>
<nav class="ratopati-nav" id="site-navigation" role="navigation" aria-label="Primary Navigation">
    <div class="ratopati-container">
        <?php
        if ( has_nav_menu( 'nispaksha-primary' ) ) {
            wp_nav_menu( array(
                'theme_location' => 'nispaksha-primary',
                'menu_class'     => 'ratopati-nav__menu',
                'container'      => false,
                'depth'          => 2,
                'fallback_cb'    => false,
            ) );
        } else {
            // Fallback Menu
            $categories = get_categories( array(
                'orderby'    => 'count',
                'order'      => 'DESC',
                'number'     => 12,
                'hide_empty' => true,
            ) );
            ?>
            <ul class="ratopati-nav__menu">
                <li class="<?php echo is_front_page() ? 'current-menu-item' : ''; ?>">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><i class="fas fa-home"></i> गृहपृष्ठ</a>
                </li>
                <?php foreach ( $categories as $cat ) :
                    if ( $cat->slug === 'uncategorized' ) continue;
                ?>
                    <li>
                        <a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>">
                            <?php echo esc_html( $cat->name ); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php
        }
        ?>
    </div>
</nav>

<?php // ===== MOBILE DRAWER MENU ===== ?>
<div class="nispaksha-mobile-backdrop" id="mobile-backdrop"></div>
<div class="nispaksha-mobile-menu" id="mobile-menu">
    <div class="nispaksha-mobile-menu__header">
        <img src="https://www.nispakshawaj.com/wp-content/uploads/2024/06/LogoNewTextBorder-1.png" alt="<?php bloginfo( 'name' ); ?>" />
        <button class="nispaksha-mobile-menu__close" id="mobile-menu-close" aria-label="Close Menu">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <ul class="nispaksha-mobile-menu__nav">
        <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><i class="fas fa-home"></i> गृहपृष्ठ</a></li>
        <?php
        $mob_cats = get_categories( array(
            'orderby'    => 'count',
            'order'      => 'DESC',
            'number'     => 15,
            'hide_empty' => true,
        ) );
        foreach ( $mob_cats as $cat ) :
            if ( $cat->slug === 'uncategorized' ) continue;
        ?>
            <li><a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>"><?php echo esc_html( $cat->name ); ?></a></li>
        <?php endforeach; ?>
    </ul>
</div>
