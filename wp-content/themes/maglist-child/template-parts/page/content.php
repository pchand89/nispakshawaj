<?php
/**
 * Ratopati-style static page body.
 *
 * @package Maglist_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'na-page-article' ); ?>>
	<header class="na-page-article__header">
		<h1 class="na-page-article__title"><?php the_title(); ?></h1>
	</header>
	<div class="na-page-article__content">
		<?php maglist_child_the_static_page_content(); ?>
	</div>
</article>
