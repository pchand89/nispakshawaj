<?php
/**
 * Single post author + date meta row.
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$author_id   = (int) get_the_author_meta( 'ID' );
$author_name = get_the_author();
$author_url  = get_author_posts_url( $author_id );
$datetime    = maglist_child_single_datetime( get_the_ID() );
?>
<div class="na-single-meta">
	<a class="na-single-meta__author" href="<?php echo esc_url( $author_url ); ?>">
		<span class="na-single-meta__avatar">
			<?php echo get_avatar( $author_id, 40, '', $author_name ); ?>
		</span>
		<span class="na-single-meta__name"><?php echo esc_html( $author_name ); ?></span>
	</a>
	<?php if ( $datetime ) : ?>
		<time class="na-single-meta__date" datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
			<?php echo esc_html( $datetime ); ?>
		</time>
	<?php endif; ?>
</div>
