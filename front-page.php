<?php
/**
 * Front Page Template
 *
 * Custom homepage template for Nispaksha Awaj news portal.
 * Automatically used by WordPress when this file exists in the theme.
 *
 * Layout:
 * 1. Breaking News Ticker
 * 2. Hero Section (5 featured posts)
 * 3. Main Content (category sections) + Sidebar (trending + latest)
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

    <?php // 1. Breaking News Ticker ?>
    <?php get_template_part( 'template-parts/breaking-news' ); ?>

    <?php // 2. Hero Section ?>
    <?php get_template_part( 'template-parts/hero-section' ); ?>

    <?php // 3. Main Content + Sidebar ?>
    <div class="nispaksha-container">
        <div class="nispaksha-main-layout">

            <?php // === MAIN CONTENT COLUMN === ?>
            <div class="nispaksha-content">

                <?php // समाचार (News) — Grid 3 ?>
                <?php get_template_part( 'template-parts/category-section', null, array(
                    'slug'    => 'samachar',
                    'title'   => 'समाचार',
                    'layout'  => 'grid-3',
                    'count'   => 6,
                    'exclude' => $exclude_ids,
                ) ); ?>

                <?php // राजनीति (Politics) — Featured + List ?>
                <?php get_template_part( 'template-parts/category-section', null, array(
                    'slug'   => 'rajniti',
                    'title'  => 'राजनीति',
                    'layout' => 'featured-list',
                    'count'  => 5,
                    'bg'     => 'alt',
                ) ); ?>

                <?php // व्यवसाय (Business) — Grid 3 ?>
                <?php get_template_part( 'template-parts/category-section', null, array(
                    'slug'   => 'byabasaya',
                    'title'  => 'व्यवसाय',
                    'layout' => 'grid-3',
                    'count'  => 3,
                ) ); ?>

                <?php // कृषि (Agriculture) — Grid 2 ?>
                <?php get_template_part( 'template-parts/category-section', null, array(
                    'slug'   => 'krishi',
                    'title'  => 'कृषि',
                    'layout' => 'grid-2',
                    'count'  => 4,
                    'bg'     => 'alt',
                ) ); ?>

                <?php // अपराध (Crime) — Horizontal ?>
                <?php get_template_part( 'template-parts/category-section', null, array(
                    'slug'   => 'aparadh',
                    'title'  => 'अपराध',
                    'layout' => 'horizontal',
                    'count'  => 4,
                ) ); ?>

                <?php // स्वास्थ्य (Health) — Grid 2 ?>
                <?php get_template_part( 'template-parts/category-section', null, array(
                    'slug'   => 'swasthya-bigyan-ra-prabidhi',
                    'title'  => 'स्वास्थ्य, विज्ञान र प्रविधि',
                    'layout' => 'grid-2',
                    'count'  => 4,
                    'bg'     => 'alt',
                ) ); ?>

                <?php // शिक्षा / साहित्य (Education) — Featured List ?>
                <?php get_template_part( 'template-parts/category-section', null, array(
                    'slug'   => 'shiksha-sahitya',
                    'title'  => 'शिक्षा / साहित्य',
                    'layout' => 'featured-list',
                    'count'  => 5,
                ) ); ?>

                <?php // खेलकुद (Sports) — Grid 3 ?>
                <?php get_template_part( 'template-parts/category-section', null, array(
                    'slug'   => 'khelkud',
                    'title'  => 'खेलकुद',
                    'layout' => 'grid-3',
                    'count'  => 3,
                    'bg'     => 'alt',
                ) ); ?>

                <?php // मनोरञ्जन (Entertainment) — Grid 2 ?>
                <?php get_template_part( 'template-parts/category-section', null, array(
                    'slug'   => 'manoranjan',
                    'title'  => 'मनोरञ्जन',
                    'layout' => 'grid-2',
                    'count'  => 4,
                ) ); ?>

                <?php // समाज (Society) — Horizontal ?>
                <?php get_template_part( 'template-parts/category-section', null, array(
                    'slug'   => 'samaj',
                    'title'  => 'समाज',
                    'layout' => 'horizontal',
                    'count'  => 4,
                    'bg'     => 'alt',
                ) ); ?>

                <?php // विदेश / कूटनीति (International) — Grid 2 ?>
                <?php get_template_part( 'template-parts/category-section', null, array(
                    'slug'   => 'bidesh-kutniti',
                    'title'  => 'विदेश / कूटनीति',
                    'layout' => 'grid-2',
                    'count'  => 4,
                ) ); ?>

                <?php // विविध (Miscellaneous) — Horizontal ?>
                <?php get_template_part( 'template-parts/category-section', null, array(
                    'slug'   => 'bibidh',
                    'title'  => 'विविध',
                    'layout' => 'horizontal',
                    'count'  => 4,
                    'bg'     => 'alt',
                ) ); ?>

            </div>

            <?php // === SIDEBAR === ?>
            <aside class="nispaksha-sidebar" role="complementary">
                <?php get_template_part( 'template-parts/sidebar-trending' ); ?>

                <?php // Additional widget area ?>
                <?php if ( is_active_sidebar( 'nispaksha-home-sidebar' ) ) : ?>
                    <?php dynamic_sidebar( 'nispaksha-home-sidebar' ); ?>
                <?php endif; ?>
            </aside>

        </div>
    </div>

</main>

<?php get_footer(); ?>
