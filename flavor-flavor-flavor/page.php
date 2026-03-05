<?php
/**
 * Szablon dla stron (regulamin, polityka prywatności, itp.)
 */
get_header();
?>

<?php while ( have_posts() ) : the_post(); ?>
    <article class="sjp-content">
        <div class="sjp-content__inner">
            <h1><?php the_title(); ?></h1>
            <?php the_content(); ?>
        </div>
    </article>
<?php endwhile; ?>

<?php
get_footer();
