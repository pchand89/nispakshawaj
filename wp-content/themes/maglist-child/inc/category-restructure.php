<?php
/**
 * One-time category restructure for the local-first menu.
 *
 * - Merges duplicate categories (राजनिती/राजनीति, खेलकुद-2, the "समाचार"
 *   children under शिक्षा and स्वास्थ्य, …) and fixes wrong parents.
 * - Adds place categories (सुदूरपश्चिम + its districts incl. कञ्चनपुर, राष्ट्रिय,
 *   अन्तर्राष्ट्रिय) and tags existing posts from their dateline
 *   ("कञ्चनपुर, २९ असार –", "काठमाडौं, जेठ १९ –").
 * - Builds a new primary menu; the old menu is left untouched for rollback.
 * - 301-redirects slugs that disappeared in a merge/rename.
 *
 * Terms are looked up by slug (not ID) so the same plan runs on local and live.
 * Run from Tools → Category restructure, or:
 *   wp eval 'echo implode( "\n", maglist_child_category_restructure( false ) );'
 * (pass true to apply). Safe to re-run.
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MAGLIST_CHILD_RESTRUCTURE_MENU', 'मुख्य मेनु (स्थानीय)' );

/**
 * The restructure plan. Slugs are Unicode; each target lists fallbacks so a
 * second run still finds terms that were renamed by the first.
 *
 * @return array
 */
