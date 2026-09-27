<?php
/**
 * Author archive profile: photo, role, name, story count, and bio.
 *
 * @package Maglist_Child
 *
 * @var array $args {
 *   @type WP_User $author Author being viewed.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$author = isset( $args['author'] ) ? $args['author'] : null;
if ( ! $author instanceof WP_User ) {
	return;
}

$author_id = (int) $author->ID;
$bio       = trim( (string) get_user_meta( $author_id, 'description', true ) );
$count     = count_user_posts( $author_id, 'post', true );
$role      = maglist_child_author_role_label( $author );
?>
<div class="na-author">
	<div class="na-author__avatar">
		<?php
		echo get_avatar(
			$author_id,
			168,
			'',
			$author->display_name,
			array( 'class' => 'na-author__avatar-img' )
		);
		?>
	</div>
	<div class="na-author__body">
		<?php if ( $role ) : ?>
			<p class="na-author__role"><?php echo esc_html( $role ); ?></p>
		<?php endif; ?>
		<h1 class="na-cat__title na-author__name"><?php echo esc_html( $author->display_name ); ?></h1>
		<p class="na-author__count">
			<?php
			echo esc_html(
				maglist_child_to_nepali_digits( (string) $count ) . ' ' . __( 'समाचार', 'maglist-child' )
			);
			?>
		</p>
		<?php if ( $bio !== '' ) : ?>
			<p class="na-author__bio"><?php echo esc_html( $bio ); ?></p>
		<?php endif; ?>
	</div>
</div>
