<?php
/**
 * Category archive orchestrator (lead + feed + sidebar + pagination).
 *
 * @package Maglist_Child
 *
 * @var array $args {
 *   @type string        $title Category display name.
 *   @type WP_Post[]     $posts Posts for this page (lead = first).
 *   @type WP_Query|null $query Optional custom query for hub-page pagination.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title = isset( $args['title'] ) ? $args['title'] : '';
$posts = isset( $args['posts'] ) && is_array( $args['posts'] ) ? $args['posts'] : array();
$query = isset( $args['query'] ) ? $args['query'] : null;

$lead = ! empty( $posts ) ? array_shift( $posts ) : null;
?>
<section class="na-cat">
	<div class="na-container na-cat__inner">
		<header class="na-cat__header">
			<h1 class="na-cat__title"><?php echo esc_html( $title ); ?></h1>
			<div class="na-cat__view-toggle" role="group" aria-label="<?php echo esc_attr__( 'दृश्य', 'maglist-child' ); ?>">
				<button type="button" class="na-cat__view-btn is-active" data-na-cat-view="list" aria-pressed="true">
					<?php esc_html_e( 'List View', 'maglist-child' ); ?>
				</button>
				<button type="button" class="na-cat__view-btn" data-na-cat-view="grid" aria-pressed="false">
					<?php esc_html_e( 'Grid View', 'maglist-child' ); ?>
				</button>
			</div>
		</header>

		<div class="na-cat__layout">
			<div class="na-cat__main">
				<?php if ( $lead instanceof WP_Post ) : ?>
					<?php
					get_template_part(
						'template-parts/category/lead',
						null,
						array( 'post' => $lead )
					);
					?>
				<?php endif; ?>

				<?php if ( empty( $lead ) && empty( $posts ) ) : ?>
					<p class="na-cat__empty"><?php esc_html_e( 'कुनै समाचार भेटिएन।', 'maglist-child' ); ?></p>
				<?php else : ?>
					<div class="na-cat__feed na-cat__feed--list" data-na-cat-feed>
						<div class="na-cat__feed-list">
							<?php foreach ( $posts as $feed_post ) : ?>
								<?php
								get_template_part(
									'template-parts/category/list-item',
									null,
									array( 'post' => $feed_post )
								);
								?>
							<?php endforeach; ?>
						</div>
						<div class="na-cat__feed-grid" hidden>
							<?php foreach ( $posts as $feed_post ) : ?>
								<?php
								get_template_part(
									'template-parts/category/grid-item',
									null,
									array( 'post' => $feed_post )
								);
								?>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>

				<div class="na-cat__pagination">
					<?php maglist_child_category_pagination( $query instanceof WP_Query ? $query : null ); ?>
				</div>
			</div>

			<div class="na-cat__sidebar">
				<?php maglist_child_widget_area( 'sidebar-ad', 'na-ad-slot na-ad-sidebar', true ); ?>
				<?php get_template_part( 'template-parts/category/sidebar-recent' ); ?>
			</div>
		</div>
	</div>
</section>
