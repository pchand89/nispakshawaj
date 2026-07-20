<?php
/**
 * Template Part: Category Section — Pixel-Perfect Ratopati Style
 *
 * @package Nispaksha_Child
 */

$cat_slug   = isset( $args['slug'] ) ? $args['slug'] : '';
$cat_title  = isset( $args['title'] ) ? $args['title'] : '';
$count      = isset( $args['count'] ) ? intval( $args['count'] ) : 4;
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

<section class="rp-section" id="section-<?php echo esc_attr( sanitize_title( $cat_slug ) ); ?>">

    <div class="rp-section__header">
        <h2 class="rp-section__title">
            <?php echo esc_html( $cat_display_name ); ?>
        </h2>
        <?php if ( $category ) : ?>
            <a href="<?php echo esc_url( $cat_link ); ?>" class="rp-section__more">
                थप समाचार <i class="fas fa-angle-right"></i>
            </a>
        <?php endif; ?>
    </div>

    <div class="rp-grid-4">
        <?php while ( $cat_query->have_posts() ) : $cat_query->the_post(); ?>
            <?php get_template_part( 'template-parts/news-card' ); ?>
        <?php endwhile; ?>
    </div>

</section>

<?php wp_reset_postdata(); ?>
