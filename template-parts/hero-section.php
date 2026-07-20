<?php
/**
 * Template Part: Hero Lead Section — Ratopati Style
 *
 * Features Ratopati's iconic lead story design:
 * - Huge centered main lead title, category tag, large featured image, and excerpt
 * - 4-column secondary lead news grid directly below
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

<section class="ratopati-hero" id="hero-section">
    <div class="ratopati-container">

        <?php // ===== MAIN LEAD STORY BANNER ===== ?>
        <div class="ratopati-lead-main">
            <?php
            $main_cat = nispaksha_get_primary_category( $main_post->ID );
            if ( $main_cat ) :
            ?>
                <span class="ratopati-lead-main__cat">
                    <?php echo esc_html( $main_cat->name ); ?>
                </span>
            <?php endif; ?>

            <h1 class="ratopati-lead-main__title">
                <a href="<?php echo esc_url( get_permalink( $main_post ) ); ?>">
                    <?php echo esc_html( get_the_title( $main_post ) ); ?>
                </a>
            </h1>

            <div class="ratopati-lead-main__img-wrap">
                <a href="<?php echo esc_url( get_permalink( $main_post ) ); ?>">
                    <img src="<?php echo esc_url( nispaksha_get_thumb_url( $main_post->ID, 'nispaksha-hero' ) ); ?>"
                         alt="<?php echo esc_attr( get_the_title( $main_post ) ); ?>"
                         loading="eager" />
                </a>
            </div>

            <?php if ( has_excerpt( $main_post ) || $main_post->post_content ) : ?>
                <p class="ratopati-lead-main__excerpt">
                    <?php echo esc_html( wp_trim_words( get_the_excerpt( $main_post ), 30 ) ); ?>
                </p>
            <?php endif; ?>
        </div>

        <?php // ===== SECONDARY LEAD STORIES (4 COLUMNS GRID) ===== ?>
        <div class="ratopati-lead-grid">
            <?php foreach ( $side_posts as $side_post ) :
                $side_thumb = nispaksha_get_thumb_url( $side_post->ID, 'nispaksha-card' );
                $side_cat = nispaksha_get_primary_category( $side_post->ID );
            ?>
                <article class="ratopati-lead-card">
                    <div class="ratopati-lead-card__thumb">
                        <a href="<?php echo esc_url( get_permalink( $side_post ) ); ?>">
                            <img src="<?php echo esc_url( $side_thumb ); ?>"
                                 alt="<?php echo esc_attr( get_the_title( $side_post ) ); ?>"
                                 loading="eager" />
                        </a>
                    </div>
                    <div class="ratopati-lead-card__body">
                        <?php if ( $side_cat ) : ?>
                            <span class="ratopati-lead-card__cat">
                                <?php echo esc_html( $side_cat->name ); ?>
                            </span>
                        <?php endif; ?>
                        <h3 class="ratopati-lead-card__title">
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
