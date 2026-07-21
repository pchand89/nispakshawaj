<?php
/**
 * Template Name: Home Layout
 * Template Post Type: page
 *
 * Alternative to front-page.php: assign this template to any Page (e.g. from
 * Pages > Edit Page > Page Attributes > Template) if you'd rather build your
 * "Home" as a static Page and pick the layout manually instead of relying on
 * front-page.php. Renders the exact same homepage grid.
 *
 * @package Maglist_Child
 */

get_header();

get_template_part( 'template-parts/home/homepage' );

get_footer();
