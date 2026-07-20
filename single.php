<?php
/**
 * Single Post Template — Ratopati Style
 *
 * Keeps the same header/footer chrome and design system as the homepage
 * so visitors don't hit a jarring, differently-styled page when they open
 * an article.
 *
 * @package Nispaksha_Child
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="rp-container">
        <?php nispaksha_breadcrumbs(); ?>

        <div class="rp-main-layout">
            <div class="rp-content">
                <?php while ( have_posts() ) : the_post();
                    $main_cat   = nispaksha_get_primary_category( get_the_ID() );
                    $main_thumb = nispaksha_get_thumb_url( get_the_ID(), 'nispaksha-hero' );
                ?>
                    <article class="rp-article" id="post-<?php the_ID(); ?>">
                        <header class="rp-article__header">
                            <?php if ( $main_cat ) : ?>
                                <a href="<?php echo esc_url( get_category_link( $main_cat->term_id ) ); ?>" class="rp-article__badge">
                                    <?php echo esc_html( $main_cat->name ); ?>
                                </a>
                            <?php endif; ?>

                            <h1 class="rp-article__title"><?php the_title(); ?></h1>

                            <div class="rp-article__meta">
                                <span><i class="far fa-user"></i> <?php the_author(); ?></span>
                                <span><i class="far fa-clock"></i> <?php echo esc_html( nispaksha_time_ago() ); ?></span>
                                <span><i class="far fa-hourglass"></i> <?php echo esc_html( nispaksha_reading_time( get_the_ID() ) ); ?></span>
                            </div>
                        </header>

                        <?php if ( ! empty( $main_thumb ) ) : ?>
                            <div class="rp-article__featured">
                                <img src="<?php echo esc_url( $main_thumb ); ?>" alt="<?php the_title_attribute(); ?>" loading="eager" />
                            </div>
                        <?php endif; ?>

                        <div class="rp-article__content">
                            <?php the_content(); ?>
                        </div>

                        <?php
                        $tags = get_the_tags();
                        if ( $tags ) :
                        ?>
                            <div class="rp-article__tags">
                                <?php foreach ( $tags as $tag ) : ?>
                                    <a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>">#<?php echo esc_html( $tag->name ); ?></a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="rp-share">
                            <span class="rp-share__label"><i class="fas fa-share-alt"></i> सेयर गर्नुहोस्:</span>
                            <a href="<?php echo esc_url( 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( get_permalink() ) ); ?>" target="_blank" rel="noopener noreferrer" class="rp-share__btn rp-share__btn--fb"><i class="fab fa-facebook-f"></i></a>
                            <a href="<?php echo esc_url( 'https://twitter.com/intent/tweet?url=' . rawurlencode( get_permalink() ) . '&text=' . rawurlencode( get_the_title() ) ); ?>" target="_blank" rel="noopener noreferrer" class="rp-share__btn rp-share__btn--tw"><i class="fab fa-x-twitter"></i></a>
                            <a href="<?php echo esc_url( 'https://api.whatsapp.com/send?text=' . rawurlencode( get_the_title() . ' ' . get_permalink() ) ); ?>" target="_blank" rel="noopener noreferrer" class="rp-share__btn rp-share__btn--wa"><i class="fab fa-whatsapp"></i></a>
                        </div>
                    </article>

                    <?php
                    // Related stories from the same category.
                    if ( $main_cat ) :
                        $related = nispaksha_get_category_posts( $main_cat->slug, 4, array( get_the_ID() ) );
                        if ( $related->have_posts() ) :
                    ?>
                        <section class="rp-related">
                            <h2 class="rp-section__title">सम्बन्धित समाचार</h2>
                            <div class="rp-grid-4">
                                <?php while ( $related->have_posts() ) : $related->the_post(); ?>
                                    <?php get_template_part( 'template-parts/news-card' ); ?>
                                <?php endwhile; ?>
                            </div>
                        </section>
                    <?php
                        endif;
                        wp_reset_postdata();
                    endif;
                    ?>

                    <?php if ( comments_open() || get_comments_number() ) : ?>
                        <div class="rp-comments">
                            <?php comments_template(); ?>
                        </div>
                    <?php endif; ?>

                <?php endwhile; ?>
            </div>

            <aside class="rp-sidebar-wrap" role="complementary">
                <?php get_template_part( 'template-parts/sidebar-trending' ); ?>
            </aside>
        </div>
    </div>
</main>

<?php get_footer(); ?>
