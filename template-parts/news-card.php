<?php
/**
 * Template Part: News Card Component
 *
 * @package Nispaksha_Child
 */

$variant   = isset( $args['variant'] ) ? $args['variant'] : 'card';
$thumb_size = ( $variant === 'portrait' || $variant === 'overlay' ) ? 'nispaksha-card' : 'nispaksha-card';
$thumb_url  = nispaksha_get_thumb_url( get_the_ID(), $thumb_size );

$card_class = 'rp-card';
if ( $variant === 'portrait' ) {
    $card_class .= ' rp-card--portrait';
} elseif ( $variant === 'overlay' ) {
    $card_class .= ' rp-card--overlay';
}
?>

<article class="<?php echo esc_attr( $card_class ); ?>" id="card-<?php the_ID(); ?>">
    <div class="rp-card__thumb">
        <a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
            <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" />
        </a>
    </div>

    <div class="rp-card__body">
        <h3 class="rp-card__title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>
        <?php if ( $variant !== 'overlay' ) : ?>
            <div class="rp-card__time">
                <i class="far fa-clock"></i> <?php echo esc_html( nispaksha_time_ago() ); ?>
            </div>
        <?php endif; ?>
    </div>
</article>
