<?php
/**
 * Template Part: Hero Section
 *
 * Large featured post + 4 supporting stories in a 2x2 grid.
 * Uses sticky posts first, then falls back to latest posts.
 *
 * @package Nispaksha_Child
 */

$featured = nispaksha_get_featured_posts( 5 );

if ( ! $featured->have_posts() ) {
    return;
}

$posts = $featured->posts;
$main_post = $posts[0];
$side_posts = array_slice( $posts, 1, 4 );
?>

<section class="nispaksha-hero" id="hero-section">
    <div class="nispaksha-container">
        <div class="nispaksha-hero__grid">

            <?php // Main featured post ?>
            <div class="nispaksha-hero__main">
                <a href="<?php echo esc_url( get_permalink( $main_post ) ); ?>">
                    <?php
                    $main_thumb = nispaksha_get_thumb_url( $main_post->ID, 'nispaksha-hero' );
                    ?>
                    <img class="nispaksha-hero__main-img"
                         src="<?php echo esc_url( $main_thumb ); ?>"
                         alt="<?php echo esc_attr( get_the_title( $main_post ) ); ?>"
                         loading="eager" />
                </a>
                <div class="nispaksha-hero__main-overlay">
                    <?php
                    $main_cat = nispaksha_get_primary_category( $main_post->ID );
                    if ( $main_cat ) :
                    ?>
                        <span class="nispaksha-hero__main-cat">
                            <?php echo esc_html( $main_cat->name ); ?>
                        </span>
                    <?php endif; ?>
                    <h2 class="nispaksha-hero__main-title">
                        <a href="<?php echo esc_url( get_permalink( $main_post ) ); ?>">
                            <?php echo esc_html( get_the_title( $main_post ) ); ?>
                        </a>
                    </h2>
                    <div class="nispaksha-hero__main-meta">
                        <i class="far fa-clock"></i>
                        <?php echo esc_html( nispaksha_time_ago( $main_post->ID ) ); ?>
                    </div>
                </div>
            </div>

            <?php // Side grid — 4 posts ?>
            <div class="nispaksha-hero__side">
                <?php foreach ( $side_posts as $side_post ) :
                    $side_thumb = nispaksha_get_thumb_url( $side_post->ID, 'nispaksha-card' );
                    $side_cat = nispaksha_get_primary_category( $side_post->ID );
                ?>
                    <div class="nispaksha-hero__side-item">
                        <a href="<?php echo esc_url( get_permalink( $side_post ) ); ?>">
                            <img src="<?php echo esc_url( $side_thumb ); ?>"
                                 alt="<?php echo esc_attr( get_the_title( $side_post ) ); ?>"
                                 loading="eager" />
                        </a>
                        <div class="nispaksha-hero__side-overlay">
                            <?php if ( $side_cat ) : ?>
                                <span class="nispaksha-hero__side-cat">
                                    <?php echo esc_html( $side_cat->name ); ?>
                                </span>
                            <?php endif; ?>
                            <h3 class="nispaksha-hero__side-title">
                                <a href="<?php echo esc_url( get_permalink( $side_post ) ); ?>">
                                    <?php echo esc_html( get_the_title( $side_post ) ); ?>
                                </a>
                            </h3>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    </div>
</section>

<?php wp_reset_postdata(); ?>
