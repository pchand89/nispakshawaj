<?php
/**
 * Template Part: Hero Lead Banner — Pixel-Perfect Ratopati Style
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

<section class="rp-lead-section" id="hero-section">
    <div class="rp-container">

        <?php // ===== RATOPATI MAIN LEAD BANNER ===== ?>
        <div class="rp-lead-banner">
            <?php
            $main_cat = nispaksha_get_primary_category( $main_post->ID );
            if ( $main_cat ) :
            ?>
                <span class="rp-lead-banner__context">
                    <?php echo esc_html( $main_cat->name ); ?>
                </span>
            <?php endif; ?>

            <h1 class="rp-lead-banner__title">
                <a href="<?php echo esc_url( get_permalink( $main_post ) ); ?>">
                    <?php echo esc_html( get_the_title( $main_post ) ); ?>
                </a>
            </h1>

            <?php if ( has_excerpt( $main_post ) || $main_post->post_content ) : ?>
                <h3 class="rp-lead-banner__sub">
                    <?php echo esc_html( wp_trim_words( get_the_excerpt( $main_post ), 25 ) ); ?>
                </h3>
            <?php endif; ?>

            <?php
            $main_thumb = nispaksha_get_thumb_url( $main_post->ID, 'nispaksha-hero' );
            if ( ! empty( $main_thumb ) ) :
            ?>
                <div class="rp-lead-banner__image">
                    <a href="<?php echo esc_url( get_permalink( $main_post ) ); ?>">
                        <img src="<?php echo esc_url( $main_thumb ); ?>"
                             alt="<?php echo esc_attr( get_the_title( $main_post ) ); ?>"
                             loading="eager" />
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <?php // ===== SECONDARY LEAD STORIES (4 COLUMNS GRID) ===== ?>
        <div class="rp-sublead-grid">
            <?php foreach ( $side_posts as $side_post ) :
                $side_thumb = nispaksha_get_thumb_url( $side_post->ID, 'nispaksha-card' );
            ?>
                <article class="rp-sublead-card">
                    <div class="rp-sublead-card__thumb">
                        <a href="<?php echo esc_url( get_permalink( $side_post ) ); ?>">
                            <img src="<?php echo esc_url( $side_thumb ); ?>"
                                 alt="<?php echo esc_attr( get_the_title( $side_post ) ); ?>"
                                 loading="eager" />
                        </a>
                    </div>
                    <div class="rp-sublead-card__body">
                        <h3 class="rp-sublead-card__title">
                            <a href="<?php echo esc_url( get_permalink( $side_post ) ); ?>">
                                <?php echo esc_html( get_the_title( $side_post ) ); ?>
                            </a>
                        </h3>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<?php wp_reset_postdata(); ?>
