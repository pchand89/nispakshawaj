<?php
/**
 * Custom site footer - replaces the parent Maglist theme's footer
 * entirely (see the note at the top of header.php for why this is safe to
 * do sitewide). Structure mirrors the reference news layout's real .first-footer /
 * .last-footer bands: a dark "about/contact + quicklinks" band, then a
 * "logo + copyright + social icons" band.
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<footer class="na-footer">

	<div class="na-footer__top">
		<div class="na-container na-footer__top-grid">

			<div class="na-footer__about">
				<?php if ( is_active_sidebar( 'footer-about' ) ) : ?>
					<?php dynamic_sidebar( 'footer-about' ); ?>
				<?php else : ?>
					<p class="na-footer__about-placeholder">
						<?php bloginfo( 'name' ); ?><br>
						<?php bloginfo( 'description' ); ?>
					</p>
				<?php endif; ?>
			</div>

			<?php if ( has_nav_menu( 'footer' ) ) : ?>
				<div class="na-footer__links">
					<h5 class="na-footer__heading"><?php esc_html_e( 'उपयोगी लिंकहरु', 'maglist-child' ); ?></h5>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'container'      => false,
							'menu_class'     => 'na-footer__links-menu',
							'depth'          => 1,
						)
					);
					?>
				</div>
			<?php endif; ?>

		</div>
	</div><!-- .na-footer__top -->

	<div class="na-footer__bottom">
		<div class="na-container na-footer__bottom-grid">

			<div class="na-footer__brand">
				<?php if ( has_custom_logo() ) : ?>
					<span class="na-footer__logo"><?php the_custom_logo(); ?></span>
				<?php endif; ?>
				<p class="na-footer__copyright">
					&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'maglist-child' ); ?>
				</p>
			</div>

			<?php if ( has_nav_menu( 'social-menu-footer' ) ) : ?>
				<div class="na-footer__social">
					<?php
					// Menu item titles for this location are already authored as raw
					// icon markup (e.g. `<i class="fa fa-facebook">`), matching how the
					// parent theme's own header social menu is used - render as-is.
					wp_nav_menu(
						array(
							'theme_location' => 'social-menu-footer',
							'container'      => false,
							'menu_class'     => 'na-footer__social-menu',
							'depth'          => 1,
						)
					);
					?>
				</div>
			<?php endif; ?>

		</div>
	</div><!-- .na-footer__bottom -->

</footer><!-- .na-footer -->

<?php wp_footer(); ?>
</body>
</html>
