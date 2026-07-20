<?php
/**
 * Front Page Template — Ratopati Multi-Colored Section Blocks
 *
 * @package Nispaksha_Child
 */

get_header();

// Collect IDs of posts shown in hero to avoid duplicates, and keep a running
// list (in $GLOBALS['nispaksha_shown_ids'], appended to by category-section.php)
// so the same story never repeats twice further down the homepage —
// this matters especially where two blocks reuse the same category slug
// (e.g. शिक्षा-साहित्य is used for both the "opinion" and "portrait" blocks below).
$hero_featured = nispaksha_get_featured_posts( 3 );
$exclude_ids = wp_list_pluck( $hero_featured->posts, 'ID' );
wp_reset_postdata();
$GLOBALS['nispaksha_shown_ids'] = $exclude_ids;
?>

<main id="primary" class="site-main">

    <?php // 1. Lead News Headline Stack ?>
    <?php get_template_part( 'template-parts/hero-section' ); ?>

    <?php // 2. Red Breaking Ticker Bar ?>
    <?php get_template_part( 'template-parts/breaking-news' ); ?>

    <?php // 3. Top News & Politics Block with Sidebar ?>
    <div class="rp-container">
        <div class="rp-main-layout">
            <div class="rp-content">
                <?php // समाचार (News) — White Block ?>
                <?php get_template_part( 'template-parts/category-section', null, array(
                    'slug'    => 'समाचार',
                    'title'   => 'समाचार',
                    'count'   => 8,
                    'theme'   => 'white',
                    'exclude' => $GLOBALS['nispaksha_shown_ids'],
                ) ); ?>

                <?php // राजनीति (Politics) — White Block ?>
                <?php get_template_part( 'template-parts/category-section', null, array(
                    'slug'    => 'राजनिती',
                    'title'   => 'राजनीति',
                    'count'   => 4,
                    'theme'   => 'white',
                    'exclude' => $GLOBALS['nispaksha_shown_ids'],
                ) ); ?>
            </div>

            <aside class="rp-sidebar-wrap" role="complementary">
                <?php get_template_part( 'template-parts/sidebar-trending' ); ?>
            </aside>
        </div>
    </div>

    <?php // 4. प्रदेश (Province) — DARK NAVY BLUE BLOCK ?>
    <?php get_template_part( 'template-parts/category-section', null, array(
        'slug'    => 'स्थानीय-तह-विकास',
        'title'   => 'प्रदेश समाचार / स्थानीय तह',
        'count'   => 4,
        'theme'   => 'province',
        'exclude' => $GLOBALS['nispaksha_shown_ids'],
    ) ); ?>

    <?php // 5. विचार / ब्लग (Opinion) — WARM LIGHT CREAM BLOCK ?>
    <?php get_template_part( 'template-parts/category-section', null, array(
        'slug'    => 'शिक्षा-साहित्य',
        'title'   => 'विचार / ब्लग',
        'count'   => 4,
        'theme'   => 'opinion',
        'exclude' => $GLOBALS['nispaksha_shown_ids'],
    ) ); ?>

    <?php // 6. मनोरञ्जन (Entertainment) — DARK PURPLE BLOCK ?>
    <?php get_template_part( 'template-parts/category-section', null, array(
        'slug'    => 'मनोरञ्जन',
        'title'   => 'मनोरञ्जन',
        'count'   => 4,
        'theme'   => 'entertainment',
        'exclude' => $GLOBALS['nispaksha_shown_ids'],
    ) ); ?>

    <?php // 7. साहित्य (Literature) — PORTRAIT COVER BLOCK (same category as
    // block #5 above, but excludes those already-shown posts so it surfaces
    // the *next* batch of literature stories instead of repeating them) ?>
    <?php get_template_part( 'template-parts/category-section', null, array(
        'slug'    => 'शिक्षा-साहित्य',
        'title'   => 'साहित्य / कृति',
        'count'   => 4,
        'theme'   => 'white',
        'variant' => 'portrait',
        'exclude' => $GLOBALS['nispaksha_shown_ids'],
    ) ); ?>

    <?php // 8. खुलामञ्च (Khulamanch) — DARK FOREST GREEN BLOCK ?>
    <?php get_template_part( 'template-parts/category-section', null, array(
        'slug'    => 'समाज',
        'title'   => 'खुलामञ्च / समाज',
        'count'   => 4,
        'theme'   => 'green',
        'exclude' => $GLOBALS['nispaksha_shown_ids'],
    ) ); ?>

    <?php // 9. खेलकुद (Sports) — DEEP SPACE BLUE BLOCK ?>
    <?php get_template_part( 'template-parts/category-section', null, array(
        'slug'    => 'खेलकुद',
        'title'   => 'खेलकुद',
        'count'   => 4,
        'theme'   => 'sports',
        'exclude' => $GLOBALS['nispaksha_shown_ids'],
    ) ); ?>

    <?php // 10. रातोपाटी टीभी (TV / Video) — PURE BLACK BLOCK ?>
    <?php get_template_part( 'template-parts/category-section', null, array(
        'slug'    => 'अपराध',
        'title'   => 'टीभी / भिडियो',
        'count'   => 4,
        'theme'   => 'tv',
        'exclude' => $GLOBALS['nispaksha_shown_ids'],
    ) ); ?>

    <?php // 11. अर्थतन्त्र (Economy) — White Block ?>
    <?php get_template_part( 'template-parts/category-section', null, array(
        'slug'    => 'व्यवसाय',
        'title'   => 'अर्थतन्त्र / बिजनेस',
        'count'   => 4,
        'theme'   => 'white',
        'exclude' => $GLOBALS['nispaksha_shown_ids'],
    ) ); ?>

    <?php // 12. विदेश / कूटनीति (International) — White Block ?>
    <?php get_template_part( 'template-parts/category-section', null, array(
        'slug'    => 'विदेश-कूटनीति',
        'title'   => 'विदेश / कूटनीति',
        'count'   => 4,
        'theme'   => 'white',
        'exclude' => $GLOBALS['nispaksha_shown_ids'],
    ) ); ?>

    <?php // 13. विविध (Miscellaneous) — White Block ?>
    <?php get_template_part( 'template-parts/category-section', null, array(
        'slug'    => 'विविध',
        'title'   => 'विविध',
        'count'   => 4,
        'theme'   => 'white',
        'exclude' => $GLOBALS['nispaksha_shown_ids'],
    ) ); ?>

</main>

<?php get_footer(); ?>
