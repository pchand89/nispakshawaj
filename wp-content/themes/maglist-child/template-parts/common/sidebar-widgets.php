<?php
/**
 * Maglist main Sidebar widget area (Appearance → Widgets → Sidebar).
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="na-sidebar-widgets">
	<?php if ( is_active_sidebar( 'maglist_sidebar' ) ) : ?>
		<?php
		$sidebar = apply_filters( Maglist_Theme::fn_prefix( 'sidebar' ), 'maglist_sidebar' );
		dynamic_sidebar( $sidebar );
		?>
	<?php elseif ( class_exists( 'Maglist_Theme' ) ) : ?>
		<?php
		Maglist_Theme::the_default_search();
		Maglist_Theme::the_default_recent_post();
		Maglist_Theme::the_default_archive();
		?>
	<?php endif; ?>
</div>
