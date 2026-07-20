<?php
/**
 * "एक्सक्लुसिभ"-style row: gradient-overlay image cards (category badge +
 * bold white headline on a dark bottom gradient). Used after the breaking
 * feed. Keeps fetching until three cards with featured images are filled
 * so the 3-column row never looks sparse.
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$maglist_child_exclusive_exclude = isset( $GLOBALS['maglist_child_shown_ids'] ) ? (array) $GLOBALS['maglist_child_shown_ids'] : array();
$maglist_child_exclusive_needed  = 3;

$maglist_child_exclusive_query = new WP_Query(
	apply_filters(
		'maglist_child_exclusive_query_args',
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => 12,
			'ignore_sticky_posts' => 1,
			'no_found_rows'       => true,
			'post__not_in'        => $maglist_child_exclusive_exclude,
			'meta_query'          => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => '_thumbnail_id',
					'compare' => 'EXISTS',
				),
			),
		)
	)
);

if ( ! $maglist_child_exclusive_query->have_posts() ) {
	return;
}

$maglist_child_exclusive_cards = array();
while ( $maglist_child_exclusive_query->have_posts() ) {
	$maglist_child_exclusive_query->the_post();
	if ( ! has_post_thumbnail() ) {
		continue;
	}
	$maglist_child_exclusive_cards[] = get_post();
	if ( count( $maglist_child_exclusive_cards ) >= $maglist_child_exclusive_needed ) {
		break;
	}
}
wp_reset_postdata();

if ( empty( $maglist_child_exclusive_cards ) ) {
	return;
}
?>

<div class="na-exclusive">
	<?php foreach ( $maglist_child_exclusive_cards as $maglist_child_card_post ) : ?>
		<a href="<?php echo esc_url( get_permalink( $maglist_child_card_post ) ); ?>" class="na-exclusive__card">
			<?php echo maglist_child_get_thumbnail( $maglist_child_card_post->ID, 'maglist-child-hero' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span class="na-exclusive__overlay">
				<?php echo maglist_child_category_badge( $maglist_child_card_post->ID ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span class="na-exclusive__title"><?php echo esc_html( get_the_title( $maglist_child_card_post ) ); ?></span>
			</span>
		</a>
	<?php endforeach; ?>
</div><!-- .na-exclusive -->
