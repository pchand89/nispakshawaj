<?php
/**
 * Fix 404s on Nepali (and other non-ASCII) permalinks, and stop WordPress
 * from falling back to numeric post IDs as slugs.
 *
 * Softaculous often stores slugs as literal percent-encoding ("%e0%a4%b8...").
 * Encoding a full Nepali title that way regularly exceeds WP's varchar(200)
 * `post_name` column, truncates mid-sequence (e.g. "...%e0%a"), and Apache
 * returns 400 Bad Request.
 *
 * Published posts keep whatever slug they already have (usually Devanagari).
 * New posts get a short Romanized slug from the title (news-site style).
 * Incoming Softaculous-style percent paths are matched via direct DB lookup
 * (and rewritten to term/post IDs) so category archives like /समाचार/ resolve
 * even while term.slug is still stored as "%e0%a4%b8...". A batched repair
 * then converts those term slugs to Unicode.
 *
 * SO Pinyin Slugs strips Devanagari to "" — disabled here (Chinese-only plugin).
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** @var int Safe max length for post_name (column is varchar(200)). */
define( 'MAGLIST_CHILD_SLUG_MAX_LEN', 180 );

/** @var int Max hyphen-separated words in a new Romanized post slug. */
define( 'MAGLIST_CHILD_ROMAN_SLUG_MAX_WORDS', 14 );

/** @var int Max characters in a new Romanized post slug. */
define( 'MAGLIST_CHILD_ROMAN_SLUG_MAX_LEN', 120 );

/**
 * Disable SO Pinyin Slugs' sanitize_title filter (breaks Devanagari/percent slugs).
 */
function maglist_child_disable_pinyin_slug_filter() {
	if ( function_exists( 'sops_getPinyinSlug' ) ) {
		remove_filter( 'sanitize_title', 'sops_getPinyinSlug', 1 );
	}
}
add_action( 'plugins_loaded', 'maglist_child_disable_pinyin_slug_filter', 20 );
add_action( 'init', 'maglist_child_disable_pinyin_slug_filter', 0 );

/**
 * Build a Unicode-safe post slug from a title (no percent-encoding).
 *
 * @param string $title Raw title.
 * @return string
 */
function maglist_child_unicode_slug( $title ) {
	$title = trim( wp_strip_all_tags( (string) $title ) );
	if ( $title === '' ) {
		return '';
	}

	// Collapse whitespace to hyphens (avoids %20 in URLs).
	$title = preg_replace( '/[\s\x{00A0}]+/u', '-', $title );
	// Keep letters, combining marks (Devanagari matras/virama), numbers, hyphens.
	$title = preg_replace( '/[^\p{L}\p{M}\p{N}\-]+/u', '', $title );
	$title = preg_replace( '/-+/', '-', $title );
	$title = trim( (string) $title, '-' );

	if ( $title === '' ) {
		return '';
	}

	if ( function_exists( 'mb_substr' ) ) {
		$title = mb_substr( $title, 0, MAGLIST_CHILD_SLUG_MAX_LEN, 'UTF-8' );
	} else {
		$title = substr( $title, 0, MAGLIST_CHILD_SLUG_MAX_LEN );
	}

	return trim( $title, '-' );
}

/**
 * Whether a string contains Devanagari (or other non-ASCII) characters.
 *
 * @param string $text Text to inspect.
 * @return bool
 */
function maglist_child_slug_has_non_ascii( $text ) {
	return is_string( $text ) && $text !== '' && (bool) preg_match( '/[^\x00-\x7F]/', $text );
}

/**
 * A post that already had a public URL must keep that slug.
 *
 * @param WP_Post|null $post Post, or null for a brand-new insert.
 * @return bool
 */
