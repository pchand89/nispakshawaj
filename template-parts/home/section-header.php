<?php
/**
 * Shared section header (title + optional "थप समाचार" link).
 * Expects: $maglist_child_label, $maglist_child_category_link (optional).
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="na-section__header">
	<h2 class="na-section__title">
		<?php if ( ! empty( $maglist_child_category_link ) ) : ?>
			<a href="<?php echo esc_url( $maglist_child_category_link ); ?>"><?php echo esc_html( $maglist_child_label ); ?></a>
		<?php else : ?>
			<?php echo esc_html( $maglist_child_label ); ?>
		<?php endif; ?>
	</h2>

	<?php if ( ! empty( $maglist_child_category_link ) ) : ?>
		<a class="na-section__more" href="<?php echo esc_url( $maglist_child_category_link ); ?>">
			<?php esc_html_e( 'थप समाचार', 'maglist-child' ); ?> &#8599;
		</a>
	<?php endif; ?>
</div>
