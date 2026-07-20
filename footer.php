<?php
/**
 * Footer Template — Pixel-Perfect Ratopati Style
 *
 * @package Nispaksha_Child
 */
?>

    <footer class="rp-footer" role="contentinfo">
        <div class="rp-container">
            <div class="rp-footer__grid">

                <?php // Column 1: About & Logo ?>
                <div>
                    <img src="https://www.nispakshawaj.com/wp-content/uploads/2024/06/LogoNewTextBorder-1.png"
                         alt="<?php bloginfo( 'name' ); ?>"
                         style="max-height: 50px; margin-bottom: 16px;" />
                    <p style="font-size: 14px; line-height: 1.6; opacity: 0.85;">
                        <?php echo esc_html( get_bloginfo( 'description' ) ); ?>
                    </p>
                </div>

                <?php // Column 2: Quick Links ?>
                <div>
                    <h3 class="rp-footer__title">समाचार विभाग</h3>
                    <ul class="rp-footer__links">
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

                <?php // Column 3: Navigation Links ?>
                <div>
                    <h3 class="rp-footer__title">मुख्य पृष्ठहरू</h3>
                    <ul class="rp-footer__links">
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
                    <h3 class="rp-footer__title">सम्पर्क</h3>
                    <ul class="rp-footer__links">
                        <li style="margin-bottom: 8px;"><i class="fas fa-envelope"></i> info@nispakshawaj.com</li>
                        <li style="margin-bottom: 8px;"><i class="fas fa-globe"></i> www.nispakshawaj.com</li>
                    </ul>
                </div>

            </div>

            <div class="rp-footer__copyright">
                &copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>। सर्वाधिकार सुरक्षित।
            </div>
        </div>
    </footer>

    <button class="rp-back-to-top" id="back-to-top" aria-label="Back to top">
        <i class="fas fa-chevron-up"></i>
    </button>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