function maglist_child_post_has_locked_slug( $post ) {
	if ( ! $post instanceof WP_Post || $post->post_name === '' ) {
		return false;
	}

	if ( in_array( $post->post_status, array( 'publish', 'future', 'private' ), true ) ) {
		return true;
	}

	// Never-published drafts keep post_date_gmt as zero until first publish.
	return $post->post_date_gmt && '0000-00-00 00:00:00' !== $post->post_date_gmt;
}

/**
 * Romanize a Nepali title into a short, shareable post slug.
 *
 * Uses a journalistic mapping (च→ch, श→sh, आ→a) rather than IAST, so
 * महाकाली becomes mahakali instead of mahaakaalii. Falls back to ICU
 * transliteration when the walker produces nothing.
 *
 * @param string $title Raw post title.
 * @return string ASCII slug, or empty string.
 */
function maglist_child_romanize_to_slug( $title ) {
	$title = trim( wp_strip_all_tags( (string) $title ) );
	if ( $title === '' ) {
		return '';
	}

	$digits = array(
		'०' => '0',
		'१' => '1',
		'२' => '2',
		'३' => '3',
		'४' => '4',
		'५' => '5',
		'६' => '6',
		'७' => '7',
		'८' => '8',
		'९' => '9',
	);
	$title  = strtr( $title, $digits );

	$roman = maglist_child_romanize_devanagari( $title );
	if ( $roman === '' && class_exists( 'Transliterator' ) ) {
		$transliterator = Transliterator::create( 'Any-Latin; Latin-ASCII; Lower()' );
		if ( $transliterator ) {
			$roman = $transliterator->transliterate( $title );
		}
	}

	if ( ! is_string( $roman ) || $roman === '' ) {
		$roman = $title;
	}

	remove_filter( 'sanitize_title', 'maglist_child_preserve_unicode_slug', 20 );
	$slug = sanitize_title( $roman );
	add_filter( 'sanitize_title', 'maglist_child_preserve_unicode_slug', 20, 3 );

	$slug = preg_replace( '/[^a-z0-9\-]+/', '', (string) $slug );
	$slug = preg_replace( '/-+/', '-', (string) $slug );
	$slug = trim( (string) $slug, '-' );

	if ( $slug === '' ) {
		return '';
	}

	$parts = explode( '-', $slug );
	if ( count( $parts ) > MAGLIST_CHILD_ROMAN_SLUG_MAX_WORDS ) {
		$parts = array_slice( $parts, 0, MAGLIST_CHILD_ROMAN_SLUG_MAX_WORDS );
		$slug  = implode( '-', $parts );
	}

	if ( strlen( $slug ) > MAGLIST_CHILD_ROMAN_SLUG_MAX_LEN ) {
		$slug = substr( $slug, 0, MAGLIST_CHILD_ROMAN_SLUG_MAX_LEN );
		$slug = preg_replace( '/-[^-]*$/', '', $slug );
		$slug = trim( (string) $slug, '-' );
	}

	return $slug;
}

/**
 * Walk Devanagari and emit a readable Roman string.
 *
 * @param string $text Title text, digits already converted.
 * @return string
 */
