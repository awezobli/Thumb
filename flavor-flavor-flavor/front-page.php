<?php
/**
 * Strona główna — landing page jednoproduktowy
 *
 * Wszystkie sekcje konfigurowalne z Customizera WordPress.
 * Kolejność sekcji odpowiada najlepszym praktykom konwersji.
 */
get_header();

$product = sjp_get_product();

if ( ! $product ) : ?>
    <section class="sjp-no-product">
        <div class="sjp-container">
            <h1><?php esc_html_e( 'Skonfiguruj swój sklep', 'flavor-flavor-flavor' ); ?></h1>
            <p><?php esc_html_e( 'Dodaj produkt w WooCommerce, a następnie wybierz go w Wygląd → Dostosuj → Produkt.', 'flavor-flavor-flavor' ); ?></p>
        </div>
    </section>
<?php else :

    get_template_part( 'template-parts/hero' );

    if ( get_theme_mod( 'sjp_video_show', true ) ) {
        get_template_part( 'template-parts/video' );
    }

    if ( get_theme_mod( 'sjp_benefits_show', true ) ) {
        get_template_part( 'template-parts/benefits' );
    }

    if ( get_theme_mod( 'sjp_comparison_show', true ) ) {
        get_template_part( 'template-parts/comparison' );
    }

    if ( get_theme_mod( 'sjp_testimonials_show', true ) ) {
        get_template_part( 'template-parts/testimonials' );
    }

    if ( get_theme_mod( 'sjp_howto_show', true ) ) {
        get_template_part( 'template-parts/how-it-works' );
    }

    if ( get_theme_mod( 'sjp_faq_show', true ) ) {
        get_template_part( 'template-parts/faq' );
    }

    get_template_part( 'template-parts/guarantee' );

endif;

get_footer();
