<?php
/**
 * Template Part: Category Section — Ratopati Style
 *
 * Displays a category section with Ratopati's signature red accent title,
 * 'थप समाचार' link, and 4-column or 3-column card grid.
 *
 * @package Nispaksha_Child
 */

$cat_slug   = isset( $args['slug'] ) ? $args['slug'] : '';
$cat_title  = isset( $args['title'] ) ? $args['title'] : '';
$layout     = isset( $args['layout'] ) ? $args['layout'] : 'grid-4';
$count      = isset( $args['count'] ) ? intval( $args['count'] ) : 4;
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

<section class="ratopati-section <?php echo esc_attr( $bg_class ); ?>" id="section-<?php echo esc_attr( sanitize_title( $cat_slug ) ); ?>">

    <div class="ratopati-section__header">
        <h2 class="ratopati-section__title">
            <?php echo esc_html( $cat_display_name ); ?>
        </h2>
        <?php if ( $category ) : ?>
            <a href="<?php echo esc_url( $cat_link ); ?>" class="ratopati-section__more">
                थप समाचार <i class="fas fa-arrow-right"></i>
            </a>
        <?php endif; ?>
    </div>

    <?php if ( $layout === 'grid-4' ) : ?>
        <div class="ratopati-grid-4">
            <?php while ( $cat_query->have_posts() ) : $cat_query->the_post(); ?>
                <?php get_template_part( 'template-parts/news-card', null, array( 'variant' => 'card' ) ); ?>
            <?php endwhile; ?>
        </div>

    <?php elseif ( $layout === 'grid-3' ) : ?>
        <div class="ratopati-grid-3">
            <?php while ( $cat_query->have_posts() ) : $cat_query->the_post(); ?>
                <?php get_template_part( 'template-parts/news-card', null, array( 'variant' => 'card' ) ); ?>
            <?php endwhile; ?>
        </div>

    <?php elseif ( $layout === 'grid-2' ) : ?>
        <div class="ratopati-grid-2">
            <?php while ( $cat_query->have_posts() ) : $cat_query->the_post(); ?>
                <?php get_template_part( 'template-parts/news-card', null, array( 'variant' => 'card' ) ); ?>
            <?php endwhile; ?>
        </div>

    <?php else : ?>
        <div class="ratopati-grid-2">
            <?php while ( $cat_query->have_posts() ) : $cat_query->the_post(); ?>
                <?php get_template_part( 'template-parts/news-card', null, array( 'variant' => 'horizontal' ) ); ?>
            <?php endwhile; ?>
        </div>

    <?php endif; ?>

</section>

<?php wp_reset_postdata(); ?>
