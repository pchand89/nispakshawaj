<?php
/**
 * Main sidebar — Maglist parent sidebar plus the child theme sidebar-ad slot.
 *
 * Used on single posts and any parent template that calls get_sidebar().
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<aside id="secondary" class="widget-area">
	<?php maglist_child_widget_area( 'sidebar-ad', 'na-ad-slot na-ad-sidebar', true ); ?>

	<?php if ( is_active_sidebar( 'maglist_sidebar' ) ) : ?>
		<?php
		$sidebar = apply_filters( Maglist_Theme::fn_prefix( 'sidebar' ), 'maglist_sidebar' );
		dynamic_sidebar( $sidebar );
		?>
	<?php else : ?>
		<?php
		Maglist_Theme::the_default_search();
		Maglist_Theme::the_default_recent_post();
		Maglist_Theme::the_default_archive();
		?>
	<?php endif; ?>
</aside><!-- #secondary -->
