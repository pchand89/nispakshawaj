<?php
/**
 * Single post share controls — brand icon buttons + Maglist sticky rail.
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$permalink = get_permalink();
$title     = wp_strip_all_tags( get_the_title() );

/**
 * Official X (formerly Twitter) mark — Font Awesome 4 only has the old bird.
 *
 * @return string
 */
$x_icon = static function () {
	return '<svg class="na-share-x" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path fill="currentColor" d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>';
};
?>
<?php
$share_counts = function_exists( 'maglist_child_get_share_counts' )
	? maglist_child_get_share_counts( get_the_ID() )
	: array( 'total' => 0 );
$share_total  = isset( $share_counts['total'] ) ? (int) $share_counts['total'] : 0;
$share_total_label = function_exists( 'maglist_child_to_nepali_digits' )
	? maglist_child_to_nepali_digits( (string) $share_total )
	: (string) $share_total;
?>
<div class="na-single-share" role="group" aria-label="<?php echo esc_attr__( 'सेयर गर्नुहोस्', 'maglist-child' ); ?>" data-na-share-total="<?php echo esc_attr( (string) $share_total ); ?>">
	<?php if ( $share_total > 0 ) : ?>
		<span class="na-single-share__total">
			<span data-na-share-total-label><?php echo esc_html( $share_total_label ); ?></span>
			<?php esc_html_e( 'सेयर', 'maglist-child' ); ?>
		</span>
	<?php else : ?>
		<span class="na-single-share__total is-empty" hidden>
			<span data-na-share-total-label>०</span>
			<?php esc_html_e( 'सेयर', 'maglist-child' ); ?>
		</span>
	<?php endif; ?>
	<a class="na-single-share__btn na-single-share__btn--fb" href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink(); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo esc_attr__( 'Facebook', 'maglist-child' ); ?>" data-na-share="facebook">
		<i class="fa fa-facebook" aria-hidden="true"></i>
		<span class="screen-reader-text"><?php esc_html_e( 'Facebook', 'maglist-child' ); ?></span>
	</a>
	<a class="na-single-share__btn na-single-share__btn--x" href="https://x.com/intent/tweet?url=<?php the_permalink(); ?>&amp;text=<?php echo rawurlencode( $title ); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo esc_attr__( 'X', 'maglist-child' ); ?>" data-na-share="x">
		<?php echo $x_icon(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<span class="screen-reader-text"><?php esc_html_e( 'X', 'maglist-child' ); ?></span>
	</a>
	<a class="na-single-share__btn na-single-share__btn--wa" href="https://api.whatsapp.com/send?text=<?php echo rawurlencode( $title . ' ' . $permalink ); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo esc_attr__( 'WhatsApp', 'maglist-child' ); ?>" data-na-share="whatsapp">
		<i class="fa fa-whatsapp" aria-hidden="true"></i>
		<span class="screen-reader-text"><?php esc_html_e( 'WhatsApp', 'maglist-child' ); ?></span>
	</a>
	<a class="na-single-share__btn na-single-share__btn--li" href="https://www.linkedin.com/shareArticle?mini=true&amp;url=<?php the_permalink(); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo esc_attr__( 'LinkedIn', 'maglist-child' ); ?>" data-na-share="linkedin">
		<i class="fa fa-linkedin" aria-hidden="true"></i>
		<span class="screen-reader-text"><?php esc_html_e( 'LinkedIn', 'maglist-child' ); ?></span>
	</a>
	<a class="na-single-share__btn na-single-share__btn--mail" href="mailto:?subject=<?php echo rawurlencode( $title ); ?>&amp;body=<?php echo rawurlencode( $permalink ); ?>" title="<?php echo esc_attr__( 'Email', 'maglist-child' ); ?>" data-na-share="email">
		<i class="fa fa-envelope" aria-hidden="true"></i>
		<span class="screen-reader-text"><?php esc_html_e( 'Email', 'maglist-child' ); ?></span>
	</a>
	<button type="button" class="na-single-share__btn na-single-share__btn--copy" data-na-copy-link="<?php echo esc_attr( $permalink ); ?>" data-na-share="copy" title="<?php echo esc_attr__( 'लिंक कपी', 'maglist-child' ); ?>">
		<i class="fa fa-link" aria-hidden="true"></i>
		<span class="screen-reader-text"><?php esc_html_e( 'लिंक कपी', 'maglist-child' ); ?></span>
	</button>
</div>

<div class="maglist-sticky-share">
	<h3><?php esc_html_e( 'Share Article:', 'maglist' ); ?></h3>
	<ul>
		<li>
			<a class="na-share-icon na-share-icon--mail" href="mailto:?subject=<?php echo rawurlencode( $title ); ?>&amp;body=<?php echo rawurlencode( $permalink ); ?>" aria-label="<?php echo esc_attr__( 'Email', 'maglist-child' ); ?>" title="<?php echo esc_attr__( 'Email', 'maglist-child' ); ?>" data-na-share="email">
				<i class="fa fa-envelope" aria-hidden="true"></i>
			</a>
		</li>
		<li>
			<a class="na-share-icon na-share-icon--li" href="https://www.linkedin.com/shareArticle?mini=true&amp;url=<?php the_permalink(); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr__( 'LinkedIn', 'maglist-child' ); ?>" title="<?php echo esc_attr__( 'LinkedIn', 'maglist-child' ); ?>" data-na-share="linkedin">
				<i class="fa fa-linkedin" aria-hidden="true"></i>
			</a>
		</li>
		<li>
			<a class="na-share-icon na-share-icon--fb" href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink(); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr__( 'Facebook', 'maglist-child' ); ?>" title="<?php echo esc_attr__( 'Facebook', 'maglist-child' ); ?>" data-na-share="facebook">
				<i class="fa fa-facebook" aria-hidden="true"></i>
			</a>
		</li>
		<li>
			<a class="na-share-icon na-share-icon--x" href="https://x.com/intent/tweet?text=<?php echo rawurlencode( $title ); ?>&amp;url=<?php the_permalink(); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr__( 'X', 'maglist-child' ); ?>" title="<?php echo esc_attr__( 'X', 'maglist-child' ); ?>" data-na-share="x">
				<?php echo $x_icon(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
		</li>
		<li>
			<a class="na-share-icon na-share-icon--wa" href="https://api.whatsapp.com/send?text=<?php echo rawurlencode( $title . ' ' . $permalink ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr__( 'WhatsApp', 'maglist-child' ); ?>" title="<?php echo esc_attr__( 'WhatsApp', 'maglist-child' ); ?>" data-na-share="whatsapp">
				<i class="fa fa-whatsapp" aria-hidden="true"></i>
			</a>
		</li>
	</ul>
</div>
