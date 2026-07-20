<?php
/**
 * Template Part: Sidebar Trending — Pixel-Perfect Ratopati Style
 *
 * @package Nispaksha_Child
 */

$trending = nispaksha_get_trending_posts( 8 );
?>

<div class="rp-sidebar" id="sidebar">

    <?php if ( $trending->have_posts() ) : ?>
    <div class="rp-widget" id="sidebar-trending">
        <div class="rp-widget__header">
            <i class="fas fa-fire"></i> ट्रेन्डिङ
        </div>
        <div class="rp-widget__body">
            <?php
            $counter = 1;
            while ( $trending->have_posts() ) : $trending->the_post();
            ?>
                <div class="rp-trending-item">
                    <span class="rp-trending-item__num"><?php echo esc_html( $counter ); ?></span>
                    <div class="rp-trending-item__content">
                        <h4 class="rp-trending-item__title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h4>
                    </div>
                </div>
            <?php
                $counter++;
            endwhile;
            wp_reset_postdata();
            ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if ( is_active_sidebar( 'nispaksha-home-sidebar' ) ) : ?>
        <?php dynamic_sidebar( 'nispaksha-home-sidebar' ); ?>
    <?php endif; ?>

</div>