function maglist_child_category_plan() {
	$districts = array( 'कञ्चनपुर', 'कैलाली', 'डडेल्धुरा', 'बैतडी', 'दार्चुला', 'डोटी', 'अछाम', 'बझाङ', 'बाजुरा' );

	$create = array(
		'सुदूरपश्चिम' => array( 'सुदूरपश्चिम', '' ),
		'राष्ट्रिय'   => array( 'राष्ट्रिय', '' ),
		'अर्थ-कृषि'   => array( 'अर्थ/कृषि', '' ),
	);
	foreach ( $districts as $district ) {
		$create[ $district ] = array( $district, 'सुदूरपश्चिम' );
	}

	return array(
		'districts' => $districts,
		'create'    => $create,

		// Target key => candidate slugs (first found wins).
		'targets'   => array(
			'politics'      => array( 'राजनिती', 'राजनीति' ),
			'international' => array( 'विदेश-कूटनीति', 'अन्तर्राष्ट्रिय' ),
		),

		// Source slug => target slug or target key. Source posts get the target.
		'merge'     => array(
			'राजनीति'                   => 'politics',
			'राजनिती-2'                 => 'politics',
			'खेलकुद-2'                  => 'खेलकुद',
			'शिक्षा'                    => 'शिक्षा-साहित्य',
			'समाचार-शिक्षा-साहित्य'     => 'शिक्षा-साहित्य',
			'समस्या-समाधान-शिक्षा-साह'  => 'शिक्षा-साहित्य',
			'समाचार-स्वास्थ्य-विज्ञा'   => 'स्वास्थ्य-विज्ञान-र-प्रव',
			'अपराध'                     => 'समाज',
			'चलचित्र-जगत'               => 'मनोरञ्जन',
		),

		// Slug or target key => array( new name, new slug|null ).
		'rename'    => array(
			'politics'          => array( 'राजनीति', 'राजनीति' ),
			'international'     => array( 'अन्तर्राष्ट्रिय', 'अन्तर्राष्ट्रिय' ),
			'स्थानीय-तह-विकास' => array( 'स्थानीय तह/विकास', null ),
		),

		// Slug or target key => parent slug ('' = top level).
		'parent'    => array(
			'समाचार'                   => '',
			'politics'                 => '',
			'international'            => '',
			'खेलकुद'                   => '',
			'कञ्चनपुर'                 => 'सुदूरपश्चिम',
			'स्वास्थ्य-विज्ञान-र-प्रव' => 'समाज',
			'समस्या-समाधान'            => 'समाज',
			'व्यवसाय'                  => 'अर्थ-कृषि',
			'कृषि'                     => 'अर्थ-कृषि',
			'निर्माण'                  => 'स्थानीय-तह-विकास',
			'पर्यटन'                   => 'स्थानीय-तह-विकास',
			'अन्य'                     => 'मनोरञ्जन',
			'साहित्यिक-रचनाहरू'        => 'शिक्षा-साहित्य',
		),

		// Category => other category: drop the first from posts that have the second.
		// खेलकुद used to sit under मनोरञ्जन (and राजनीति under स्थानीय तह), so
		// editors ticked both on sports / political posts.
		'untag_if'  => array(
			'मनोरञ्जन'          => 'खेलकुद',
			'स्थानीय-तह-विकास' => 'राजनीति',
		),

		// Place slug => words that identify it in a dateline or title.
		'places'    => array(
			'कञ्चनपुर'    => array( 'कञ्चनपुर', 'कंचनपुर', 'कञचनपुर', 'महेन्द्रनगर', 'भीमदत्त', 'शुक्लाफाँटा', 'बेलौरी', 'पुनर्वास', 'कृष्णपुर', 'बेदकोट', 'वेदकोट', 'लालझाडी', 'बेलडाँडी', 'झलारी', 'दोधारा' ),
			'कैलाली'      => array( 'कैलाली', 'धनगढी', 'टीकापुर', 'टिकापुर', 'लम्की', 'अत्तरिया', 'गोदावरी' ),
			'डडेल्धुरा'   => array( 'डडेल्धुरा', 'अमरगढी' ),
			'बैतडी'       => array( 'बैतडी', 'दशरथचन्द', 'गोठालापानी' ),
			'दार्चुला'    => array( 'दार्चुला', 'खलंगा' ),
			'डोटी'        => array( 'डोटी', 'दिपायल', 'सिलगढी' ),
			'अछाम'        => array( 'अछाम', 'मंगलसेन' ),
			'बझाङ'        => array( 'बझाङ', 'चैनपुर' ),
			'बाजुरा'      => array( 'बाजुरा', 'मार्तडी' ),
			'सुदूरपश्चिम' => array( 'सुदूरपश्चिम', 'सुदुरपश्चिम' ),
			// Outside the province: datelines only, never titles.
			'राष्ट्रिय'       => array( 'काठमाडौं', 'काठमाडौँ', 'काठमाडौ', 'काठमाण्डौ', 'ललितपुर', 'भक्तपुर', 'पोखरा', 'बुटवल', 'नेपालगञ्ज', 'सुर्खेत', 'झापा', 'विराटनगर', 'जनकपुर', 'वीरगञ्ज', 'चितवन', 'दाङ', 'बाँके', 'बर्दिया' ),
			'अन्तर्राष्ट्रिय' => array( 'नयाँदिल्ली', 'नयाँ दिल्ली', 'दिल्ली', 'मुम्बई', 'थाईलेण्ड', 'थाइल्यान्ड', 'दुबई', 'वासिङ्टन', 'बेइजिङ', 'लन्डन', 'कतार' ),
		),

		// Menu: slug or target key => child slugs or keys. Empty categories are skipped.
		'menu'      => array(
			'समाचार'            => array( 'राष्ट्रिय', 'international', 'विविध' ),
			'सुदूरपश्चिम'       => $districts,
			'politics'          => array(),
			'स्थानीय-तह-विकास' => array( 'निर्माण', 'पर्यटन' ),
			'अर्थ-कृषि'         => array( 'व्यवसाय', 'कृषि' ),
			'समाज'              => array( 'स्वास्थ्य-विज्ञान-र-प्रव', 'समस्या-समाधान' ),
			'शिक्षा-साहित्य'    => array( 'साहित्यिक-रचनाहरू' ),
			'खेलकुद'            => array(),
			'मनोरञ्जन'          => array(),
			'भिडियो'            => array(),
		),
	);
}

/**
 * Resolve a plan reference (slug or target key) to a category term.
 *
 * @param string $ref  Slug or key from $plan['targets'].
 * @param array  $plan Plan.
 * @return WP_Term|false
 */
