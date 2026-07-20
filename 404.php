<?php
/**
 * 404 Not Found Template — Ratopati Style
 *
 * @package Nispaksha_Child
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="rp-container">
        <div class="rp-empty-state rp-empty-state--404">
            <i class="fas fa-triangle-exclamation"></i>
            <h1>४०४ — पृष्ठ फेला परेन</h1>
            <p>तपाईंले खोज्नुभएको पृष्ठ हटाइएको, नाम परिवर्तन गरिएको, वा अस्थायी रूपमा उपलब्ध नभएको हुनसक्छ।</p>
            <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="rp-empty-state__search">
                <input type="search" name="s" placeholder="समाचार खोज्नुहोस्..." required />
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="rp-empty-state__btn">गृहपृष्ठमा फर्कनुहोस्</a>
        </div>

        <div class="rp-404-trending">
            <?php get_template_part( 'template-parts/sidebar-trending' ); ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>
