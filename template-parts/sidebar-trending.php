<?php
/**
 * Template Part: Sidebar Trending
 *
 * Displays trending posts in a numbered list and latest updates.
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

<?php // Trending Posts Widget ?>
<?php if ( $trending->have_posts() ) : ?>
<div class="nispaksha-sidebar-widget" id="sidebar-trending">
    <div class="nispaksha-sidebar-widget__header">
        <i class="fas fa-fire"></i> ट्रेन्डिङ
    </div>
    <div class="nispaksha-sidebar-widget__body">
        <div class="nispaksha-trending-list">
            <?php
            $counter = 1;
            while ( $trending->have_posts() ) : $trending->the_post();
            ?>
                <div class="nispaksha-trending-item">
                    <span class="nispaksha-trending-item__number"><?php echo esc_html( $counter ); ?></span>
                    <div class="nispaksha-trending-item__content">
                        <h4 class="nispaksha-trending-item__title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h4>
                        <span class="nispaksha-trending-item__time">
                            <i class="far fa-clock"></i> <?php echo esc_html( nispaksha_time_ago() ); ?>
                        </span>
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

<?php // Latest Updates Widget ?>
<?php if ( $latest->have_posts() ) : ?>
<div class="nispaksha-sidebar-widget" id="sidebar-latest">
    <div class="nispaksha-sidebar-widget__header">
        <i class="far fa-newspaper"></i> ताजा अपडेट
    </div>
    <div class="nispaksha-sidebar-widget__body">
        <?php while ( $latest->have_posts() ) : $latest->the_post(); ?>
            <div class="nispaksha-latest-item">
                <div class="nispaksha-latest-item__thumb">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <a href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail( 'nispaksha-thumb', array( 'loading' => 'lazy' ) ); ?>
                        </a>
                    <?php endif; ?>
                </div>
                <div>
                    <h4 class="nispaksha-latest-item__title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h4>
                    <span class="nispaksha-latest-item__time">
                        <?php echo esc_html( nispaksha_time_ago() ); ?>
                    </span>
                </div>
            </div>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>
</div>
<?php endif; ?>

<?php // Social Follow Widget ?>
<div class="nispaksha-sidebar-widget" id="sidebar-social">
    <div class="nispaksha-sidebar-widget__header">
        <i class="fas fa-share-alt"></i> हामीलाई फलो गर्नुहोस्
    </div>
    <div class="nispaksha-sidebar-widget__body">
        <div class="nispaksha-social-follow">
            <?php $fb = get_theme_mod( 'nispaksha_facebook', 'https://www.facebook.com/nispakshawaj' ); ?>
            <?php if ( $fb ) : ?>
                <a href="<?php echo esc_url( $fb ); ?>" target="_blank" rel="noopener" class="nispaksha-social-follow__btn nispaksha-social-follow__btn--facebook">
                    <i class="fab fa-facebook-f"></i> Facebook मा फलो गर्नुहोस्
                </a>
            <?php endif; ?>

            <?php $tw = get_theme_mod( 'nispaksha_twitter', '' ); ?>
            <?php if ( $tw ) : ?>
                <a href="<?php echo esc_url( $tw ); ?>" target="_blank" rel="noopener" class="nispaksha-social-follow__btn nispaksha-social-follow__btn--twitter">
                    <i class="fab fa-twitter"></i> Twitter मा फलो गर्नुहोस्
                </a>
            <?php endif; ?>

            <?php $yt = get_theme_mod( 'nispaksha_youtube', '' ); ?>
            <?php if ( $yt ) : ?>
                <a href="<?php echo esc_url( $yt ); ?>" target="_blank" rel="noopener" class="nispaksha-social-follow__btn nispaksha-social-follow__btn--youtube">
                    <i class="fab fa-youtube"></i> YouTube मा Subscribe गर्नुहोस्
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>
