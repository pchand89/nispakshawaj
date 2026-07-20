<?php
/**
 * Custom site header — topbar, logo header, sticky primary nav, trending tags.
 * Colors are owned by site-header.css / style.css tokens (do not hardcode brand
 * swaps here).
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class( 'na-body' ); ?>>
<?php wp_body_open(); ?>

<?php
// Sitewide leaderboard — always render the anchor so Ad Inserter can target it.
maglist_child_widget_area( 'home-above-header', 'na-ad-slot na-ad-above-header', true );
?>

<div class="na-topbar">
	<div class="na-container na-topbar__inner">
		<button type="button" class="na-topbar__hamburger" data-na-nav-toggle aria-label="<?php esc_attr_e( 'Menu', 'maglist-child' ); ?>" aria-expanded="false">
			<span></span><span></span><span></span>
		</button>

		<span class="na-topbar__date"><?php echo esc_html( maglist_child_today_nepali_bs_date() ); ?></span>

		<?php if ( has_nav_menu( 'top-bar' ) ) : ?>
			<div class="na-topbar__links">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'top-bar',
						'container'      => false,
						'menu_class'     => 'na-topbar__menu',
						'depth'          => 1,
					)
				);
				?>
			</div>
		<?php else : ?>
			<div class="na-topbar__links" aria-hidden="true"></div>
		<?php endif; ?>

		<button type="button" class="na-topbar__search-btn" data-na-search-toggle aria-label="<?php esc_attr_e( 'Search', 'maglist-child' ); ?>">
			<i class="fa fa-search" aria-hidden="true"></i>
		</button>
	</div>
</div><!-- .na-topbar -->

<header class="na-header">
	<div class="na-container na-header__inner">
		<div class="na-header__logo">
			<?php
			if ( has_custom_logo() ) {
				the_custom_logo();
			} else {
				?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="na-header__site-name">
					<?php bloginfo( 'name' ); ?>
				</a>
				<?php
			}
			?>
		</div>
	</div>
</header><!-- .na-header -->

<div class="na-search-overlay" data-na-search-overlay>
	<div class="na-container">
		<?php echo get_search_form( array( 'echo' => false ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<button type="button" class="na-search-overlay__close" data-na-search-toggle aria-label="<?php esc_attr_e( 'Close search', 'maglist-child' ); ?>">&times;</button>
	</div>
</div>

<nav class="na-nav" data-na-nav>
	<div class="na-container">
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'na-nav__menu',
				'fallback_cb'    => false,
			)
		);
		?>
	</div>
</nav><!-- .na-nav -->

<?php
$maglist_child_trending_tags = get_terms(
	array(
		'taxonomy'   => 'post_tag',
		'orderby'    => 'count',
		'order'      => 'DESC',
		'number'     => 14,
		'hide_empty' => true,
	)
);

if ( ! is_wp_error( $maglist_child_trending_tags ) && ! empty( $maglist_child_trending_tags ) ) :
	?>
	<div class="na-trending-tags">
		<div class="na-container">
			<ul>
				<?php foreach ( $maglist_child_trending_tags as $maglist_child_tag ) : ?>
					<li>
						<a href="<?php echo esc_url( get_tag_link( $maglist_child_tag ) ); ?>"><?php echo esc_html( $maglist_child_tag->name ); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div><!-- .na-trending-tags -->
	<?php
endif;
