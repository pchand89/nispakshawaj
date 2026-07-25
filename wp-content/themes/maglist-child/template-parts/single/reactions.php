<?php
/**
 * “How did you feel?” reaction strip (DB-backed counts).
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reactions = array(
	'happy'    => array( 'emoji' => '😊', 'label' => __( 'खुशी', 'maglist-child' ) ),
	'sad'      => array( 'emoji' => '😢', 'label' => __( 'दुखी', 'maglist-child' ) ),
	'angry'    => array( 'emoji' => '😡', 'label' => __( 'रिस', 'maglist-child' ) ),
	'surprise' => array( 'emoji' => '😮', 'label' => __( 'अचम्म', 'maglist-child' ) ),
	'love'     => array( 'emoji' => '❤️', 'label' => __( 'मनपर्‍यो', 'maglist-child' ) ),
);

$counts   = maglist_child_get_reaction_counts( get_the_ID() );
$selected = (string) get_transient( maglist_child_reaction_vote_transient_key( get_the_ID() ) );
if ( $selected && ! isset( $reactions[ $selected ] ) ) {
	$selected = '';
}
?>
<section
	class="na-single-reactions"
	data-na-reactions
	data-na-post-id="<?php the_ID(); ?>"
	data-na-selected="<?php echo esc_attr( $selected ); ?>"
	aria-label="<?php echo esc_attr__( 'प्रतिक्रिया', 'maglist-child' ); ?>"
>
	<h2 class="na-single-reactions__title"><?php esc_html_e( 'खबर पढेर तपाईलाई कस्तो महसुस भयो ?', 'maglist-child' ); ?></h2>
	<div class="na-single-reactions__list" role="group">
		<?php foreach ( $reactions as $key => $reaction ) : ?>
			<?php
			$count  = isset( $counts[ $key ] ) ? (int) $counts[ $key ] : 0;
			$active = ( $selected === $key );
			?>
			<button
				type="button"
				class="na-single-reactions__btn<?php echo $active ? ' is-active' : ''; ?>"
				data-na-reaction="<?php echo esc_attr( $key ); ?>"
				aria-pressed="<?php echo $active ? 'true' : 'false'; ?>"
			>
				<span class="na-single-reactions__emoji" aria-hidden="true"><?php echo esc_html( $reaction['emoji'] ); ?></span>
				<span class="na-single-reactions__label"><?php echo esc_html( $reaction['label'] ); ?></span>
				<span class="na-single-reactions__count" data-na-reaction-count><?php echo esc_html( (string) $count ); ?></span>
			</button>
		<?php endforeach; ?>
	</div>
</section>