function maglist_child_romanize_devanagari( $text ) {
	$special = array(
		'क्ष' => 'ksh',
		'त्र' => 'tr',
		'ज्ञ' => 'gya',
		'श्र' => 'shr',
	);

	$ind_vowels = array(
		'अ' => 'a',
		'आ' => 'a',
		'इ' => 'i',
		'ई' => 'i',
		'उ' => 'u',
		'ऊ' => 'u',
		'ए' => 'e',
		'ऐ' => 'ai',
		'ओ' => 'o',
		'औ' => 'au',
		'ऋ' => 'ri',
	);

	$cons = array(
		'क' => 'k',
		'ख' => 'kh',
		'ग' => 'g',
		'घ' => 'gh',
		'ङ' => 'ng',
		'च' => 'ch',
		'छ' => 'chh',
		'ज' => 'j',
		'झ' => 'jh',
		'ञ' => 'n',
		'ट' => 't',
		'ठ' => 'th',
		'ड' => 'd',
		'ढ' => 'dh',
		'ण' => 'n',
		'त' => 't',
		'थ' => 'th',
		'द' => 'd',
		'ध' => 'dh',
		'न' => 'n',
		'प' => 'p',
		'फ' => 'ph',
		'ब' => 'b',
		'भ' => 'bh',
		'म' => 'm',
		'य' => 'y',
		'र' => 'r',
		'ल' => 'l',
		'व' => 'b',
		'श' => 'sh',
		'ष' => 'sh',
		'स' => 's',
		'ह' => 'h',
	);

	$matras = array(
		'ा' => 'a',
		'ि' => 'i',
		'ी' => 'i',
		'ु' => 'u',
		'ू' => 'u',
		'े' => 'e',
		'ै' => 'ai',
		'ो' => 'o',
		'ौ' => 'au',
		'ृ' => 'ri',
	);

	$chars = preg_split( '//u', $text, -1, PREG_SPLIT_NO_EMPTY );
	if ( ! is_array( $chars ) ) {
		return '';
	}

	$out   = '';
	$count = count( $chars );

	for ( $i = 0; $i < $count; $i++ ) {
		$ch   = $chars[ $i ];
		$next = ( $i + 1 < $count ) ? $chars[ $i + 1 ] : '';

		if ( isset( $chars[ $i + 1 ] ) && isset( $special[ $ch . $next ] ) ) {
			$out .= $special[ $ch . $next ];
			$i++;
			continue;
		}

		if ( isset( $ind_vowels[ $ch ] ) ) {
			$out .= $ind_vowels[ $ch ];
			continue;
		}

		if ( isset( $cons[ $ch ] ) ) {
			$base = $cons[ $ch ];
			if ( 'व' === $ch ) {
				if ( $i > 0 && '्' === $chars[ $i - 1 ] ) {
					$base = 'w';
				} elseif ( in_array( $next, array( 'ि', 'ी', 'े', 'ै' ), true ) ) {
					$base = 'v';
				}
			}
			$out .= $base;

			if ( '्' === $next ) {
				$i++;
				continue;
			}

			if ( isset( $matras[ $next ] ) ) {
				$out .= $matras[ $next ];
				$i++;
				continue;
			}

			if ( 'ं' === $next || 'ँ' === $next ) {
				$out .= 'an';
				$i++;
				continue;
			}

			$after = $next;
			$end   = ( $after === '' || preg_match( '/[\s\-\.,;:!\?\/\(\)\"\'।]/u', $after ) );
			if ( ! $end ) {
				$out .= 'a';
			}
			continue;
		}

		if ( isset( $matras[ $ch ] ) || '्' === $ch || 'ं' === $ch || 'ँ' === $ch || 'ः' === $ch || '़' === $ch ) {
			continue;
		}

		$out .= $ch;
	}

	return $out;
}

/**
 * Give never-published posts a Romanized slug. Leave published URLs alone.
 *
 * Gutenberg sends a Devanagari slug from sanitize_title(); we replace that
 * on save. A reporter-typed ASCII slug is kept.
 *
 * @param array $data    Sanitized post data about to be written.
 * @param array $postarr Raw post array, including ID.
 * @return array
 */
