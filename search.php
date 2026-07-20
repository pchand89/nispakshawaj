<?php
/**
 * Search Results Template — Ratopati Style
 *
 * @package Nispaksha_Child
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="rp-container">
        <?php nispaksha_breadcrumbs(); ?>

        <div class="rp-main-layout">
            <div class="rp-content">
                <header class="rp-archive-header">
                    <h1 class="rp-archive-header__title">
                        "<?php echo esc_html( get_search_query() ); ?>" को खोज परिणाम
                    </h1>
                </header>

                <?php if ( have_posts() ) : ?>
                    <div class="rp-grid-4">
                        <?php while ( have_posts() ) : the_post(); ?>
                            <?php get_template_part( 'template-parts/news-card' ); ?>
                        <?php endwhile; ?>
                    </div>

                    <div class="rp-pagination">
                        <?php
                        echo paginate_links( array(
                            'prev_text' => '<i class="fas fa-angle-left"></i> अघिल्लो',
                            'next_text' => 'अर्को <i class="fas fa-angle-right"></i>',
                        ) );
                        ?>
                    </div>
                <?php else : ?>
                    <div class="rp-empty-state">
                        <i class="fas fa-search"></i>
                        <p>तपाईंको खोजसँग मिल्ने कुनै समाचार भेटिएन। फेरि प्रयास गर्नुहोस्।</p>
                        <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="rp-empty-state__search">
                            <input type="search" name="s" placeholder="समाचार खोज्नुहोस्..." value="<?php echo esc_attr( get_search_query() ); ?>" required />
                            <button type="submit"><i class="fas fa-search"></i></button>
                        </form>
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="rp-empty-state__btn">गृहपृष्ठमा फर्कनुहोस्</a>
                    </div>
                <?php endif; ?>
            </div>

            <aside class="rp-sidebar-wrap" role="complementary">
                <?php get_template_part( 'template-parts/sidebar-trending' ); ?>
            </aside>
        </div>
    </div>
</main>

<?php get_footer(); ?>
