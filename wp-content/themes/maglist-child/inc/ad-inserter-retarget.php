<?php
/**
 * One-time retarget of Ad Inserter HTML selectors away from removed Maglist
 * banner markup onto the child theme ad-slot anchors, and repair sidebar-ad
 * insertion flags so the ad fires on homepage, category, and single templates.
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Map of old Ad Inserter CSS selectors → new child-theme anchors.
 *
 * @return array<string, string>
 */
function maglist_child_ad_inserter_selector_map() {
	return array(
		'body > div.maglist-main-banner-wrapper'                          => '#home-above-header',
		'body &gt; div.maglist-main-banner-wrapper'                       => '#home-above-header',
		'body > section.maglist-latest-post-wrapper'                      => '#home-mid-grid-ad-1',
		'body &gt; section.maglist-latest-post-wrapper'                   => '#home-mid-grid-ad-1',
		'body > div#content > div:nth-child(1) > section#text-8'          => '#sidebar-ad',
		'body &gt; div#content &gt; div:nth-child(1) &gt; section#text-8' => '#sidebar-ad',
	);
}

/**
 * Whether a block targets the sidebar-ad slot (current or legacy selector).
 *
 * @param array $block Ad Inserter block settings.
 * @return bool
 */
function maglist_child_ad_inserter_is_sidebar_block( $block ) {
	$selector = isset( $block['html_selector'] ) ? html_entity_decode( (string) $block['html_selector'], ENT_QUOTES, 'UTF-8' ) : '';

	if ( '#sidebar-ad' === $selector ) {
		return true;
	}

	return false !== strpos( $selector, 'section#text-8' );
}

/**
 * Point legacy Maglist sidebar selectors at #sidebar-ad only.
 *
 * Does not re-enable paused blocks or force PHP-call / display flags — those
 * stay under Ad Inserter admin control so disabling an ad survives theme updates.
 *
 * @param array $block Block settings.
 * @return array{0:array,1:bool}
 */
function maglist_child_ad_inserter_fix_sidebar_block( $block ) {
	$changed  = false;
	$selector = isset( $block['html_selector'] ) ? html_entity_decode( (string) $block['html_selector'], ENT_QUOTES, 'UTF-8' ) : '';

	if ( '#sidebar-ad' !== $selector ) {
		$block['html_selector'] = '#sidebar-ad';
		$changed                = true;
	}

	return array( $block, $changed );
}

/**
 * Replace known Maglist selectors and repair sidebar insertion flags.
 *
 * @param mixed $settings Decoded ad_inserter option (array of blocks).
 * @return array{0:mixed,1:bool} Updated settings and whether anything changed.
 */
function maglist_child_ad_inserter_retarget_settings( $settings ) {
	if ( ! is_array( $settings ) ) {
		return array( $settings, false );
	}

	$map     = maglist_child_ad_inserter_selector_map();
	$changed = false;

	foreach ( $settings as $key => $block ) {
		if ( ! is_array( $block ) ) {
			continue;
		}

		foreach ( array( 'html_selector', 'wait_for' ) as $field ) {
			if ( empty( $block[ $field ] ) || ! is_string( $block[ $field ] ) ) {
				continue;
			}

			$current = $block[ $field ];
			$decoded = html_entity_decode( $current, ENT_QUOTES, 'UTF-8' );

			if ( isset( $map[ $current ] ) ) {
				$settings[ $key ][ $field ] = $map[ $current ];
				$block[ $field ]            = $map[ $current ];
				$changed                    = true;
			} elseif ( isset( $map[ $decoded ] ) ) {
				$settings[ $key ][ $field ] = $map[ $decoded ];
				$block[ $field ]            = $map[ $decoded ];
				$changed                    = true;
			}
		}

		if ( maglist_child_ad_inserter_is_sidebar_block( $block ) ) {
			list( $block, $block_changed ) = maglist_child_ad_inserter_fix_sidebar_block( $block );
			$settings[ $key ]              = $block;
			if ( $block_changed ) {
				$changed = true;
			}
		}
	}

	return array( $settings, $changed );
}

/**
 * Run once until the option flag is set for this theme version.
 */
function maglist_child_maybe_retarget_ad_inserter() {
	if ( get_option( 'maglist_child_ad_inserter_retargeted', '' ) === MAGLIST_CHILD_VERSION ) {
		return;
	}

	$option_name = defined( 'AI_OPTION_NAME' ) ? AI_OPTION_NAME : 'ad_inserter';
	$raw         = get_option( $option_name );

	if ( false === $raw ) {
		update_option( 'maglist_child_ad_inserter_retargeted', MAGLIST_CHILD_VERSION, false );
		return;
	}

	if ( function_exists( 'ai_get_option' ) ) {
		$settings = ai_get_option( $option_name, array() );
	} elseif ( is_string( $raw ) && 0 === strpos( $raw, ':AI:' ) ) {
		$decoded  = base64_decode( substr( $raw, 4 ), true );
		$settings = ( false !== $decoded ) ? @unserialize( $decoded ) : false; // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize,WordPress.PHP.NoSilencedErrors.Discouraged
	} else {
		$settings = $raw;
	}

	list( $settings, $changed ) = maglist_child_ad_inserter_retarget_settings( $settings );

	if ( $changed && is_array( $settings ) ) {
		if ( function_exists( 'ai_update_option' ) ) {
			ai_update_option( $option_name, $settings );
		} else {
			update_option( $option_name, ':AI:' . base64_encode( serialize( $settings ) ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
		}
	}

	update_option( 'maglist_child_ad_inserter_retargeted', MAGLIST_CHILD_VERSION, false );
}
add_action( 'init', 'maglist_child_maybe_retarget_ad_inserter', 30 );