function maglist_child_romanize_new_post_slug( $data, $postarr ) {
	if ( ( $data['post_type'] ?? '' ) !== 'post' ) {
		return $data;
	}

	if ( in_array( $data['post_status'] ?? '', array( 'inherit', 'trash' ), true ) ) {
		return $data;
	}

	$post_id  = isset( $postarr['ID'] ) ? (int) $postarr['ID'] : 0;
	$existing = $post_id ? get_post( $post_id ) : null;

	if ( maglist_child_post_has_locked_slug( $existing ) ) {
		return $data;
	}

	$title = isset( $data['post_title'] ) ? trim( wp_strip_all_tags( (string) $data['post_title'] ) ) : '';
	if ( $title === '' || $title === __( 'Auto Draft' ) ) {
		return $data;
	}

	$current = isset( $data['post_name'] ) ? (string) $data['post_name'] : '';
	$replace = (
		$current === ''
		|| maglist_child_slug_has_non_ascii( $current )
		|| false !== strpos( $current, '%' )
		|| (bool) preg_match( '/^[0-9]+$/', $current )
	);

	if ( ! $replace ) {
		return $data;
	}

	$slug = maglist_child_romanize_to_slug( $title );
	if ( $slug === '' ) {
		return $data;
	}

	$data['post_name'] = $slug;
	return $data;
}
add_filter( 'wp_insert_post_data', 'maglist_child_romanize_new_post_slug', 20, 2 );

/**
 * REST: romanize a title so the block editor can show the slug while typing.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response
 */
function maglist_child_rest_romanize_slug( WP_REST_Request $request ) {
	$title = trim( wp_strip_all_tags( (string) $request->get_param( 'title' ) ) );
	return rest_ensure_response(
		array(
			'slug' => maglist_child_romanize_to_slug( $title ),
		)
	);
}

/**
 * Register the romanize-slug REST route.
 */
function maglist_child_register_romanize_slug_route() {
	register_rest_route(
		'maglist-child/v1',
		'/romanize-slug',
		array(
			'methods'             => 'GET',
			'permission_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
			'args'                => array(
				'title' => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => static function ( $value ) {
						return wp_strip_all_tags( wp_unslash( (string) $value ) );
					},
				),
			),
			'callback'            => 'maglist_child_rest_romanize_slug',
		)
	);
}
add_action( 'rest_api_init', 'maglist_child_register_romanize_slug_route' );

/**
 * Load the live slug preview in the post editor.
 */
function maglist_child_enqueue_editor_romanize_slug() {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'post' !== $screen->post_type ) {
		return;
	}

	wp_enqueue_script(
		'maglist-child-editor-romanize-slug',
		MAGLIST_CHILD_URI . '/assets/js/editor-romanize-slug.js',
		array( 'wp-data', 'wp-api-fetch' ),
		MAGLIST_CHILD_VERSION,
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'maglist_child_enqueue_editor_romanize_slug' );

/**
 * Decode Softaculous-style percent-encoded path segments to Unicode.
 *
 * @param string $slug Pagename, post name, or category_name (may include "/").
 * @return string
 */
function maglist_child_decode_softaculous_slug( $slug ) {
	if ( ! is_string( $slug ) || $slug === '' ) {
		return $slug;
	}

	$parts = explode( '/', $slug );
	foreach ( $parts as &$part ) {
		if ( $part === '' || preg_match( '/[^\x00-\x7F]/', $part ) ) {
			continue;
		}
		if ( ! preg_match( '/%[0-9a-f]{2}/i', $part ) ) {
			continue;
		}
		$decoded = rawurldecode( $part );
		if ( is_string( $decoded ) && $decoded !== '' && preg_match( '/[^\x00-\x7F]/', $decoded ) ) {
			$part = $decoded;
		}
	}
	unset( $part );

	return implode( '/', $parts );
}

/**
 * Encode a Unicode slug the Softaculous way (literal lowercase %xx per byte).
 *
 * @param string $slug Unicode or already-encoded slug.
 * @return string
 */
