<?php
/**
 * Template Part: Category Section
 *
 * Reusable template for each category block on the homepage.
 * Pass parameters via $args array.
 *
 * Usage:
 * get_template_part( 'template-parts/category-section', null, array(
 *     'slug'   => 'samachar',
 *     'title'  => 'समाचार',
 *     'layout' => 'grid-3',  // grid-3, grid-2, featured-list, horizontal
 *     'count'  => 6,
 *     'bg'     => '',        // 'alt' for alternate background
 * ) );
 *
 * @package Nispaksha_Child
 */

$cat_slug   = isset( $args['slug'] ) ? $args['slug'] : '';
$cat_title  = isset( $args['title'] ) ? $args['title'] : '';
$layout     = isset( $args['layout'] ) ? $args['layout'] : 'grid-3';
$count      = isset( $args['count'] ) ? intval( $args['count'] ) : 6;
$bg_class   = isset( $args['bg'] ) && $args['bg'] === 'alt' ? 'bg-alt' : '';
$exclude    = isset( $args['exclude'] ) ? $args['exclude'] : array();

if ( empty( $cat_slug ) ) {
    return;
}

$category = get_category_by_slug( $cat_slug );
if ( ! $category ) {
    $category = get_category_by_slug( urlencode( $cat_slug ) );
}
if ( ! $category ) {
    $category = get_category_by_slug( sanitize_title( $cat_slug ) );
}
if ( ! $category ) {
    $category = get_term_by( 'name', $cat_title ?: $cat_slug, 'category' );
}

$cat_query = nispaksha_get_category_posts( $cat_slug, $count, $exclude );

if ( ! $cat_query->have_posts() ) {
    wp_reset_postdata();
    return;
}
$cat_display_name = $cat_title;
$cat_link = '#';
if ( $category ) {
    $cat_display_name = $cat_title ?: $category->name;
    $cat_link = get_category_link( $category->term_id );
}
?>

<section class="nispaksha-category-section <?php echo esc_attr( $bg_class ); ?>" id="section-<?php echo esc_attr( sanitize_title( $cat_slug ) ); ?>">

    <div class="nispaksha-section-header">
        <h2 class="nispaksha-section-title">
            <?php echo esc_html( $cat_display_name ); ?>
        </h2>
        <?php if ( $category ) : ?>
            <a href="<?php echo esc_url( $cat_link ); ?>" class="nispaksha-section-more">
                थप हेर्नुहोस्
            </a>
        <?php endif; ?>
    </div>

    <?php if ( $layout === 'grid-3' ) : ?>
        <div class="nispaksha-grid-3">
            <?php while ( $cat_query->have_posts() ) : $cat_query->the_post(); ?>
                <?php get_template_part( 'template-parts/news-card', null, array( 'variant' => 'medium' ) ); ?>
            <?php endwhile; ?>
        </div>

    <?php elseif ( $layout === 'grid-2' ) : ?>
        <div class="nispaksha-grid-2">
            <?php while ( $cat_query->have_posts() ) : $cat_query->the_post(); ?>
                <?php get_template_part( 'template-parts/news-card', null, array( 'variant' => 'medium' ) ); ?>
            <?php endwhile; ?>
        </div>

    <?php elseif ( $layout === 'featured-list' ) : ?>
        <div class="nispaksha-featured-list">
            <?php
            $first = true;
            while ( $cat_query->have_posts() ) : $cat_query->the_post();
                if ( $first ) :
                    $first = false;
            ?>
                <div class="nispaksha-featured-list__main">
                    <?php get_template_part( 'template-parts/news-card', null, array(
                        'variant'      => 'large',
                        'show_excerpt' => true,
                    ) ); ?>
                </div>
                <div class="nispaksha-featured-list__items">
            <?php else : ?>
                    <?php get_template_part( 'template-parts/news-card', null, array( 'variant' => 'list' ) ); ?>
            <?php
                endif;
            endwhile;
            ?>
                </div>
        </div>

    <?php elseif ( $layout === 'horizontal' ) : ?>
        <div class="nispaksha-grid-2">
            <?php while ( $cat_query->have_posts() ) : $cat_query->the_post(); ?>
                <?php get_template_part( 'template-parts/news-card', null, array( 'variant' => 'horizontal' ) ); ?>
            <?php endwhile; ?>
        </div>

    <?php endif; ?>

</section>

<?php wp_reset_postdata(); ?>
