<?php
/**
 * Template Part: Breaking News Ticker
 *
 * Displays an animated horizontal scrolling ticker with latest headlines.
 * Auto-populated from latest posts.
 *
 * @package Nispaksha_Child
 */

// Check if breaking news is enabled in customizer
if ( ! get_theme_mod( 'nispaksha_breaking_news', true ) ) {
    return;
}

$breaking = nispaksha_get_breaking_news( 10 );

if ( ! $breaking->have_posts() ) {
    return;
}
?>

<div class="nispaksha-breaking" id="breaking-news-ticker" role="marquee" aria-label="ब्रेकिङ न्यूज">
    <span class="nispaksha-breaking__label">
        <span class="pulse-dot"></span>
        ब्रेकिङ
    </span>
    <div class="nispaksha-breaking__track">
        <?php while ( $breaking->have_posts() ) : $breaking->the_post(); ?>
            <span class="nispaksha-breaking__item">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </span>
        <?php endwhile; ?>
        <?php
        // Duplicate for seamless loop
        $breaking->rewind_posts();
        while ( $breaking->have_posts() ) : $breaking->the_post();
        ?>
            <span class="nispaksha-breaking__item">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </span>
        <?php endwhile; ?>
    </div>
</div>

<?php wp_reset_postdata(); ?>