function maglist_child_plan_term( $ref, $plan ) {
	$slugs = isset( $plan['targets'][ $ref ] ) ? $plan['targets'][ $ref ] : array( $ref );
	foreach ( $slugs as $slug ) {
		$term = maglist_child_find_term_flexible( $slug, 'category' );
		if ( $term ) {
			return $term;
		}
	}
	return false;
}

/**
 * Place slug from a post's dateline ("कञ्चनपुर, २९ असार –"), or ''.
 *
 * @param string $content Post content.
 * @param array  $places  Place slug => keywords.
 * @return string
 */
function maglist_child_dateline_place( $content, $places ) {
	$text = html_entity_decode( wp_strip_all_tags( (string) $content ), ENT_QUOTES, 'UTF-8' );
	$text = trim( preg_replace( '/[\s\x{00A0}\x{200B}\x{FEFF}]+/u', ' ', $text ) );

	// The dateline is the short run of text before the first comma, dash, danda or digit.
	if ( preg_match( '/^([^,\-–—।0-9०-९]{2,30})[,\-–—।0-9०-९]/u', mb_substr( $text, 0, 60 ), $m ) ) {
		foreach ( $places as $slug => $words ) {
			foreach ( $words as $word ) {
				if ( false !== mb_strpos( $m[1], $word ) ) {
					return $slug;
				}
			}
		}
	}

	// Dateline glued to the text ("महेन्द्रनगरसुदूरपश्चिम प्रदेश …").
	foreach ( $places as $slug => $words ) {
		foreach ( $words as $word ) {
			if ( 0 === mb_strpos( $text, $word ) ) {
				return $slug;
			}
		}
	}

	// Date-first openings ("२१ पुस, २०८२ सञ्जय महर, महेन्द्रनगर …"): take the
	// place named first before the first danda.
	$opening = mb_substr( $text, 0, 70 );
	$danda   = mb_strpos( $opening, '।' );
	if ( false !== $danda ) {
		$opening = mb_substr( $opening, 0, $danda );
	}
	if ( ! preg_match( '/बैशाख|वैशाख|जेठ|असार|साउन|श्रावण|भदौ|असोज|कात्तिक|कार्तिक|मंसिर|मङ्सिर|पुस|पौष|माघ|फागुन|चैत/u', $opening ) ) {
		return '';
	}

	$best     = '';
	$best_pos = PHP_INT_MAX;
	foreach ( $places as $slug => $words ) {
		foreach ( $words as $word ) {
			$pos = mb_strpos( $opening, $word );
			if ( false !== $pos && $pos < $best_pos ) {
				$best     = (string) $slug;
				$best_pos = $pos;
			}
		}
	}
	return $best;
}

/**
 * Place slug when the title names exactly one place, or ''.
 *
 * @param string $title  Post title.
 * @param array  $places Place slug => keywords.
 * @return string
 */
function maglist_child_title_place( $title, $places ) {
	$found = array();
	foreach ( $places as $slug => $words ) {
		foreach ( $words as $word ) {
			if ( false !== mb_strpos( (string) $title, $word ) ) {
				$found[ $slug ] = true;
				break;
			}
		}
	}
	unset( $found['सुदूरपश्चिम'], $found['राष्ट्रिय'], $found['अन्तर्राष्ट्रिय'] );
	return 1 === count( $found ) ? (string) key( $found ) : '';
}

/**
 * Run (or preview) the restructure.
 *
 * @param bool $apply False = dry run.
 * @return string[] Log lines.
 */
