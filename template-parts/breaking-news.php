<?php
/**
 * Template Part: Breaking News Ticker — Ratopati Red Bar
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

<div class="ratopati-ticker" id="breaking-news-ticker">
    <div class="ratopati-ticker__container">
        <span class="ratopati-ticker__badge">
            ब्रेकिङ
        </span>
        <div class="ratopati-ticker__scroll">
            <?php while ( $breaking->have_posts() ) : $breaking->the_post(); ?>
                <span class="ratopati-ticker__item">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </span>
            <?php endwhile; ?>
            <?php
            // Duplicate for smooth seamless looping animation
            $breaking->rewind_posts();
            while ( $breaking->have_posts() ) : $breaking->the_post();
            ?>
                <span class="ratopati-ticker__item">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </span>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php wp_reset_postdata(); ?>
