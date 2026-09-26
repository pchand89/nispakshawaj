<?php
/**
 * Reporter guidance above the Categories panel in the post editor, with a
 * live check that one place and one topic category are ticked.
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Term IDs of the given category slugs plus all of their subcategories.
 *
 * @param string[] $slugs Category slugs.
 * @return int[]
 */
function maglist_child_category_ids_with_children( $slugs ) {
	$ids = array();
	foreach ( $slugs as $slug ) {
		$term = maglist_child_resolve_category( $slug );
		if ( ! $term ) {
			continue;
		}
		$ids[]    = (int) $term->term_id;
		$children = get_term_children( $term->term_id, 'category' );
		if ( ! is_wp_error( $children ) ) {
			$ids = array_merge( $ids, array_map( 'intval', $children ) );
		}
	}
	return array_values( array_unique( $ids ) );
}

/**
 * Load the guide in the block editor for posts only.
 */
function maglist_child_enqueue_editor_category_guide() {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'post' !== $screen->post_type ) {
		return;
	}

	wp_enqueue_script(
		'maglist-child-editor-category-guide',
		MAGLIST_CHILD_URI . '/assets/js/editor-category-guide.js',
		array( 'wp-hooks', 'wp-element', 'wp-data' ),
		MAGLIST_CHILD_VERSION,
		true
	);

	wp_localize_script(
		'maglist-child-editor-category-guide',
		'naCategoryGuide',
		array(
			'placeIds'  => maglist_child_category_ids_with_children( array( 'सुदूरपश्चिम', 'राष्ट्रिय', 'अन्तर्राष्ट्रिय' ) ),
			'topicIds'  => maglist_child_category_ids_with_children( array( 'राजनीति', 'समाज', 'अर्थ-कृषि', 'स्थानीय-तह-विकास', 'शिक्षा-साहित्य', 'खेलकुद', 'मनोरञ्जन', 'विविध' ) ),
			'uncatIds'  => maglist_child_category_ids_with_children( array( 'uncategorized' ) ),
		)
	);

	wp_register_style( 'maglist-child-editor-category-guide', false, array(), MAGLIST_CHILD_VERSION );
	wp_enqueue_style( 'maglist-child-editor-category-guide' );
	wp_add_inline_style(
		'maglist-child-editor-category-guide',
		'.na-cat-guide{margin:0 0 14px;padding:10px 12px;background:#f3f6fc;border-left:3px solid #003893;border-radius:2px;font-size:12.5px;line-height:1.6;color:#1e1e1e}
		.na-cat-guide__title{margin:0 0 4px;font-weight:700;font-size:13px;color:#003893}
		.na-cat-guide__rule{margin:0 0 6px}
		.na-cat-guide ul{margin:0 0 8px;padding-left:16px;list-style:disc}
		.na-cat-guide li{margin:0 0 3px}
		.na-cat-guide__status{display:flex;flex-wrap:wrap;gap:6px}
		.na-cat-guide__pill{padding:1px 8px;border-radius:10px;font-weight:600;font-size:12px}
		.na-cat-guide__pill.is-ok{background:#e3f4e8;color:#1a6b34}
		.na-cat-guide__pill.is-missing{background:#fde7e9;color:#bf1e2e}'
	);
}
add_action( 'enqueue_block_editor_assets', 'maglist_child_enqueue_editor_category_guide' );