function maglist_child_category_restructure( $apply = false ) {
	$plan = maglist_child_category_plan();
	$log  = array( $apply ? '=== APPLY ===' : '=== DRY RUN (nothing is changed) ===' );

	$redirects = (array) get_option( 'maglist_child_category_redirects', array() );

	// 1. Create missing categories.
	$log[] = '';
	$log[] = '1. New categories';
	foreach ( $plan['create'] as $slug => $spec ) {
		if ( maglist_child_find_term_flexible( $slug, 'category' ) ) {
			$log[] = "   exists: {$spec[0]}";
			continue;
		}
		$log[] = "   create: {$spec[0]}" . ( $spec[1] ? " (under {$spec[1]})" : '' );
		if ( $apply ) {
			$parent = $spec[1] ? maglist_child_find_term_flexible( $spec[1], 'category' ) : false;
			wp_insert_term(
				$spec[0],
				'category',
				array(
					'slug'   => $slug,
					'parent' => $parent ? (int) $parent->term_id : 0,
				)
			);
		}
	}

	// 2. Merge duplicates into their main category.
	$log[] = '';
	$log[] = '2. Merges';
	foreach ( $plan['merge'] as $source_slug => $target_ref ) {
		$source = maglist_child_find_term_flexible( $source_slug, 'category' );
		$target = maglist_child_plan_term( $target_ref, $plan );
		if ( ! $source || ! $target || (int) $source->term_id === (int) $target->term_id ) {
			$log[] = "   skip: {$source_slug} (already merged or missing)";
			continue;
		}
		$log[] = sprintf( '   %s #%d (%d posts) -> %s #%d', $source->name, $source->term_id, $source->count, $target->name, $target->term_id );
		if ( $apply ) {
			$redirects[ rawurldecode( $source->slug ) ] = (int) $target->term_id;
			wp_delete_term(
				$source->term_id,
				'category',
				array(
					'default'       => (int) $target->term_id,
					'force_default' => true,
				)
			);
		}
	}

	// 3. Renames.
	$log[] = '';
	$log[] = '3. Renames';
	foreach ( $plan['rename'] as $ref => $spec ) {
		$term = maglist_child_plan_term( $ref, $plan );
		if ( ! $term ) {
			$log[] = "   skip: {$ref} (missing)";
			continue;
		}
		$old_slug = rawurldecode( $term->slug );
		$args     = array();
		if ( $term->name !== $spec[0] ) {
			$args['name'] = $spec[0];
		}
		if ( $spec[1] && $old_slug !== $spec[1] ) {
			$args['slug'] = $spec[1];
		}
		if ( ! $args ) {
			$log[] = "   ok: {$term->name}";
			continue;
		}
		$log[] = sprintf( '   %s -> %s%s', $term->name, $spec[0], isset( $args['slug'] ) ? " (URL /{$old_slug}/ -> /{$spec[1]}/, redirected)" : '' );
		if ( $apply ) {
			if ( isset( $args['slug'] ) ) {
				$redirects[ $old_slug ] = (int) $term->term_id;
			}
			wp_update_term( $term->term_id, 'category', $args );
		}
	}

	// 4. Parents.
	$log[] = '';
	$log[] = '4. Parent changes';
	foreach ( $plan['parent'] as $ref => $parent_slug ) {
		$term = maglist_child_plan_term( $ref, $plan );
		if ( ! $term ) {
			$log[] = "   skip: {$ref} (missing)";
			continue;
		}
		$old    = $term->parent ? get_term( $term->parent, 'category' ) : null;
		$from   = $old instanceof WP_Term ? $old->name : 'top level';
		$parent = $parent_slug ? maglist_child_find_term_flexible( $parent_slug, 'category' ) : false;
		if ( $parent_slug && ! $parent ) {
			// Only possible in a dry run: the parent is created in step 1.
			$log[] = "   {$term->name}: {$from} -> {$parent_slug} (new)";
			continue;
		}
		$new_parent = $parent ? (int) $parent->term_id : 0;
		if ( (int) $term->parent === $new_parent ) {
			continue;
		}
		$log[] = sprintf( '   %s: %s -> %s', $term->name, $from, $parent ? $parent->name : 'top level' );
		if ( $apply ) {
			wp_update_term( $term->term_id, 'category', array( 'parent' => $new_parent ) );
		}
	}

	if ( $apply ) {
		update_option( 'maglist_child_category_redirects', $redirects, false );
	}

	// 5. Drop leftover parent tags.
	$log[] = '';
	$log[] = '5. Leftover tags';
	foreach ( $plan['untag_if'] as $drop_slug => $keep_slug ) {
		$drop = maglist_child_find_term_flexible( $drop_slug, 'category' );
		$keep = maglist_child_find_term_flexible( $keep_slug, 'category' );
		if ( ! $drop || ! $keep ) {
			continue;
		}
		$both = get_posts(
			array(
				'post_type'        => 'post',
				'post_status'      => 'any',
				'numberposts'      => -1,
				'fields'           => 'ids',
				'suppress_filters' => true,
				'category__and'    => array( (int) $drop->term_id, (int) $keep->term_id ),
			)
		);
		$log[] = sprintf( '   remove %s from %d posts that also have %s', $drop->name, count( $both ), $keep->name );
		if ( $apply ) {
			foreach ( $both as $post_id ) {
				wp_remove_object_terms( $post_id, (int) $drop->term_id, 'category' );
			}
		}
	}

	// 6. Tag posts with their place.
	$log[]  = '';
	$log[]  = '6. Place tagging (from dateline, else a title naming one place)';
	$counts = array();
	$none   = array();
	$ids    = get_posts(
		array(
			'post_type'        => 'post',
			'post_status'      => array( 'publish', 'future', 'draft', 'pending', 'private' ),
			'numberposts'      => -1,
			'fields'           => 'ids',
			'suppress_filters' => true,
		)
	);
	foreach ( $ids as $post_id ) {
		$post  = get_post( $post_id );
		$place = maglist_child_dateline_place( $post->post_content, $plan['places'] );
		$how   = 'dateline';
		if ( '' === $place ) {
			$place = maglist_child_title_place( $post->post_title, $plan['places'] );
			$how   = 'title';
		}
		if ( '' === $place ) {
			$none[] = $post_id;
			continue;
		}
		$key = "{$place} ({$how})";
		$counts[ $key ] = isset( $counts[ $key ] ) ? $counts[ $key ] + 1 : 1;

		if ( $apply ) {
			$term = maglist_child_find_term_flexible( $place, 'category' );
			if ( $term ) {
				wp_set_post_categories( $post_id, array( (int) $term->term_id ), true );
			}
		}
	}
	ksort( $counts );
	foreach ( $counts as $key => $n ) {
		$log[] = "   {$key}: {$n}";
	}
	$log[] = sprintf( '   no place found: %d of %d', count( $none ), count( $ids ) );
	foreach ( array_slice( $none, 0, 8 ) as $post_id ) {
		$text  = trim( preg_replace( '/\s+/u', ' ', html_entity_decode( wp_strip_all_tags( get_post_field( 'post_content', $post_id ) ), ENT_QUOTES, 'UTF-8' ) ) );
		$log[] = "      e.g. #{$post_id}: " . mb_substr( $text, 0, 50 );
	}

	// 7. Menu.
	$log[] = '';
	$log[] = '7. Menu "' . MAGLIST_CHILD_RESTRUCTURE_MENU . '" (assigned to the primary location; old menu kept)';
	$log   = array_merge( $log, maglist_child_build_restructure_menu( $plan, $apply ) );

	return $log;
}

