<?php
/**
 * Template Part: News Card — Ratopati Style
 *
 * Universal news card component for Ratopati layout grids.
 *
 * @package Nispaksha_Child
 */

$variant = isset( $args['variant'] ) ? $args['variant'] : 'card';
$thumb_size = ( $variant === 'horizontal' ) ? 'nispaksha-horizontal' : 'nispaksha-card';
$card_class = 'ratopati-card';

if ( $variant === 'horizontal' ) {
    $card_class .= ' ratopati-card--horizontal';
}
?>

<article class="<?php echo esc_attr( $card_class ); ?>" id="card-<?php the_ID(); ?>">
    <div class="ratopati-card__thumb">
        <a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
            <?php if ( has_post_thumbnail() ) : ?>
                <?php the_post_thumbnail( $thumb_size, array( 'loading' => 'lazy', 'alt' => get_the_title() ) ); ?>
            <?php else : ?>
                <img src="<?php echo esc_url( nispaksha_get_thumb_url() ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" />
            <?php endif; ?>
        </a>
    </div>

    <div class="ratopati-card__body">
        <h3 class="ratopati-card__title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>
        <div class="ratopati-card__time">
            <i class="far fa-clock"></i> <?php echo esc_html( nispaksha_time_ago() ); ?>
        </div>
    </div>
</article>
