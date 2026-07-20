<?php
/**
 * Template Part: Breaking News Ticker — Pixel-Perfect Ratopati Style
 *
 * @package Nispaksha_Child
 */

if ( ! get_theme_mod( 'nispaksha_breaking_news', true ) ) {
    return;
}

$breaking = nispaksha_get_breaking_news( 10 );

if ( ! $breaking->have_posts() ) {
    return;
}
?>

<div class="rp-ticker" id="breaking-news-ticker">
    <div class="rp-container">
        <span class="rp-ticker__badge">
            ब्रेकिङ
        </span>
        <div class="rp-ticker__scroll">
            <?php while ( $breaking->have_posts() ) : $breaking->the_post(); ?>
                <span class="rp-ticker__item">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </span>
            <?php endwhile; ?>
            <?php
            $breaking->rewind_posts();
            while ( $breaking->have_posts() ) : $breaking->the_post();
            ?>
                <span class="rp-ticker__item">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </span>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php wp_reset_postdata(); ?>
