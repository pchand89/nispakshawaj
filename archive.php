<?php
/**
 * Archive Template — Category / Tag / Date / Author archives, Ratopati Style
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
                    <h1 class="rp-archive-header__title"><?php echo wp_kses_post( get_the_archive_title() ); ?></h1>
                    <?php if ( get_the_archive_description() ) : ?>
                        <div class="rp-archive-header__desc"><?php echo wp_kses_post( get_the_archive_description() ); ?></div>
                    <?php endif; ?>
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
                        <i class="fas fa-newspaper"></i>
                        <p>यो विभागमा अहिलेसम्म कुनै समाचार प्रकाशित भएको छैन।</p>
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
