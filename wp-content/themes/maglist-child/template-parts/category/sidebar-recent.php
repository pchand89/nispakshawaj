<?php
/**
 * भर्खरै (recent) sidebar for category archives.
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$recent = new WP_Query(
	array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => 8,
		'ignore_sticky_posts' => 1,
		'no_found_rows'       => true,
	)
);

if ( ! $recent->have_posts() ) {
	return;
}
?>
<aside class="na-cat-recent" aria-label="<?php echo esc_attr__( 'भर्खरै', 'maglist-child' ); ?>">
	<h2 class="na-cat-recent__title"><?php esc_html_e( 'भर्खरै', 'maglist-child' ); ?></h2>
	<ul class="na-cat-recent__list">
		<?php
		while ( $recent->have_posts() ) :
			$recent->the_post();
			?>
			<li>
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</li>
		<?php endwhile; ?>
	</ul>
</aside>
<?php
wp_reset_postdata();
