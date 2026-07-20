<?php
/**
 * Category section dispatcher.
 *
 * Expects $args:
 *   slug     (string)  category slug (Devanagari OK)
 *   count    (int)     posts to pull
 *   layout   (string)  main-news | lead-grid | overlay-lists | dark-band | sports-band | edu-split | default
 *   band     (string)  optional color key for band layouts (purple|navy|green)
 *   label    (string)  optional heading override
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$maglist_child_slug   = isset( $args['slug'] ) ? (string) $args['slug'] : '';
$maglist_child_count  = isset( $args['count'] ) ? absint( $args['count'] ) : 6;
$maglist_child_layout = isset( $args['layout'] ) ? (string) $args['layout'] : 'default';
$maglist_child_band   = isset( $args['band'] ) ? (string) $args['band'] : '';

if ( $maglist_child_slug && ! maglist_child_category_exists( $maglist_child_slug ) ) {
	return;
}

$maglist_child_query = maglist_child_get_category_query( $maglist_child_slug, $maglist_child_count );

if ( ! $maglist_child_query->have_posts() ) {
	return;
}

$maglist_child_term = $maglist_child_slug ? maglist_child_resolve_category( $maglist_child_slug ) : false;
$maglist_child_link = $maglist_child_term ? get_category_link( $maglist_child_term ) : '';
$maglist_child_label = isset( $args['label'] )
	? $args['label']
	: ( $maglist_child_term ? $maglist_child_term->name : esc_html__( 'Latest', 'maglist-child' ) );

$layout_args = array(
	'posts'         => $maglist_child_query->posts,
	'label'         => $maglist_child_label,
	'category_link' => $maglist_child_link,
	'band'          => $maglist_child_band,
);

$layout_map = array(
	'main-news'     => 'layouts/main-news',
	'lead-grid'     => 'layouts/lead-grid',
	'overlay-lists' => 'layouts/overlay-lists',
	'dark-band'     => 'layouts/dark-band',
	'sports-band'   => 'layouts/sports-band',
	'edu-split'     => 'layouts/edu-split',
);

if ( isset( $layout_map[ $maglist_child_layout ] ) ) {
	get_template_part( 'template-parts/home/' . $layout_map[ $maglist_child_layout ], null, $layout_args );
	wp_reset_postdata();
	return;
}

// Default: lead card + thumbnail cards (kept for simpler rows).
$maglist_child_post_index = 0;
$maglist_child_category_link = $maglist_child_link;
?>
<div class="na-section">
	<?php require get_stylesheet_directory() . '/template-parts/home/section-header.php'; ?>

	<div class="na-section__grid na-section__grid--cols-3">
		<?php
		while ( $maglist_child_query->have_posts() ) :
			$maglist_child_query->the_post();
			$maglist_child_post_index++;

			if ( 1 === $maglist_child_post_index ) :
				?>
				<article <?php post_class( 'na-lead-card' . ( has_post_thumbnail() ? '' : ' na-lead-card--no-thumb' ) ); ?>>
					<?php if ( has_post_thumbnail() ) : ?>
						<a class="na-lead-card__thumb" href="<?php the_permalink(); ?>">
							<?php echo maglist_child_get_thumbnail( get_the_ID(), 'maglist-child-card' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
					<?php endif; ?>
					<div class="na-lead-card__body">
						<h3 class="na-lead-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<p class="na-lead-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 26 ) ); ?></p>
					</div>
				</article>
				<?php
			else :
				?>
				<article <?php post_class( 'na-thumb-card' . ( has_post_thumbnail() ? '' : ' na-thumb-card--no-thumb' ) ); ?>>
					<?php if ( has_post_thumbnail() ) : ?>
						<a class="na-thumb-card__thumb" href="<?php the_permalink(); ?>">
							<?php echo maglist_child_get_thumbnail( get_the_ID(), 'maglist-child-card' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
					<?php endif; ?>
					<h3 class="na-thumb-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
				</article>
				<?php
			endif;
		endwhile;
		wp_reset_postdata();
		?>
	</div>
</div>
