<?php
/**
 * Single post author + date meta row.
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_template_part(
	'template-parts/common/post-meta',
	null,
	array(
		'modifier' => 'na-post-meta--single',
		'avatar'   => 32,
	)
);
