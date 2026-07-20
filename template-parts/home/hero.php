<?php
/**
 * Top "breaking" feed - matches ratopati.com's actual homepage structure:
 * a vertically stacked series of full-width, centered items (small category
 * tag, large bold headline, byline + date, then a full-width image below),
 * NOT a hero-image-left/fresh-news-list-right split. There is no sidebar
 * next to this section on the real site.
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$maglist_child_breaking_query = new WP_Query(
	array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => 4,
		'ignore_sticky_posts' => 0, // A sticky post naturally becomes the top "breaking" item.
		'no_found_rows'       => true,
	)
);

if ( ! $maglist_child_breaking_query->have_posts() ) {
	return;
}

$maglist_child_breaking_index = 0;
?>

<div class="rp-breaking">
	<?php
	while ( $maglist_child_breaking_query->have_posts() ) :
		$maglist_child_breaking_query->the_post();
		$maglist_child_breaking_index++;
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'rp-breaking__item' ); ?>>

			<?php echo maglist_child_category_badge( get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

			<h2 class="rp-breaking__title<?php echo ( 1 === $maglist_child_breaking_index ) ? ' rp-breaking__title--lead' : ''; ?>">
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</h2>

			<div class="rp-breaking__meta">
				<span class="rp-breaking__author"><?php the_author(); ?></span>
				<span class="rp-breaking__dot" aria-hidden="true">&middot;</span>
				<span class="rp-breaking__time"><?php echo esc_html( maglist_child_time_ago( get_the_ID() ) ); ?></span>
			</div>

			<?php if ( has_post_thumbnail() ) : ?>
				<a class="rp-breaking__thumb" href="<?php the_permalink(); ?>">
					<?php echo maglist_child_get_thumbnail( get_the_ID(), 'maglist-child-hero' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			<?php endif; ?>

		</article>
		<?php
	endwhile;
	wp_reset_postdata();
	?>
</div><!-- .rp-breaking -->
