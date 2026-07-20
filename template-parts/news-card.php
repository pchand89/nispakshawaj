<?php
/**
 * Template Part: News Card
 *
 * Reusable news card component.
 * Usage: get_template_part( 'template-parts/news-card', null, array( 'variant' => 'medium' ) );
 *
 * Variants: 'large', 'medium', 'horizontal', 'list'
 *
 * @package Nispaksha_Child
 */

$variant   = isset( $args['variant'] ) ? $args['variant'] : 'medium';
$show_excerpt = isset( $args['show_excerpt'] ) ? $args['show_excerpt'] : ( $variant === 'large' );
$show_cat    = isset( $args['show_cat'] ) ? $args['show_cat'] : true;
$thumb_size  = 'nispaksha-card';

if ( $variant === 'large' ) {
    $thumb_size = 'nispaksha-hero';
} elseif ( $variant === 'horizontal' ) {
    $thumb_size = 'nispaksha-horizontal';
} elseif ( $variant === 'list' ) {
    $thumb_size = 'nispaksha-thumb';
}

$category = nispaksha_get_primary_category();
$card_class = 'nispaksha-card';
if ( $variant !== 'medium' ) {
    $card_class .= ' nispaksha-card--' . esc_attr( $variant );
}
?>

<article class="<?php echo esc_attr( $card_class ); ?>" id="card-<?php the_ID(); ?>">
    <div class="nispaksha-card__thumb">
        <a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
            <?php if ( has_post_thumbnail() ) : ?>
                <?php the_post_thumbnail( $thumb_size, array( 'loading' => 'lazy', 'alt' => get_the_title() ) ); ?>
            <?php else : ?>
                <img src="<?php echo esc_url( nispaksha_get_thumb_url() ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" />
            <?php endif; ?>
        </a>
        <?php if ( $show_cat && $category && $variant !== 'list' ) : ?>
            <span class="nispaksha-card__cat">
                <a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>">
                    <?php echo esc_html( $category->name ); ?>
                </a>
            </span>
        <?php endif; ?>
    </div>

    <div class="nispaksha-card__body">
        <h3 class="nispaksha-card__title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>

        <?php if ( $show_excerpt ) : ?>
            <p class="nispaksha-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
        <?php endif; ?>

        <div class="nispaksha-card__meta">
            <span class="nispaksha-card__meta-item">
                <i class="far fa-clock"></i>
                <?php echo esc_html( nispaksha_time_ago() ); ?>
            </span>
        </div>
    </div>
</article>
