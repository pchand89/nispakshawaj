<?php
/**
 * Shared author + date/time meta (avatar, calendar, clock).
 *
 * Optional $args:
 *   @type string $modifier   Extra BEM modifier class (e.g. 'na-post-meta--breaking').
 *   @type int    $avatar     Avatar size in px (default 32).
 *   @type bool   $show_time  Whether to append clock time (default true).
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$modifier    = isset( $args['modifier'] ) ? (string) $args['modifier'] : '';
$avatar_px   = isset( $args['avatar'] ) ? absint( $args['avatar'] ) : 32;
$avatar_px   = $avatar_px > 0 ? $avatar_px : 32;
$show_time   = ! isset( $args['show_time'] ) || (bool) $args['show_time'];
$author_id   = (int) get_the_author_meta( 'ID' );
$author_name = get_the_author();
$author_url  = get_author_posts_url( $author_id );
$date_label  = maglist_child_single_date( get_the_ID() );
$time_label  = $show_time ? maglist_child_single_time( get_the_ID() ) : '';
$classes     = trim( 'na-post-meta ' . $modifier );
?>
<div class="<?php echo esc_attr( $classes ); ?>">
	<a class="na-post-meta__author" href="<?php echo esc_url( $author_url ); ?>">
		<span class="na-post-meta__avatar" aria-hidden="true">
			<?php
			echo get_avatar(
				$author_id,
				$avatar_px,
				'',
				$author_name,
				array(
					'class' => 'na-post-meta__avatar-img',
				)
			);
			?>
		</span>
		<span class="na-post-meta__name"><?php echo esc_html( $author_name ); ?></span>
	</a>

	<?php if ( $date_label || $time_label ) : ?>
		<span class="na-post-meta__when">
			<time class="na-post-meta__date" datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
				<i class="fa fa-calendar" aria-hidden="true"></i>
				<span>
					<?php
					echo esc_html(
						$time_label
							? trim( $date_label . ', ' . $time_label )
							: $date_label
					);
					?>
				</span>
			</time>
		</span>
	<?php endif; ?>
</div>
