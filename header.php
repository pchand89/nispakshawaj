<?php
/**
 * Header Template — Pixel-Perfect Ratopati Style
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

<?php // ===== RATOPATI TOPBAR ===== ?>
<div class="rp-topbar">
    <div class="rp-container">
        <div class="rp-topbar__date">
            <i class="far fa-calendar-alt"></i>
            <?php echo esc_html( date_i18n( 'l, j F Y' ) ); ?>
        </div>
        <div class="rp-topbar__links">
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

<?php // ===== RATOPATI CENTERED HEADER ===== ?>
<header class="rp-header" role="banner">
    <div class="rp-container">
        <div class="rp-header__left">
            <button class="rp-hamburger" id="mobile-menu-toggle" aria-label="Toggle Navigation">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>

        <div class="rp-header__logo">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php bloginfo( 'name' ); ?>">
                <img src="https://www.nispakshawaj.com/wp-content/uploads/2024/06/LogoNewTextBorder-1.png"
                     alt="<?php bloginfo( 'name' ); ?>" />
            </a>
        </div>

        <div class="rp-header__right">
            <button class="rp-search-btn" id="search-toggle" aria-label="Toggle Search" aria-expanded="false" aria-controls="rp-search-overlay">
                <i class="fas fa-search"></i>
            </button>
            <button class="rp-theme-btn" id="dark-mode-toggle" aria-label="Toggle Dark Mode">
                <span id="dark-mode-icon">🌙</span> <span id="dark-mode-text">डार्क</span>
            </button>
        </div>
    </div>
</header>

<?php // ===== SEARCH OVERLAY ===== ?>
<div class="rp-search-overlay" id="rp-search-overlay">
    <div class="rp-search-overlay__box">
        <button class="rp-search-overlay__close" id="search-close" aria-label="Close Search">
            <i class="fas fa-times"></i>
        </button>
        <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
            <input type="search" name="s" placeholder="समाचार खोज्नुहोस्..." value="<?php echo esc_attr( get_search_query() ); ?>" required />
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>
</div>

<?php // ===== RATOPATI PRIMARY NAV BAR ===== ?>
<div class="rp-nav-backdrop" id="nav-backdrop"></div>
<nav class="rp-nav" id="site-navigation" role="navigation" aria-label="Primary Navigation">
    <div class="rp-container">
        <?php
        if ( has_nav_menu( 'nispaksha-primary' ) ) {
            wp_nav_menu( array(
                'theme_location' => 'nispaksha-primary',
                'menu_class'     => 'rp-nav__menu',
                'container'      => false,
                'depth'          => 2,
                'fallback_cb'    => false,
            ) );
        } else {
            $categories = get_categories( array(
                'orderby'    => 'count',
                'order'      => 'DESC',
                'number'     => 12,
                'hide_empty' => true,
            ) );
            ?>
            <ul class="rp-nav__menu">
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