function maglist_child_softaculous_encode_slug( $slug ) {
	if ( ! is_string( $slug ) || $slug === '' ) {
		return $slug;
	}

	if ( ! preg_match( '/[^\x00-\x7F]/', $slug ) ) {
		return preg_match( '/%[0-9a-f]{2}/i', $slug ) ? strtolower( $slug ) : $slug;
	}

	$out  = '';
	$bytes = $slug;
	$len   = strlen( $bytes );
	for ( $i = 0; $i < $len; $i++ ) {
		$byte = $bytes[ $i ];
		$ord  = ord( $byte );
		if (
			( $ord >= 48 && $ord <= 57 ) ||
			( $ord >= 65 && $ord <= 90 ) ||
			( $ord >= 97 && $ord <= 122 ) ||
			'-' === $byte || '_' === $byte || '.' === $byte
		) {
			$out .= $byte;
		} else {
			$out .= '%' . sprintf( '%02x', $ord );
		}
	}

	return $out;
}

/**
 * Candidate slug forms for Softaculous / Unicode / core lookups.
 *
 * @param string $slug Raw request or stored slug (may include "/").
 * @return string[]
 */
function maglist_child_slug_candidates( $slug ) {
	$slug = is_string( $slug ) ? $slug : '';
	if ( $slug === '' ) {
		return array();
	}

	$decoded = maglist_child_decode_softaculous_slug( $slug );
	$encoded = maglist_child_softaculous_encode_slug( $decoded );

	$candidates = array( $slug, $decoded, $encoded );
	if ( function_exists( 'mb_strtolower' ) ) {
		$candidates[] = mb_strtolower( $decoded, 'UTF-8' );
	}

	return array_values( array_unique( array_filter( $candidates, 'strlen' ) ) );
}

/**
 * Find a term by trying Softaculous percent + Unicode slug forms (direct DB).
 *
 * Bypasses get_term_by()/sanitize_title(), which convert %xx → Unicode and miss
 * Softaculous rows that still store the literal percent slug.
 *
 * @param string $slug     Request slug (may be nested "parent/child").
 * @param string $taxonomy Taxonomy name.
 * @return WP_Term|false
 */
function maglist_child_find_term_flexible( $slug, $taxonomy ) {
	$slug = is_string( $slug ) ? trim( $slug, '/' ) : '';
	if ( $slug === '' || ! taxonomy_exists( $taxonomy ) ) {
		return false;
	}

	// Hierarchical paths: resolve the leaf against full path via get_term_by after leaf match.
	$parts = explode( '/', $slug );
	$leaf  = end( $parts );
	if ( ! is_string( $leaf ) || $leaf === '' ) {
		return false;
	}

	global $wpdb;

	foreach ( maglist_child_slug_candidates( $leaf ) as $candidate ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$term_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT t.term_id FROM {$wpdb->terms} AS t
				INNER JOIN {$wpdb->term_taxonomy} AS tt ON t.term_id = tt.term_id
				WHERE tt.taxonomy = %s AND t.slug = %s
				LIMIT 1",
				$taxonomy,
				$candidate
			)
		);

		if ( ! $term_id ) {
			continue;
		}

		$term = get_term( (int) $term_id, $taxonomy );
		if ( $term instanceof WP_Term && ! is_wp_error( $term ) ) {
			return $term;
		}
	}

	return false;
}

/**
 * Find a post/page by Softaculous percent or Unicode post_name (direct DB).
 *
 * @param string $slug      Request slug (may include nested page path).
 * @param string $post_type post|page|attachment.
 * @return WP_Post|false
 */
function maglist_child_find_post_flexible( $slug, $post_type = 'post' ) {
	$slug = is_string( $slug ) ? trim( $slug, '/' ) : '';
	if ( $slug === '' ) {
		return false;
	}

	$parts = explode( '/', $slug );
	$leaf  = end( $parts );
	if ( ! is_string( $leaf ) || $leaf === '' ) {
		return false;
	}

	global $wpdb;

	foreach ( maglist_child_slug_candidates( $leaf ) as $candidate ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$post_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts}
				WHERE post_name = %s AND post_type = %s AND post_status NOT IN ('trash','auto-draft')
				ORDER BY CASE post_status WHEN 'publish' THEN 0 WHEN 'private' THEN 1 ELSE 2 END, ID DESC
				LIMIT 1",
				$candidate,
				$post_type
			)
		);

		if ( $post_id ) {
			$post = get_post( (int) $post_id );
			if ( $post instanceof WP_Post ) {
				return $post;
			}
		}
	}

	return false;
}

