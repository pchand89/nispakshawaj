<?php
/**
 * Author about box.
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$author_id   = (int) get_the_author_meta( 'ID' );
$author_name = get_the_author();
$author_url  = get_author_posts_url( $author_id );
$bio         = get_the_author_meta( 'description', $author_id );
?>
<section class="na-single-author" aria-label="<?php echo esc_attr__( 'लेखकको बारेमा', 'maglist-child' ); ?>">
	<h2 class="na-single-author__heading"><?php esc_html_e( 'लेखकको बारेमा', 'maglist-child' ); ?></h2>
	<div class="na-single-author__card">
		<a class="na-single-author__avatar" href="<?php echo esc_url( $author_url ); ?>">
			<?php echo get_avatar( $author_id, 72, '', $author_name ); ?>
		</a>
		<div class="na-single-author__body">
			<h3 class="na-single-author__name">
				<a href="<?php echo esc_url( $author_url ); ?>"><?php echo esc_html( $author_name ); ?></a>
			</h3>
			<?php if ( $bio ) : ?>
				<p class="na-single-author__bio"><?php echo esc_html( $bio ); ?></p>
			<?php endif; ?>
			<a class="na-single-author__more" href="<?php echo esc_url( $author_url ); ?>">
				<?php esc_html_e( 'लेखकबाट थप', 'maglist-child' ); ?>
			</a>
		</div>
	</div>
</section>
