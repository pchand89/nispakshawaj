<?php
/**
 * Front Page Template — Ratopati Design
 *
 * Modeled directly after Ratopati.com homepage layout.
 *
 * @package Nispaksha_Child
 */

get_header();

// Collect IDs of posts shown in hero to avoid duplicates
$hero_featured = nispaksha_get_featured_posts( 5 );
$exclude_ids = wp_list_pluck( $hero_featured->posts, 'ID' );
wp_reset_postdata();
?>

<main id="primary" class="site-main">

    <?php // 1. Red Breaking News Ticker ?>
    <?php get_template_part( 'template-parts/breaking-news' ); ?>

    <?php // 2. Main Lead Story & Hero Section ?>
    <?php get_template_part( 'template-parts/hero-section' ); ?>

    <?php // 3. Main Category Content + Trending Sidebar ?>
    <div class="ratopati-container">
        <div class="ratopati-main-layout">

            <?php // === MAIN CATEGORIES COLUMN === ?>
            <div class="ratopati-content">

                <?php // समाचार (News) — 4 Column Grid ?>
                <?php get_template_part( 'template-parts/category-section', null, array(
                    'slug'    => 'समाचार',
                    'title'   => 'समाचार',
                    'layout'  => 'grid-4',
                    'count'   => 8,
                    'exclude' => $exclude_ids,
                ) ); ?>

                <?php // राजनीति (Politics) — 4 Column Grid ?>
                <?php get_template_part( 'template-parts/category-section', null, array(
                    'slug'   => 'राजनिती',
                    'title'  => 'राजनीति',
                    'layout' => 'grid-4',
                    'count'  => 4,
                ) ); ?>

                <?php // व्यवसाय (Business) — 4 Column Grid ?>
                <?php get_template_part( 'template-parts/category-section', null, array(
                    'slug'   => 'व्यवसाय',
                    'title'  => 'बिजनेस / व्यवसाय',
                    'layout' => 'grid-4',
                    'count'  => 4,
                ) ); ?>

                <?php // कृषि (Agriculture) — 3 Column Grid ?>
                <?php get_template_part( 'template-parts/category-section', null, array(
                    'slug'   => 'कृषि',
                    'title'  => 'कृषि',
                    'layout' => 'grid-3',
                    'count'  => 3,
                ) ); ?>

                <?php // अपराध (Crime) — 4 Column Grid ?>
                <?php get_template_part( 'template-parts/category-section', null, array(
                    'slug'   => 'अपराध',
                    'title'  => 'अपराध / सुरक्षा',
                    'layout' => 'grid-4',
                    'count'  => 4,
                ) ); ?>

                <?php // स्वास्थ्य (Health) — 3 Column Grid ?>
                <?php get_template_part( 'template-parts/category-section', null, array(
                    'slug'   => 'स्वास्थ्य-विज्ञान-र-प्रव',
                    'title'  => 'स्वास्थ्य, विज्ञान र प्रविधि',
                    'layout' => 'grid-3',
                    'count'  => 3,
                ) ); ?>

                <?php // शिक्षा / साहित्य (Education) — 4 Column Grid ?>
                <?php get_template_part( 'template-parts/category-section', null, array(
                    'slug'   => 'शिक्षा-साहित्य',
                    'title'  => 'शिक्षा / साहित्य',
                    'layout' => 'grid-4',
                    'count'  => 4,
                ) ); ?>

                <?php // खेलकुद (Sports) — 4 Column Grid ?>
                <?php get_template_part( 'template-parts/category-section', null, array(
                    'slug'   => 'खेलकुद',
                    'title'  => 'खेलकुद',
                    'layout' => 'grid-4',
                    'count'  => 4,
                ) ); ?>

                <?php // मनोरञ्जन (Entertainment) — 4 Column Grid ?>
                <?php get_template_part( 'template-parts/category-section', null, array(
                    'slug'   => 'मनोरञ्जन',
                    'title'  => 'मनोरञ्जन',
                    'layout' => 'grid-4',
                    'count'  => 4,
                ) ); ?>

                <?php // समाज (Society) — 4 Column Grid ?>
                <?php get_template_part( 'template-parts/category-section', null, array(
                    'slug'   => 'समाज',
                    'title'  => 'समाज',
                    'layout' => 'grid-4',
                    'count'  => 4,
                ) ); ?>

                <?php // स्थानीय तह / विकास — 4 Column Grid ?>
                <?php get_template_part( 'template-parts/category-section', null, array(
                    'slug'   => 'स्थानीय-तह-विकास',
                    'title'  => 'स्थानीय तह / विकास',
                    'layout' => 'grid-4',
                    'count'  => 4,
                ) ); ?>

                <?php // विदेश / कूटनीति (International) — 4 Column Grid ?>
                <?php get_template_part( 'template-parts/category-section', null, array(
                    'slug'   => 'विदेश-कूटनीति',
                    'title'  => 'विदेश / कूटनीति',
                    'layout' => 'grid-4',
                    'count'  => 4,
                ) ); ?>

                <?php // विविध (Miscellaneous) — 4 Column Grid ?>
                <?php get_template_part( 'template-parts/category-section', null, array(
                    'slug'   => 'विविध',
                    'title'  => 'विविध',
                    'layout' => 'grid-4',
                    'count'  => 4,
                ) ); ?>

            </div>

            <?php // === SIDEBAR COLUMN === ?>
            <aside class="ratopati-sidebar-wrap" role="complementary">
                <?php get_template_part( 'template-parts/sidebar-trending' ); ?>
            </aside>

        </div>
    </div>

</main>

<?php get_footer(); ?>