/**
 * Build (or rebuild) the new primary menu from the plan.
 *
 * @param array $plan  Plan.
 * @param bool  $apply False = only describe it.
 * @return string[] Log lines.
 */
function maglist_child_build_restructure_menu( $plan, $apply ) {
	$log     = array();
	$menu_id = 0;

	if ( $apply ) {
		$menu    = wp_get_nav_menu_object( MAGLIST_CHILD_RESTRUCTURE_MENU );
		$menu_id = $menu ? (int) $menu->term_id : (int) wp_create_nav_menu( MAGLIST_CHILD_RESTRUCTURE_MENU );
		foreach ( (array) wp_get_nav_menu_items( $menu_id ) as $item ) {
			wp_delete_post( $item->ID, true );
		}
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'  => '<i class="fa fa-lg fa-home"></i>',
				'menu-item-url'    => home_url( '/' ),
				'menu-item-type'   => 'custom',
				'menu-item-status' => 'publish',
			)
		);
	}

	$add = static function ( $term, $parent_item ) use ( $apply, $menu_id ) {
		if ( ! $apply ) {
			return 0;
		}
		return (int) wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-object-id' => (int) $term->term_id,
				'menu-item-object'    => 'category',
				'menu-item-type'      => 'taxonomy',
				'menu-item-parent-id' => (int) $parent_item,
				'menu-item-status'    => 'publish',
			)
		);
	};

	foreach ( $plan['menu'] as $ref => $children ) {
		$term = maglist_child_plan_term( $ref, $plan );
		if ( ! $term ) {
			$log[] = "   (not yet created: {$ref})";
			continue;
		}
		$item_id = $add( $term, 0 );
		$names   = array();
		foreach ( $children as $child_ref ) {
			$child = maglist_child_plan_term( $child_ref, $plan );
			if ( ! $child || ( $apply && ! $child->count ) ) {
				continue;
			}
			$add( $child, $item_id );
			$names[] = $child->name;
		}
		$log[] = '   ' . $term->name . ( $names ? ' ⌄ ' . implode( ', ', $names ) : '' );
	}

	if ( $apply ) {
		$locations            = (array) get_theme_mod( 'nav_menu_locations', array() );
		$locations['primary'] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );
	}

	return $log;
}

