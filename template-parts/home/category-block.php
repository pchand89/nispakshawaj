<?php
/**
 * Reusable category row matching ratopati.com's real recurring section
 * pattern: a heading with a "थप समाचार" (more) link, then ONE large "lead"
 * card (thumbnail + title + excerpt side-by-side) followed by 3-4 smaller
 * thumbnail-only cards (title under image, no excerpt) - not a uniform grid
 * of identical cards.
 *
 * Expects $args (passed via get_template_part()'s 4th parameter) shaped like:
 *   array( 'slug' => 'राजनिती', 'count' => 6, 'columns' => 3 )
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Note: slug is NOT passed through sanitize_title() here - that would mangle
// raw Devanagari/Nepali slugs (e.g. "राजनिती"). maglist_child_resolve_category()
// already tries a sanitize_title()'d variant itself as one of its fallbacks.
$maglist_child_slug    = isset( $args['slug'] ) ? (string) $args['slug'] : '';
$maglist_child_count   = isset( $args['count'] ) ? absint( $args['count'] ) : 6;
$maglist_child_columns = isset( $args['columns'] ) && in_array( (int) $args['columns'], array( 2, 3, 4 ), true ) ? (int) $args['columns'] : 3;

$maglist_child_category_query = maglist_child_get_category_query( $maglist_child_slug, $maglist_child_count );

if ( ! $maglist_child_category_query->have_posts() ) {
	return; // Nothing to show yet - skip the block instead of rendering empty markup.
}

$maglist_child_category_term = $maglist_child_slug ? maglist_child_resolve_category( $maglist_child_slug ) : false;
$maglist_child_category_link = $maglist_child_category_term ? get_category_link( $maglist_child_category_term ) : '';

// Prefer an explicit 'label' arg; otherwise fall back to the resolved term's
// real name so the heading is never out of sync with the actual category.
$maglist_child_label = isset( $args['label'] )
	? $args['label']
	: ( $maglist_child_category_term ? $maglist_child_category_term->name : esc_html__( 'Latest', 'maglist-child' ) );

$maglist_child_post_index = 0;
?>

<div class="rp-section">

	<div class="rp-section__header">
		<h2 class="rp-section__title">
			<?php if ( $maglist_child_category_link ) : ?>
				<a href="<?php echo esc_url( $maglist_child_category_link ); ?>"><?php echo esc_html( $maglist_child_label ); ?></a>
			<?php else : ?>
				<?php echo esc_html( $maglist_child_label ); ?>
			<?php endif; ?>
		</h2>

		<?php if ( $maglist_child_category_link ) : ?>
			<a class="rp-section__more" href="<?php echo esc_url( $maglist_child_category_link ); ?>">
				<?php esc_html_e( 'थप समाचार', 'maglist-child' ); ?> &rarr;
			</a>
		<?php endif; ?>
	</div><!-- .rp-section__header -->

	<div class="rp-section__grid rp-section__grid--cols-<?php echo esc_attr( $maglist_child_columns ); ?>">
		<?php
		while ( $maglist_child_category_query->have_posts() ) :
			$maglist_child_category_query->the_post();
			$maglist_child_post_index++;

			if ( 1 === $maglist_child_post_index ) :
				// First post: the large "lead" card (thumbnail + title + excerpt).
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'rp-lead-card' ); ?>>
					<a class="rp-lead-card__thumb" href="<?php the_permalink(); ?>">
						<?php echo maglist_child_get_thumbnail( get_the_ID(), 'maglist-child-card' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
					<div class="rp-lead-card__body">
						<h3 class="rp-lead-card__title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h3>
						<p class="rp-lead-card__excerpt">
							<?php echo esc_html( wp_trim_words( get_the_excerpt(), 26 ) ); ?>
						</p>
						<span class="rp-lead-card__time"><?php echo esc_html( maglist_child_time_ago( get_the_ID() ) ); ?></span>
					</div>
				</article>
				<?php
			else :
				// Remaining posts: compact thumbnail-only cards (title under image, no excerpt).
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'rp-thumb-card' ); ?>>
					<a class="rp-thumb-card__thumb" href="<?php the_permalink(); ?>">
						<?php echo maglist_child_get_thumbnail( get_the_ID(), 'maglist-child-card' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
					<h3 class="rp-thumb-card__title">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h3>
				</article>
				<?php
			endif;
		endwhile;
		wp_reset_postdata();
		?>
	</div><!-- .rp-section__grid -->

</div><!-- .rp-section -->
