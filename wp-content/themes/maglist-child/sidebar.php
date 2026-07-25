<?php
/**
 * Main sidebar — Sidebar Ad + Maglist Sidebar widgets.
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
	<?php get_template_part( 'template-parts/common/sidebar-widgets' ); ?>
</aside><!-- #secondary -->
