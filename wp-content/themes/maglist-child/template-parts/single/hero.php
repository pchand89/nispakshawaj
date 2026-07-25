<?php
/**
 * Single post featured image (or placeholder).
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$caption = get_the_post_thumbnail_caption();
?>
<figure class="na-single-hero">
	<div class="na-single-hero__media">
		<?php echo maglist_child_get_thumbnail( get_the_ID(), 'maglist-child-hero', true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
	<?php if ( $caption ) : ?>
		<figcaption class="na-single-hero__caption"><?php echo esc_html( $caption ); ?></figcaption>
	<?php endif; ?>
</figure>
