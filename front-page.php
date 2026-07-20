<?php
/**
 * The Front page.
 *
 * WordPress always prefers front-page.php over index.php/home.php for the
 * site's front page (is_front_page()), regardless of whether Settings > Reading
 * is set to "your latest posts" or "a static page" - so this file is enough on
 * its own to replace the homepage.
 *
 * get_header() / get_footer() still fire every standard Maglist header/footer
 * action hook (top bar, primary menu, footer widgets, copyright, etc.), so
 * global elements keep working exactly as before. We intentionally do NOT call
 * do_action( Maglist_Helper::fn_prefix( 'before-content' ) ) / 'after-content'
 * here - those are what render the *default* Maglist main-banner/breaking-news
 * homepage blocks, and skipping them is how we cleanly bypass the stock layout
 * without editing any parent theme file.
 *
 * @package Maglist_Child
 */

get_header();

get_template_part( 'template-parts/home/homepage' );

get_footer();
