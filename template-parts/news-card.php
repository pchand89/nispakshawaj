<?php
/**
 * Template Part: News Card — Pixel-Perfect Ratopati Style
 *
 * @package Nispaksha_Child
 */

$thumb_url = nispaksha_get_thumb_url( get_the_ID(), 'nispaksha-card' );
?>

<article class="rp-card" id="card-<?php the_ID(); ?>">
    <div class="rp-card__thumb">
        <a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
            <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" />
        </a>
    </div>

    <div class="rp-card__body">
        <h3 class="rp-card__title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>
        <div class="rp-card__time">
            <i class="far fa-clock"></i> <?php echo esc_html( nispaksha_time_ago() ); ?>
        </div>
    </div>
</article>
