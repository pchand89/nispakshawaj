<?php
/**
 * Custom Header Template
 *
 * Overrides the parent Maglist theme header with a modern design
 * featuring Nepali flag colors.
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

<?php // ===== TOP BAR ===== ?>
<div class="nispaksha-topbar" id="topbar">
    <div class="nispaksha-container">
        <div class="nispaksha-topbar__date">
            <?php
            // Display current Nepali-style date
            echo esc_html( date_i18n( 'l, j F Y' ) );
            ?>
        </div>
        <div class="nispaksha-topbar__actions">
            <div class="nispaksha-topbar__social">
                <?php $fb = get_theme_mod( 'nispaksha_facebook', 'https://www.facebook.com/nispakshawaj' ); ?>
                <?php if ( $fb ) : ?>
                    <a href="<?php echo esc_url( $fb ); ?>" target="_blank" rel="noopener" aria-label="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                <?php endif; ?>

                <?php $tw = get_theme_mod( 'nispaksha_twitter', '' ); ?>
                <?php if ( $tw ) : ?>
                    <a href="<?php echo esc_url( $tw ); ?>" target="_blank" rel="noopener" aria-label="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                <?php endif; ?>

                <?php $yt = get_theme_mod( 'nispaksha_youtube', '' ); ?>
                <?php if ( $yt ) : ?>
                    <a href="<?php echo esc_url( $yt ); ?>" target="_blank" rel="noopener" aria-label="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                <?php endif; ?>
            </div>

            <button class="nispaksha-dark-toggle" id="dark-mode-toggle" aria-label="Toggle dark mode">
                <span class="nispaksha-dark-toggle__icon">🌙</span>
                <span class="nispaksha-dark-toggle__text">डार्क मोड</span>
            </button>
        </div>
    </div>
</div>

<?php // ===== MAIN HEADER ===== ?>
<header class="nispaksha-header" id="masthead" role="banner">
    <div class="nispaksha-container">
        <div class="nispaksha-header__logo">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php bloginfo( 'name' ); ?>">
                <?php if ( has_custom_logo() ) : ?>
                    <?php
                    $custom_logo_id = get_theme_mod( 'custom_logo' );
                    $logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
                    ?>
                    <img src="<?php echo esc_url( $logo_url ); ?>"
                         alt="<?php bloginfo( 'name' ); ?>"
                         class="nispaksha-logo-img" />
                <?php else : ?>
                    <img src="https://www.nispakshawaj.com/wp-content/uploads/2024/06/LogoNewTextBorder-1.png"
                         alt="<?php bloginfo( 'name' ); ?>"
                         class="nispaksha-logo-img" />
                <?php endif; ?>
            </a>
        </div>

        <div class="nispaksha-header__search">
            <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <input type="search"
                       placeholder="खोज्नुहोस्..."
                       value="<?php echo get_search_query(); ?>"
                       name="s"
                       id="header-search"
                       aria-label="खोज्नुहोस्" />
                <button type="submit" aria-label="Search">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </div>
</header>

<?php // ===== NAVIGATION ===== ?>
<nav class="nispaksha-nav" id="site-navigation" role="navigation" aria-label="Primary Navigation">
    <div class="nispaksha-container">
        <div class="nispaksha-nav__sticky-logo">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                <?php if ( has_custom_logo() ) : ?>
                    <?php
                    $custom_logo_id = get_theme_mod( 'custom_logo' );
                    $logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
                    ?>
                    <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php bloginfo( 'name' ); ?>" />
                <?php else : ?>
                    <img src="https://www.nispakshawaj.com/wp-content/uploads/2024/06/LogoNewTextBorder-1.png"
                         alt="<?php bloginfo( 'name' ); ?>" />
                <?php endif; ?>
            </a>
        </div>

        <?php
        if ( has_nav_menu( 'nispaksha-primary' ) ) {
            wp_nav_menu( array(
                'theme_location' => 'nispaksha-primary',
                'menu_class'     => 'nispaksha-nav__menu',
                'container'      => false,
                'depth'          => 2,
                'fallback_cb'    => false,
            ) );
        } else {
            // Fallback: display categories as menu
            $categories = get_categories( array(
                'orderby'    => 'count',
                'order'      => 'DESC',
                'number'     => 10,
                'hide_empty' => true,
            ) );
            if ( ! empty( $categories ) ) :
            ?>
                <ul class="nispaksha-nav__menu">
                    <li class="<?php echo is_front_page() ? 'current-menu-item' : ''; ?>">
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                            <i class="fas fa-home"></i> होमपेज
                        </a>
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
            endif;
        }
        ?>

        <button class="nispaksha-nav__hamburger" id="mobile-menu-toggle" aria-label="Open Menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</nav>

<?php // ===== MOBILE MENU OVERLAY ===== ?>
<div class="nispaksha-mobile-backdrop" id="mobile-backdrop"></div>
<div class="nispaksha-mobile-menu" id="mobile-menu">
    <div class="nispaksha-mobile-menu__header">
        <?php if ( has_custom_logo() ) : ?>
            <?php
            $custom_logo_id = get_theme_mod( 'custom_logo' );
            $logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
            ?>
            <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php bloginfo( 'name' ); ?>" />
        <?php else : ?>
            <img src="https://www.nispakshawaj.com/wp-content/uploads/2024/06/LogoNewTextBorder-1.png"
                 alt="<?php bloginfo( 'name' ); ?>" />
        <?php endif; ?>
        <button class="nispaksha-mobile-menu__close" id="mobile-menu-close" aria-label="Close Menu">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <ul class="nispaksha-mobile-menu__nav">
        <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><i class="fas fa-home"></i> होमपेज</a></li>
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
