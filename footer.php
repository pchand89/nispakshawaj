<?php
/**
 * Custom Footer Template
 *
 * Dark blue footer with category links, about section, social icons, and copyright.
 *
 * @package Nispaksha_Child
 */
?>

    <?php // ===== FOOTER ===== ?>
    <footer class="nispaksha-footer" id="colophon" role="contentinfo">
        <div class="nispaksha-container">
            <div class="nispaksha-footer__grid">

                <?php // Column 1: About ?>
                <div class="nispaksha-footer__col">
                    <?php if ( has_custom_logo() ) : ?>
                        <?php
                        $custom_logo_id = get_theme_mod( 'custom_logo' );
                        $logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
                        ?>
                        <img src="<?php echo esc_url( $logo_url ); ?>"
                             alt="<?php bloginfo( 'name' ); ?>"
                             class="nispaksha-footer__about-logo" />
                    <?php else : ?>
                        <img src="https://www.nispakshawaj.com/wp-content/uploads/2024/06/LogoNewTextBorder-1.png"
                             alt="<?php bloginfo( 'name' ); ?>"
                             class="nispaksha-footer__about-logo" />
                    <?php endif; ?>

                    <p class="nispaksha-footer__about">
                        <?php echo esc_html( get_bloginfo( 'description' ) ); ?>
                    </p>

                    <div class="nispaksha-footer__social">
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
                </div>

                <?php // Column 2: Categories ?>
                <div class="nispaksha-footer__col">
                    <h3 class="nispaksha-footer__title">विभागहरू</h3>
                    <ul class="nispaksha-footer__links">
                        <?php
                        $footer_cats = get_categories( array(
                            'orderby'    => 'count',
                            'order'      => 'DESC',
                            'number'     => 8,
                            'hide_empty' => true,
                        ) );
                        foreach ( $footer_cats as $cat ) :
                            if ( $cat->slug === 'uncategorized' ) continue;
                        ?>
                            <li>
                                <a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>">
                                    <?php echo esc_html( $cat->name ); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <?php // Column 3: Useful Links ?>
                <div class="nispaksha-footer__col">
                    <h3 class="nispaksha-footer__title">उपयोगी लिंकहरू</h3>
                    <ul class="nispaksha-footer__links">
                        <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">होमपेज</a></li>
                        <?php
                        // Show pages in footer
                        $pages = get_pages( array( 'number' => 5, 'sort_order' => 'ASC' ) );
                        foreach ( $pages as $page ) :
                        ?>
                            <li>
                                <a href="<?php echo esc_url( get_permalink( $page->ID ) ); ?>">
                                    <?php echo esc_html( $page->post_title ); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <?php // Column 4: Contact ?>
                <div class="nispaksha-footer__col">
                    <h3 class="nispaksha-footer__title">सम्पर्क</h3>
                    <ul class="nispaksha-footer__links">
                        <li>
                            <a href="mailto:info@nispakshawaj.com">
                                <i class="fas fa-envelope"></i> info@nispakshawaj.com
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                                <i class="fas fa-globe"></i> www.nispakshawaj.com
                            </a>
                        </li>
                    </ul>

                    <?php if ( is_active_sidebar( 'nispaksha-footer-1' ) ) : ?>
                        <?php dynamic_sidebar( 'nispaksha-footer-1' ); ?>
                    <?php endif; ?>
                </div>

            </div>

            <?php // Copyright bar ?>
            <div class="nispaksha-footer__copyright">
                &copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>। सर्वाधिकार सुरक्षित।
            </div>
        </div>
    </footer>

    <?php // Back to top button ?>
    <button class="nispaksha-back-to-top" id="back-to-top" aria-label="Back to top">
        <i class="fas fa-chevron-up"></i>
    </button>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
