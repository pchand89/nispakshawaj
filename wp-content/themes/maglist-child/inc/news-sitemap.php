<?php
/**
 * Google News sitemap for articles published in the last two days.
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register /news-sitemap.xml.
 */
function maglist_child_news_sitemap_rewrite() {
	add_rewrite_rule( '^google-news\.xml$', 'index.php?na_news_sitemap=1', 'top' );
}
add_action( 'init', 'maglist_child_news_sitemap_rewrite' );

/**
 * @param string[] $vars Query vars.
 * @return string[]
 */
function maglist_child_news_sitemap_query_var( $vars ) {
	$vars[] = 'na_news_sitemap';
	return $vars;
}
add_filter( 'query_vars', 'maglist_child_news_sitemap_query_var' );

/**
 * Flush rewrites once so the sitemap address resolves.
 */
function maglist_child_news_sitemap_flush_rules() {
	if ( '2' === get_option( 'maglist_child_news_sitemap', '' ) ) {
		return;
	}

	flush_rewrite_rules( false );
	update_option( 'maglist_child_news_sitemap', '2', false );
}
add_action( 'init', 'maglist_child_news_sitemap_flush_rules', 99 );

/**
 * @param string $output robots.txt body.
 * @return string
 */
function maglist_child_news_sitemap_robots( $output ) {
	$line = 'Sitemap: ' . home_url( '/google-news.xml' );
	if ( false !== strpos( $output, $line ) ) {
		return $output;
	}

	return rtrim( $output ) . "\n" . $line . "\n";
}
add_filter( 'robots_txt', 'maglist_child_news_sitemap_robots', 20 );

/**
 * Print the news sitemap and stop.
 */
function maglist_child_news_sitemap_render() {
	if ( ! get_query_var( 'na_news_sitemap' ) ) {
		return;
	}

	$query = new WP_Query(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => 1000,
			'ignore_sticky_posts' => 1,
			'no_found_rows'       => true,
			'date_query'          => array(
				array(
					'after'     => '2 days ago',
					'inclusive' => true,
				),
			),
		)
	);

	$publication = 'निश्पक्ष आवाज';

	status_header( 200 );
	header( 'Content-Type: application/xml; charset=UTF-8' );
	header( 'X-Robots-Tag: noindex' );
	nocache_headers();

	echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
	echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">' . "\n";

	foreach ( $query->posts as $post ) {
		if ( ! $post instanceof WP_Post || '' !== $post->post_password ) {
			continue;
		}

		$loc   = get_permalink( $post );
		$title = wp_strip_all_tags( get_the_title( $post ) );
		$date  = get_post_time( DATE_W3C, true, $post );
		if ( ! $loc || '' === $title || ! $date ) {
			continue;
		}

		echo "\t<url>\n";
		echo "\t\t<loc>" . esc_url( $loc ) . "</loc>\n";
		echo "\t\t<news:news>\n";
		echo "\t\t\t<news:publication>\n";
		echo "\t\t\t\t<news:name>" . esc_html( $publication ) . "</news:name>\n";
		echo "\t\t\t\t<news:language>ne</news:language>\n";
		echo "\t\t\t</news:publication>\n";
		echo "\t\t\t<news:publication_date>" . esc_html( $date ) . "</news:publication_date>\n";
		echo "\t\t\t<news:title>" . esc_html( $title ) . "</news:title>\n";
		echo "\t\t</news:news>\n";
		echo "\t</url>\n";
	}

	echo '</urlset>';
	exit;
}
add_action( 'template_redirect', 'maglist_child_news_sitemap_render', 0 );
