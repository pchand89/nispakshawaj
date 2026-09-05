<?php
/**
 * Single-post ad slots: labelled slot markup plus automatic in-content
 * injection between article paragraphs (Ratopati / Onlinekhabar pattern).
 *
 * Every slot is a widget area, so ad code goes in via Appearance > Widgets
 * (Custom HTML / ad plugin widget), and each keeps a stable DOM id so Ad
 * Inserter HTML-element blocks can target it too.
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget-area IDs added for single posts.
 *
 * @return string[]
 */
function maglist_child_single_ad_areas() {
	return array(
		'single-below-title',
		'single-in-content-1',
		'single-in-content-2',
		'single-in-content-3',
		'single-after-content',
		'single-before-related',
		'sidebar-ad-2',
	);
}

/**
 * Let the single-post slots take part in the server-rendered Ad Inserter map,
 * same as the homepage/archive rails.
 *
 * @param array<string, int> $map Area ID => block number.
 * @return array<string, int>
 */
function maglist_child_single_ad_inserter_map( $map ) {
	$block = maglist_child_sidebar_ad_inserter_block();

	foreach ( maglist_child_single_ad_areas() as $area ) {
		if ( ! isset( $map[ $area ] ) ) {
			$map[ $area ] = $block;
		}
	}

	return $map;
}
add_filter( 'maglist_child_sidebar_ad_inserter_map', 'maglist_child_single_ad_inserter_map' );

/**
 * Small "विज्ञापन" caption printed above a filled ad slot.
 *
 * @return string
 */
function maglist_child_ad_slot_label() {
	return (string) apply_filters( 'maglist_child_ad_slot_label', __( 'विज्ञापन', 'maglist-child' ) );
}

/**
 * Ad slot markup: wrapper anchor + label (only when the slot has a creative).
 *
 * The wrapper is always returned so Ad Inserter's client-side HTML-element
 * insertion has something to target; empty wrappers stay invisible through
 * `.na-ad-slot:empty`.
 *
 * @param string $sidebar_id    Registered widget-area ID.
 * @param string $wrapper_class Class(es) for the wrapper.
 * @param bool   $show_label    Print the "विज्ञापन" caption.
 * @return string
 */
function maglist_child_get_ad_slot_html( $sidebar_id, $wrapper_class = 'na-ad-slot', $show_label = true ) {
	$inner = maglist_child_get_sidebar_ad_inner_html( $sidebar_id );
	$label = '';

	if ( $show_label && maglist_child_html_has_content( $inner ) ) {
		$label = '<span class="na-ad-slot__label">' . esc_html( maglist_child_ad_slot_label() ) . '</span>';
	}

	return sprintf(
		'<div class="%1$s" id="%2$s">%3$s%4$s</div>',
		esc_attr( $wrapper_class ),
		esc_attr( $sidebar_id ),
		$label,
		$inner
	);
}

/**
 * Echo an ad slot.
 *
 * @param string $sidebar_id    Registered widget-area ID.
 * @param string $wrapper_class Class(es) for the wrapper.
 * @param bool   $show_label    Print the "विज्ञापन" caption.
 */
function maglist_child_ad_slot( $sidebar_id, $wrapper_class = 'na-ad-slot', $show_label = true ) {
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Ad/widget HTML is intentional.
	echo maglist_child_get_ad_slot_html( $sidebar_id, $wrapper_class, $show_label );
}

/**
 * In-content slots and the paragraph each one follows.
 *
 * Example: `'single-in-content-1' => 2` renders after the 2nd paragraph.
 * A slot is skipped when the story is too short to keep at least one
 * paragraph of text below the ad.
 *
 * @return array<string, int>
 */
function maglist_child_single_content_ad_positions() {
	$positions = array(
		'single-in-content-1' => 2,
		'single-in-content-2' => 6,
		'single-in-content-3' => 11,
	);

	/**
	 * Filter in-content ad placement.
	 *
	 * @param array<string, int> $positions Area ID => paragraph number.
	 */
	return (array) apply_filters( 'maglist_child_single_content_ad_positions', $positions );
}

/**
 * Top-level paragraphs of the post body: inner HTML plus the byte offset just
 * after each closing `</p>`.
 *
 * Paragraphs nested inside a quote, figure, list, or table are ignored so an
 * ad can never land in the middle of one of those. `div` is deliberately not
 * tracked because the parent theme wraps the whole body in `div.post-content`.
 *
 * @param string $content Post content (after wpautop).
 * @return array<int, array{end:int, html:string}>
 */
