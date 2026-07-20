<?php
/**
 * Footer Template — Pixel-Perfect Ratopati Style
 *
 * Includes 4-column Slate Top Footer + Signature Red Bottom Footer Bar (#bf1e2e).
 *
 * @package Nispaksha_Child
 */
?>

    <!-- RATOPATI TOP FOOTER (#0f172a) -->
    <footer class="rp-footer-top" role="contentinfo">
        <div class="rp-container">
            <div class="rp-footer-top__grid">

                <?php // Column 1: About ?>
                <div>
                    <img src="https://www.nispakshawaj.com/wp-content/uploads/2024/06/LogoNewTextBorder-1.png"
                         alt="<?php bloginfo( 'name' ); ?>"
                         style="max-height: 48px; margin-bottom: 16px; filter: brightness(0) invert(1);" />
                    <p style="font-size: 14px; line-height: 1.6; opacity: 0.85;">
                        <?php echo esc_html( get_bloginfo( 'description' ) ); ?>
                    </p>
                </div>

                <?php // Column 2: Quick Links ?>
                <div>
                    <h3 class="rp-footer-top__title">समाचार विभाग</h3>
                    <ul>
                        <?php
                        $footer_cats = get_categories( array(
                            'orderby'    => 'count',
                            'order'      => 'DESC',
                            'number'     => 6,
                            'hide_empty' => true,
                        ) );
                        foreach ( $footer_cats as $cat ) :
                            if ( $cat->slug === 'uncategorized' ) continue;
                        ?>
                            <li style="margin-bottom: 8px;">
                                <a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>">
                                    <?php echo esc_html( $cat->name ); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <?php // Column 3: Useful Links ?>
                <div>
                    <h3 class="rp-footer-top__title">मुख्य पृष्ठहरू</h3>
                    <ul>
                        <li style="margin-bottom: 8px;"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">गृहपृष्ठ</a></li>
                        <?php
                        $pages = get_pages( array( 'number' => 5, 'sort_order' => 'ASC' ) );
                        foreach ( $pages as $page ) :
                        ?>
                            <li style="margin-bottom: 8px;">
                                <a href="<?php echo esc_url( get_permalink( $page->ID ) ); ?>">
                                    <?php echo esc_html( $page->post_title ); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <?php // Column 4: Contact ?>
                <div>
                    <h3 class="rp-footer-top__title">सम्पर्क</h3>
                    <ul>
                        <li style="margin-bottom: 8px;"><i class="fas fa-envelope"></i> info@nispakshawaj.com</li>
                        <li style="margin-bottom: 8px;"><i class="fas fa-globe"></i> www.nispakshawaj.com</li>
                    </ul>

                    <?php
                    $social_links = array(
                        'facebook' => array( 'mod' => 'nispaksha_facebook', 'icon' => 'fab fa-facebook-f' ),
                        'twitter'  => array( 'mod' => 'nispaksha_twitter', 'icon' => 'fab fa-x-twitter' ),
                        'youtube'  => array( 'mod' => 'nispaksha_youtube', 'icon' => 'fab fa-youtube' ),
                    );
                    $has_social = false;
                    foreach ( $social_links as $network ) {
                        if ( get_theme_mod( $network['mod'] ) ) {
                            $has_social = true;
                            break;
                        }
                    }
                    ?>
                    <?php if ( $has_social ) : ?>
                        <div class="rp-footer-social">
                            <?php foreach ( $social_links as $network ) :
                                $url = get_theme_mod( $network['mod'] );
                                if ( ! $url ) continue;
                            ?>
                                <a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer">
                                    <i class="<?php echo esc_attr( $network['icon'] ); ?>"></i>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ( is_active_sidebar( 'nispaksha-footer-1' ) ) : ?>
                    <div>
                        <?php dynamic_sidebar( 'nispaksha-footer-1' ); ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </footer>

    <!-- RATOPATI SOLID RED BOTTOM FOOTER BAR (#bf1e2e) -->
    <div class="rp-footer-bottom">
        <div class="rp-container">
            <img src="https://www.nispakshawaj.com/wp-content/uploads/2024/06/LogoNewTextBorder-1.png"
                 alt="<?php bloginfo( 'name' ); ?>" />
            <div class="rp-footer-bottom__text">
                &copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>। सर्वाधिकार सुरक्षित।
            </div>
        </div>
    </div>

    <!-- Back to top button -->
    <button class="rp-back-to-top" id="back-to-top" aria-label="Back to top">
        <i class="fas fa-chevron-up"></i>
    </button>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