/**
 * When saving Unicode titles, keep a real Devanagari slug instead of "".
 * For queries, core also strips non-ASCII — preserve the slug so name= matches.
 *
 * @param string $title     Sanitized title so far.
 * @param string $raw_title Original title before sanitization.
 * @param string $context   Sanitization context (save|query|…).
 * @return string
 */
function maglist_child_preserve_unicode_slug( $title, $raw_title = '', $context = 'save' ) {
	$source = ( is_string( $raw_title ) && $raw_title !== '' ) ? $raw_title : $title;
	if ( ! is_string( $source ) || $source === '' ) {
		return $title;
	}

	// Query: keep Devanagari and Softaculous percent slugs intact for DB matching.
	if ( 'query' === $context ) {
		if ( preg_match( '/[^\x00-\x7F]/', $source ) ) {
			return $source;
		}
		if ( preg_match( '/%[0-9a-f]{2}/i', $source ) ) {
			return $source;
		}
		return $title;
	}

	if ( ! preg_match( '/[^\x00-\x7F]/', $source ) ) {
		return $title;
	}

	$slug = maglist_child_unicode_slug( $source );
	return $slug !== '' ? $slug : $title;
}
add_filter( 'sanitize_title', 'maglist_child_preserve_unicode_slug', 20, 3 );

/**
 * Remap rewrite query vars so Softaculous percent slugs and Unicode URLs both resolve.
 *
 * Category/tag lookups use term IDs (avoids sanitize_title wiping/mismatching slugs).
 *
 * @param array $query_vars Parsed request vars.
 * @return array
 */
