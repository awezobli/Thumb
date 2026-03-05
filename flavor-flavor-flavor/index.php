<?php
/**
 * Fallback template — przekierowuje na stronę główną (landing page)
 */
get_header();

if ( have_posts() ) :
    while ( have_posts() ) :
        the_post();
        ?>
        <article class="sjp-content">
            <div class="sjp-content__inner">
                <h1><?php the_title(); ?></h1>
                <?php the_content(); ?>
            </div>
        </article>
        <?php
    endwhile;
else :
    get_template_part( 'front-page' );
endif;

get_footer();
