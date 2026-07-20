<?php
/**
 * Template Part: Sidebar Trending — Ratopati Style
 *
 * Numbered trending news list (1-10) with red circular number badges,
 * and latest news feed.
 *
 * @package Nispaksha_Child
 */

$trending = nispaksha_get_trending_posts( 8 );
$latest   = new WP_Query( array(
    'posts_per_page' => 6,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
    'no_found_rows'  => true,
) );
?>

<div class="ratopati-sidebar" id="sidebar">

    <?php // ===== TRENDING WIDGET (ट्रेन्डिङ) ===== ?>
    <?php if ( $trending->have_posts() ) : ?>
    <div class="ratopati-widget" id="sidebar-trending">
        <div class="ratopati-widget__header">
            <i class="fas fa-fire"></i> ट्रेन्डिङ
        </div>
        <div class="ratopati-widget__body">
            <div class="ratopati-trending-list">
                <?php
                $counter = 1;
                while ( $trending->have_posts() ) : $trending->the_post();
                ?>
                    <div class="ratopati-trending-item">
                        <span class="ratopati-trending-item__num"><?php echo esc_html( $counter ); ?></span>
                        <div class="ratopati-trending-item__content">
                            <h4 class="ratopati-trending-item__title">
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
    </div>
    <?php endif; ?>

    <?php // ===== LATEST NEWS WIDGET (ताजा अपडेट) ===== ?>
    <?php if ( $latest->have_posts() ) : ?>
    <div class="ratopati-widget" id="sidebar-latest">
        <div class="ratopati-widget__header">
            <i class="far fa-newspaper"></i> ताजा अपडेट
        </div>
        <div class="ratopati-widget__body">
            <?php while ( $latest->have_posts() ) : $latest->the_post(); ?>
                <div class="ratopati-card ratopati-card--horizontal" style="margin-bottom: 10px;">
                    <div class="ratopati-card__thumb" style="width: 80px; min-width: 80px;">
                        <a href="<?php the_permalink(); ?>">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <?php the_post_thumbnail( 'nispaksha-thumb' ); ?>
                            <?php else : ?>
                                <img src="<?php echo esc_url( nispaksha_get_thumb_url() ); ?>" alt="<?php the_title(); ?>" />
                            <?php endif; ?>
                        </a>
                    </div>
                    <div class="ratopati-card__body" style="padding: 6px 10px;">
                        <h4 class="ratopati-card__title" style="font-size: 13px;">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h4>
                        <div class="ratopati-card__time" style="font-size: 11px;">
                            <?php echo esc_html( nispaksha_time_ago() ); ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
    <?php endif; ?>

</div>