function maglist_child_fix_encoded_slugs( $query_vars ) {
	if ( ! is_array( $query_vars ) ) {
		return $query_vars;
	}

	if ( ! empty( $query_vars['category_name'] ) && is_string( $query_vars['category_name'] ) ) {
		$term = maglist_child_find_term_flexible( $query_vars['category_name'], 'category' );
		if ( $term ) {
			$query_vars['cat'] = (int) $term->term_id;
			unset( $query_vars['category_name'], $query_vars['error'] );
		}
	}

	if ( ! empty( $query_vars['tag'] ) && is_string( $query_vars['tag'] ) ) {
		$term = maglist_child_find_term_flexible( $query_vars['tag'], 'post_tag' );
		if ( $term ) {
			$query_vars['tag_id'] = (int) $term->term_id;
			unset( $query_vars['tag'], $query_vars['error'] );
		}
	}

	// /tag/{slug}/ may land as pagename "tag/{slug}" when rewrites miss Unicode paths.
	if ( empty( $query_vars['tag_id'] ) && empty( $query_vars['tag'] ) ) {
		$tag_path = '';
		if ( ! empty( $query_vars['pagename'] ) && is_string( $query_vars['pagename'] ) ) {
			$tag_path = $query_vars['pagename'];
		} elseif ( ! empty( $query_vars['name'] ) && is_string( $query_vars['name'] ) ) {
			$tag_path = $query_vars['name'];
		}

		if ( $tag_path && preg_match( '#^tag/(.+)$#u', trim( $tag_path, '/' ), $tag_match ) ) {
			$term = maglist_child_find_term_flexible( $tag_match[1], 'post_tag' );
			if ( $term ) {
				$query_vars['tag_id'] = (int) $term->term_id;
				unset( $query_vars['pagename'], $query_vars['name'], $query_vars['page'], $query_vars['page_id'], $query_vars['attachment'], $query_vars['error'] );
			}
		}
	}

	// Last resort: read /tag/{slug}/ from the request URI (Unicode or percent-encoded).
	if ( empty( $query_vars['tag_id'] ) && empty( $query_vars['tag'] ) && ! empty( $_SERVER['REQUEST_URI'] ) ) {
		$req_path = (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH );
		if ( is_string( $req_path ) && preg_match( '#/tag/([^/]+)/?$#u', $req_path, $uri_match ) ) {
			$term = maglist_child_find_term_flexible( rawurldecode( $uri_match[1] ), 'post_tag' );
			if ( $term ) {
				$query_vars['tag_id'] = (int) $term->term_id;
				unset( $query_vars['pagename'], $query_vars['name'], $query_vars['page'], $query_vars['page_id'], $query_vars['error'] );
			}
		}
	}

	// Empty category_base: /समाचार/ may parse as pagename instead of category_name.
	if (
		empty( $query_vars['cat'] ) &&
		empty( $query_vars['category_name'] ) &&
		! empty( $query_vars['pagename'] ) &&
		is_string( $query_vars['pagename'] ) &&
		empty( $query_vars['page_id'] )
	) {
		$term = maglist_child_find_term_flexible( $query_vars['pagename'], 'category' );
		$page = maglist_child_find_post_flexible( $query_vars['pagename'], 'page' );
		if ( $term && ! $page ) {
			$query_vars['cat'] = (int) $term->term_id;
			unset( $query_vars['pagename'], $query_vars['name'], $query_vars['page'] );
		}
	}

	foreach ( array( 'name' => 'post', 'pagename' => 'page', 'attachment' => 'attachment' ) as $key => $post_type ) {
		if ( empty( $query_vars[ $key ] ) || ! is_string( $query_vars[ $key ] ) ) {
			continue;
		}
		if ( 'pagename' === $key && ! empty( $query_vars['cat'] ) ) {
			continue;
		}

		$post = maglist_child_find_post_flexible( $query_vars[ $key ], $post_type );
		if ( ! $post ) {
			// Still normalize the var for downstream Unicode matching after repairs.
			$query_vars[ $key ] = maglist_child_decode_softaculous_slug( $query_vars[ $key ] );
			continue;
		}

		if ( 'page' === $post_type ) {
			$query_vars['page_id'] = (int) $post->ID;
			unset( $query_vars['pagename'], $query_vars['name'] );
		} elseif ( 'attachment' === $post_type ) {
			$query_vars['attachment_id'] = (int) $post->ID;
			unset( $query_vars['attachment'] );
		} else {
			$query_vars['p'] = (int) $post->ID;
			unset( $query_vars['name'] );
		}
	}

	return $query_vars;
}
add_filter( 'request', 'maglist_child_fix_encoded_slugs', 1 );

/**
 * Build a permalink-safe slug from a post title.
 *
 * @param string $title   Post title.
 * @param int    $post_id Optional post ID for uniqueness.
 * @return string
 */
function maglist_child_make_post_slug( $title, $post_id = 0 ) {
	$title = trim( wp_strip_all_tags( (string) $title ) );
	if ( $title === '' ) {
		return '';
	}

	if ( preg_match( '/[^\x00-\x7F]/', $title ) ) {
		$slug = maglist_child_unicode_slug( $title );
	} else {
		remove_filter( 'sanitize_title', 'maglist_child_preserve_unicode_slug', 20 );
		$slug = sanitize_title( $title );
		add_filter( 'sanitize_title', 'maglist_child_preserve_unicode_slug', 20, 3 );
	}

	$slug = trim( (string) $slug, '-' );
	if ( $slug === '' || ! $post_id ) {
		return $slug;
	}

	$post = get_post( $post_id );
	if ( ! $post instanceof WP_Post ) {
		return $slug;
	}

	return wp_unique_post_slug( $slug, $post_id, $post->post_status, $post->post_type, (int) $post->post_parent );
}

/**
 * Whether a stored post_name should be regenerated from the title.
 *
 * @param WP_Post $post Post object.
 * @return bool
 */
