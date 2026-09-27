<?php
/**
 * Search form. Same markup as the parent theme so the header overlay
 * keeps working; labels are Nepali.
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label>
		<span class="screen-reader-text"><?php esc_html_e( 'खोज्नुहोस्', 'maglist-child' ); ?></span>
		<input type="search" class="search-field" placeholder="<?php echo esc_attr__( 'खोज्नुहोस्', 'maglist-child' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" />
	</label>
	<button type="submit" class="search-submit">
		<span class="screen-reader-text"><?php esc_html_e( 'खोज्नुहोस्', 'maglist-child' ); ?></span>
		<i class="fa fa-search" aria-hidden="true"></i>
	</button>
</form>