function maglist_child_content_paragraphs( $content ) {
	$paragraphs = array();
	$pattern    = '#<(/?)(p|blockquote|figure|table|ul|ol|dl|pre|aside|form|details)\b[^>]*?(/?)>#i';

	if ( ! preg_match_all( $pattern, $content, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER ) ) {
		return $paragraphs;
	}

	$depth      = 0;
	$inner_from = null;

	foreach ( $matches as $match ) {
		if ( '/' === $match[3][0] ) {
			continue; // Self-closing, opens nothing.
		}

		$is_closing = '/' === $match[1][0];
		$tag        = strtolower( $match[2][0] );

		if ( 'p' === $tag ) {
			if ( ! $is_closing ) {
				$inner_from = ( 0 === $depth ) ? $match[0][1] + strlen( $match[0][0] ) : null;
				continue;
			}

			if ( 0 === $depth && null !== $inner_from ) {
				$paragraphs[] = array(
					'end'  => $match[0][1] + strlen( $match[0][0] ),
					'html' => substr( $content, $inner_from, $match[0][1] - $inner_from ),
				);
			}

			$inner_from = null;
			continue;
		}

		$depth = $is_closing ? max( 0, $depth - 1 ) : $depth + 1;
	}

	return $paragraphs;
}

/**
 * Whether a paragraph reads as a sub-heading — a short line that is entirely
 * bold, or one ending in a colon — i.e. a label for the block below it.
 *
 * Putting an ad straight after one of these would separate the label from what
 * it introduces, so those breaks get skipped.
 *
 * @param string $html Paragraph inner HTML.
 * @return bool
 */
function maglist_child_paragraph_is_subheading( $html ) {
	$text = trim( wp_strip_all_tags( $html ) );

	if ( '' === $text ) {
		return true;
	}

	if ( mb_strlen( $text ) > 120 ) {
		return false;
	}

	if ( preg_match( '#^\s*<(strong|b|em)\b[^>]*>.*</\1>\s*$#is', trim( $html ) ) ) {
		return true;
	}

	return (bool) preg_match( '/[:：]$/u', $text );
}

/**
 * Pick the paragraph an ad should follow, nudging past unsuitable breaks.
 *
 * @param array<int, array{end:int, html:string}> $paragraphs Body paragraphs.
 * @param int                                     $after      Requested paragraph number.
 * @param array<int, bool>                        $taken      Paragraph numbers already used.
 * @return int|null 1-based paragraph number, or null when there is no room.
 */
function maglist_child_resolve_content_ad_break( $paragraphs, $after, $taken ) {
	$total = count( $paragraphs );

	// Look a few paragraphs ahead, then give up rather than drift far off.
	for ( $shift = 0; $shift <= 3; $shift++ ) {
		$index = $after + $shift;

		// Needs a paragraph before AND after the ad.
		if ( $index < 1 || $index >= $total ) {
			return null;
		}

		if ( isset( $taken[ $index ] ) ) {
			continue;
		}

		if ( maglist_child_paragraph_is_subheading( $paragraphs[ $index - 1 ]['html'] ) ) {
			continue;
		}

		return $index;
	}

	return null;
}

/**
 * Drop the in-content ad slots between paragraphs of a single post.
 *
 * Runs at priority 20 so it sees paragraphs created by `wpautop` (priority 10).
 *
 * @param string $content Post content.
 * @return string
 */
function maglist_child_inject_single_content_ads( $content ) {
	static $running = false;

	if ( $running || ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	if ( post_password_required() ) {
		return $content;
	}

	$positions = maglist_child_single_content_ad_positions();
	if ( empty( $positions ) ) {
		return $content;
	}

	$paragraphs = maglist_child_content_paragraphs( $content );
	if ( count( $paragraphs ) < 2 ) {
		return $content;
	}

	// Widgets in a slot could run `the_content` themselves; don't re-enter.
	$running = true;

	$insertions = array();
	$taken      = array();

	foreach ( $positions as $area => $after_paragraph ) {
		$index = maglist_child_resolve_content_ad_break( $paragraphs, absint( $after_paragraph ), $taken );
		if ( null === $index ) {
			continue;
		}

		$taken[ $index ]                                = true;
		$insertions[ $paragraphs[ $index - 1 ]['end'] ] = maglist_child_get_ad_slot_html( $area, 'na-ad-slot na-ad-inline' );
	}

	$running = false;

	// Splice from the bottom up so earlier offsets stay valid.
	krsort( $insertions, SORT_NUMERIC );
	foreach ( $insertions as $offset => $html ) {
		$content = substr( $content, 0, $offset ) . $html . substr( $content, $offset );
	}

	return $content;
}
add_filter( 'the_content', 'maglist_child_inject_single_content_ads', 20 );
