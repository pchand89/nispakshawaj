<?php
/**
 * Template Part: Lead Story Stack — Ratopati Style
 *
 * Renders Ratopati's iconic Stack of 3 top lead headlines with big titles & images.
 *
 * @package Nispaksha_Child
 */

$featured = nispaksha_get_featured_posts( 3 );

if ( ! $featured->have_posts() ) {
    return;
}
?>

<section class="rp-lead-stack" id="hero-section">
    <div class="rp-container">
        <?php while ( $featured->have_posts() ) : $featured->the_post();
            $main_cat = nispaksha_get_primary_category( get_the_ID() );
            $main_thumb = nispaksha_get_thumb_url( get_the_ID(), 'nispaksha-hero' );
        ?>
            <div class="rp-lead-item">
                <?php if ( $main_cat ) : ?>
                    <span class="rp-lead-item__badge">
                        <?php echo esc_html( $main_cat->name ); ?>
                    </span>
                <?php endif; ?>

                <h1 class="rp-lead-item__title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h1>

                <?php if ( has_excerpt() || get_the_content() ) : ?>
                    <h3 class="rp-lead-item__sub">
                        <?php echo esc_html( wp_trim_words( get_the_excerpt(), 25 ) ); ?>
                    </h3>
                <?php endif; ?>

                <?php if ( ! empty( $main_thumb ) ) : ?>
                    <div class="rp-lead-item__image">
                        <a href="<?php the_permalink(); ?>">
                            <img src="<?php echo esc_url( $main_thumb ); ?>" alt="<?php the_title_attribute(); ?>" loading="eager" />
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>
</section>