/**
 * 301 old category URLs whose slug was removed by a merge or rename, and the
 * old menu hub pages (/अपराध/ …) that no longer map to a category.
 */
function maglist_child_redirect_merged_categories() {
	$orphan_hub = is_page() && ! maglist_child_get_page_category_hub();
	if ( ( ! is_404() && ! $orphan_hub ) || empty( $_SERVER['REQUEST_URI'] ) ) {
		return;
	}

	$map = (array) get_option( 'maglist_child_category_redirects', array() );
	if ( ! $map ) {
		return;
	}

	$path  = trim( rawurldecode( (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH ) ), '/' );
	$paged = 0;
	if ( preg_match( '#^(.*)/page/(\d+)$#u', $path, $m ) ) {
		$path  = $m[1];
		$paged = (int) $m[2];
	}

	$parts = explode( '/', $path );
	$leaf  = end( $parts );
	if ( ! isset( $map[ $leaf ] ) ) {
		return;
	}

	$link = get_term_link( (int) $map[ $leaf ], 'category' );
	if ( is_wp_error( $link ) ) {
		return;
	}
	if ( $paged > 1 ) {
		$link = trailingslashit( $link ) . 'page/' . $paged . '/';
	}

	wp_safe_redirect( $link, 301 );
	exit;
}
add_action( 'template_redirect', 'maglist_child_redirect_merged_categories', 1 );

/**
 * Tools → Category restructure (for sites without WP-CLI).
 */
function maglist_child_restructure_admin_page() {
	add_management_page(
		__( 'Category restructure', 'maglist-child' ),
		__( 'Category restructure', 'maglist-child' ),
		'manage_options',
		'maglist-child-restructure',
		'maglist_child_render_restructure_page'
	);
}
add_action( 'admin_menu', 'maglist_child_restructure_admin_page' );

/**
 * Render the Tools page.
 */
function maglist_child_render_restructure_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$log = array();
	if ( isset( $_POST['maglist_child_restructure'] ) && check_admin_referer( 'maglist_child_restructure' ) ) {
		$apply = 'apply' === sanitize_key( wp_unslash( $_POST['maglist_child_restructure'] ) );
		$log   = maglist_child_category_restructure( $apply );
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Category restructure', 'maglist-child' ); ?></h1>
		<p><?php esc_html_e( 'Preview first. Back up the database before applying, then purge the page cache.', 'maglist-child' ); ?></p>
		<form method="post">
			<?php wp_nonce_field( 'maglist_child_restructure' ); ?>
			<button class="button" name="maglist_child_restructure" value="preview"><?php esc_html_e( 'Preview (dry run)', 'maglist-child' ); ?></button>
			<button class="button button-primary" name="maglist_child_restructure" value="apply" onclick="return confirm('Apply the category restructure?');"><?php esc_html_e( 'Apply', 'maglist-child' ); ?></button>
		</form>
		<?php if ( $log ) : ?>
			<pre style="background:#fff;padding:12px;border:1px solid #ccd0d4;max-height:70vh;overflow:auto;"><?php echo esc_html( implode( "\n", $log ) ); ?></pre>
		<?php endif; ?>
	</div>
	<?php
}
