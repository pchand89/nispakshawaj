<?php
/**
 * Top "breaking" feed - matches the reference news layout's actual homepage structure:
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
		'posts_per_page'      => 3,
		'ignore_sticky_posts' => 0, // A sticky post naturally becomes the top "breaking" item.
		'no_found_rows'       => true,
	)
);

if ( ! $maglist_child_breaking_query->have_posts() ) {
	return;
}

$maglist_child_breaking_index = 0;
?>

<div class="na-breaking">
	<?php
	while ( $maglist_child_breaking_query->have_posts() ) :
		$maglist_child_breaking_query->the_post();
		$maglist_child_breaking_index++;
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'na-breaking__item' ); ?>>

			<?php echo maglist_child_category_badge( get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

			<h2 class="na-breaking__title<?php echo ( 1 === $maglist_child_breaking_index ) ? ' na-breaking__title--lead' : ''; ?>">
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</h2>

			<?php
			$maglist_child_breaking_excerpt = wp_trim_words( get_the_excerpt(), 22 );
			if ( $maglist_child_breaking_excerpt ) :
				?>
				<p class="na-breaking__dek"><?php echo esc_html( $maglist_child_breaking_excerpt ); ?></p>
			<?php endif; ?>

			<div class="na-breaking__meta">
				<span class="na-breaking__author"><?php the_author(); ?></span>
				<span class="na-breaking__dot" aria-hidden="true">&middot;</span>
				<span class="na-breaking__time"><?php echo esc_html( maglist_child_time_ago( get_the_ID() ) ); ?></span>
			</div>

			<?php if ( has_post_thumbnail() ) : ?>
				<a class="na-breaking__thumb" href="<?php the_permalink(); ?>">
					<?php echo maglist_child_get_thumbnail( get_the_ID(), 'maglist-child-hero' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			<?php endif; ?>

		</article>
		<?php
		// Leaderboard/widget slot after each breaking story (1–3).
		maglist_child_widget_area(
			'home-breaking-ad-' . $maglist_child_breaking_index,
			'na-ad-slot na-ad-breaking na-ad-breaking--' . $maglist_child_breaking_index,
			true
		);
	endwhile;
	wp_reset_postdata();
	?>
</div><!-- .na-breaking -->
<?php
// Let later homepage sections (e.g. exclusive-block.php) avoid repeating
// whatever already ran at the very top of the page.
$GLOBALS['maglist_child_shown_ids'] = wp_list_pluck( $maglist_child_breaking_query->posts, 'ID' );
