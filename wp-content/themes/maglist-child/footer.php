<?php
/**
 * Custom site footer - replaces the parent Maglist theme's footer
 * entirely (see the note at the top of header.php for why this is safe to
 * do sitewide). Four-column band (about, categories, useful links, follow)
 * plus a copyright bar.
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$na_footer_year = function_exists( 'maglist_child_to_nepali_digits' )
	? maglist_child_to_nepali_digits( date_i18n( 'Y' ) )
	: date_i18n( 'Y' );

$na_footer_categories = maglist_child_get_footer_categories();
$na_footer_useful     = maglist_child_get_footer_useful_links();
$na_footer_social     = maglist_child_get_footer_social_links();
?>

<footer class="na-footer">

	<div class="na-footer__top">
		<div class="na-container na-footer__top-grid">

			<div class="na-footer__col na-footer__about">
				<?php if ( has_custom_logo() ) : ?>
					<span class="na-footer__logo na-footer__logo--about"><?php the_custom_logo(); ?></span>
				<?php endif; ?>
				<p class="na-footer__tagline"><?php esc_html_e( 'निश्पक्ष आवाज प्रा. लि.', 'maglist-child' ); ?></p>

				<ul class="na-footer__meta">
					<li>
						<i class="fa fa-user" aria-hidden="true"></i>
						<span><?php esc_html_e( 'अध्यक्ष: श्रीमती निर्मला जोशी', 'maglist-child' ); ?></span>
					</li>
					<li>
						<i class="fa fa-user" aria-hidden="true"></i>
						<span><?php esc_html_e( 'सञ्चालक: श्रीमती दक्षिणा कुमारी बम', 'maglist-child' ); ?></span>
					</li>
					<li>
						<i class="fa fa-pencil" aria-hidden="true"></i>
						<span><?php esc_html_e( 'सम्पादक: श्री राज बहादुर चन्द', 'maglist-child' ); ?></span>
					</li>
					<li>
						<i class="fa fa-map-marker" aria-hidden="true"></i>
						<span><?php esc_html_e( 'भीमदत्त–१०, महेन्द्रनगर, कञ्चनपुर', 'maglist-child' ); ?></span>
					</li>
					<li>
						<i class="fa fa-phone" aria-hidden="true"></i>
						<span>
							<?php esc_html_e( 'सम्पर्क:', 'maglist-child' ); ?>
							<a href="tel:+9779742379333">९७४२३७९३३३</a>
						</span>
					</li>
					<li>
						<i class="fa fa-envelope" aria-hidden="true"></i>
						<span>
							<?php esc_html_e( 'इमेल:', 'maglist-child' ); ?>
							<a href="mailto:info@nispakshawaj.com">info@nispakshawaj.com</a>
						</span>
					</li>
				</ul>
			</div>

			<div class="na-footer__col na-footer__cats">
				<h5 class="na-footer__heading"><?php esc_html_e( 'समाचार विधा', 'maglist-child' ); ?></h5>
				<?php if ( has_nav_menu( 'footer-categories' ) ) : ?>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer-categories',
							'container'      => false,
							'menu_class'     => 'na-footer__list',
							'depth'          => 1,
						)
					);
					?>
				<?php elseif ( ! empty( $na_footer_categories ) ) : ?>
					<ul class="na-footer__list">
						<?php foreach ( $na_footer_categories as $na_footer_cat ) : ?>
							<li>
								<a href="<?php echo esc_url( $na_footer_cat['url'] ); ?>">
									<?php echo esc_html( $na_footer_cat['label'] ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

			<div class="na-footer__col na-footer__links">
				<h5 class="na-footer__heading"><?php esc_html_e( 'उपयोगी लिंक', 'maglist-child' ); ?></h5>
				<?php if ( has_nav_menu( 'footer' ) ) : ?>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'container'      => false,
							'menu_class'     => 'na-footer__list',
							'depth'          => 1,
						)
					);
					?>
				<?php elseif ( ! empty( $na_footer_useful ) ) : ?>
					<ul class="na-footer__list">
						<?php foreach ( $na_footer_useful as $na_footer_link ) : ?>
							<li>
								<a href="<?php echo esc_url( $na_footer_link['url'] ); ?>">
									<?php echo esc_html( $na_footer_link['label'] ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

			<div class="na-footer__col na-footer__follow">
				<h5 class="na-footer__heading"><?php esc_html_e( 'हामीलाई फलो गर्नुहोस्', 'maglist-child' ); ?></h5>

				<?php if ( has_nav_menu( 'social-menu-footer' ) ) : ?>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'social-menu-footer',
							'container'      => false,
							'menu_class'     => 'na-footer__social-menu',
							'depth'          => 1,
						)
					);
					?>
				<?php elseif ( ! empty( $na_footer_social ) ) : ?>
					<ul class="na-footer__social-menu">
						<?php foreach ( $na_footer_social as $na_footer_social_item ) : ?>
							<li>
								<a
									href="<?php echo esc_url( $na_footer_social_item['url'] ); ?>"
									target="_blank"
									rel="noopener noreferrer"
									aria-label="<?php echo esc_attr( $na_footer_social_item['label'] ); ?>"
									title="<?php echo esc_attr( $na_footer_social_item['label'] ); ?>"
								>
									<i class="<?php echo esc_attr( $na_footer_social_item['icon'] ); ?>" aria-hidden="true"></i>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<p class="na-footer__reg">
					<?php esc_html_e( 'सूचना विभाग दर्ता नं.: ३७१७', 'maglist-child' ); ?>
				</p>
			</div>

		</div>
	</div><!-- .na-footer__top -->

	<div class="na-footer__bottom">
		<div class="na-container na-footer__bottom-grid">
			<div class="na-footer__brand">
				<?php if ( has_custom_logo() ) : ?>
					<span class="na-footer__logo"><?php the_custom_logo(); ?></span>
				<?php endif; ?>
				<p class="na-footer__copyright">
					&copy; <?php echo esc_html( $na_footer_year ); ?>
					<?php esc_html_e( 'निश्पक्ष आवाज प्रा. लि.', 'maglist-child' ); ?> ।
					<?php esc_html_e( 'सर्वाधिकार सुरक्षित।', 'maglist-child' ); ?>
				</p>
			</div>
		</div>
	</div><!-- .na-footer__bottom -->

</footer><!-- .na-footer -->

<?php wp_footer(); ?>
</body>
</html>