function maglist_child_slug_needs_repair( WP_Post $post ) {
	$name = (string) $post->post_name;

	if ( $name === '' ) {
		return true;
	}

	// WP ID fallback: /2026/07/17/3766/
	if ( preg_match( '/^[0-9]+$/', $name ) && (string) (int) $post->ID === $name ) {
		return true;
	}

	// Softaculous percent-encoding, or a varchar(200)-truncated broken sequence.
	if ( false !== strpos( $name, '%' ) ) {
		return true;
	}

	return false;
}

/**
 * Batched repair: numeric / percent / truncated slugs → Unicode title slugs.
 */
function maglist_child_maybe_repair_post_slugs() {
	$flag = 'maglist_child_unicode_slugs_repaired';
	if ( get_option( $flag, '' ) === '1' ) {
		return;
	}

	if ( wp_doing_ajax() || wp_doing_cron() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}

	global $wpdb;

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$ids = $wpdb->get_col(
		"SELECT ID FROM {$wpdb->posts}
		WHERE post_type = 'post'
		  AND post_status IN ('publish','draft','pending','future','private')
		  AND (
		    post_name = ''
		    OR ( post_name REGEXP '^[0-9]+$' AND post_name = CAST(ID AS CHAR) )
		    OR post_name LIKE '%\\%%'
		  )
		ORDER BY ID DESC
		LIMIT 40"
	);

	if ( empty( $ids ) ) {
		update_option( $flag, '1', false );
		delete_option( 'maglist_child_numeric_slugs_repaired' );
		flush_rewrite_rules( false );
		return;
	}

	foreach ( $ids as $post_id ) {
		$post = get_post( (int) $post_id );
		if ( ! $post instanceof WP_Post || ! maglist_child_slug_needs_repair( $post ) ) {
			continue;
		}

		$new_slug = maglist_child_make_post_slug( $post->post_title, (int) $post->ID );
		if ( $new_slug === '' || $new_slug === $post->post_name || preg_match( '/^[0-9]+$/', $new_slug ) ) {
			continue;
		}

		wp_update_post(
			array(
				'ID'        => (int) $post->ID,
				'post_name' => $new_slug,
			)
		);
	}
}
add_action( 'init', 'maglist_child_maybe_repair_post_slugs', 30 );

/**
 * Batched repair: Softaculous percent-encoded term slugs → Unicode.
 */
function maglist_child_maybe_repair_term_slugs() {
	$flag = 'maglist_child_unicode_term_slugs_repaired';
	if ( get_option( $flag, '' ) === '1' ) {
		return;
	}

	if ( wp_doing_ajax() || wp_doing_cron() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}

	global $wpdb;

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$rows = $wpdb->get_results(
		"SELECT t.term_id, t.name, t.slug, tt.taxonomy
		FROM {$wpdb->terms} AS t
		INNER JOIN {$wpdb->term_taxonomy} AS tt ON t.term_id = tt.term_id
		WHERE t.slug LIKE '%\\%%'
		  AND tt.taxonomy IN ('category', 'post_tag')
		ORDER BY t.term_id ASC
		LIMIT 40"
	);

	if ( empty( $rows ) ) {
		update_option( $flag, '1', false );
		flush_rewrite_rules( false );
		return;
	}

	foreach ( $rows as $row ) {
		$decoded = maglist_child_decode_softaculous_slug( (string) $row->slug );
		$new     = maglist_child_unicode_slug( $decoded !== '' ? $decoded : (string) $row->name );
		if ( $new === '' || $new === $row->slug ) {
			continue;
		}

		$result = wp_update_term(
			(int) $row->term_id,
			(string) $row->taxonomy,
			array( 'slug' => $new )
		);

		if ( is_wp_error( $result ) ) {
			continue;
		}
	}
}
add_action( 'init', 'maglist_child_maybe_repair_term_slugs', 31 );