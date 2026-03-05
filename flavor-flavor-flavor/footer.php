</main><!-- .sjp-main -->

<?php
$product    = sjp_get_product();
$price      = $product ? $product->get_price_html() : '';
$cta_text   = get_theme_mod( 'sjp_cta_text', __( 'Kup teraz', 'flavor-flavor-flavor' ) );
$product_id = sjp_get_product_id();

$company_name  = get_theme_mod( 'sjp_company_name', get_bloginfo( 'name' ) );
$contact_email = get_theme_mod( 'sjp_contact_email', get_bloginfo( 'admin_email' ) );
$regulations   = get_theme_mod( 'sjp_regulations_url', '' );
$privacy       = get_theme_mod( 'sjp_privacy_url', '' );
?>

<footer class="sjp-footer">
    <div class="sjp-footer__inner">
        <div class="sjp-footer__left">
            <span>&copy; <?php echo date( 'Y' ); ?> <?php echo esc_html( $company_name ); ?></span>
        </div>
        <div class="sjp-footer__links">
            <?php if ( $regulations ) : ?>
                <a href="<?php echo esc_url( $regulations ); ?>"><?php esc_html_e( 'Regulamin', 'flavor-flavor-flavor' ); ?></a>
            <?php endif; ?>
            <?php if ( $privacy ) : ?>
                <a href="<?php echo esc_url( $privacy ); ?>"><?php esc_html_e( 'Polityka prywatności', 'flavor-flavor-flavor' ); ?></a>
            <?php endif; ?>
            <?php if ( $contact_email ) : ?>
                <a href="mailto:<?php echo esc_attr( $contact_email ); ?>"><?php esc_html_e( 'Kontakt', 'flavor-flavor-flavor' ); ?></a>
            <?php endif; ?>
        </div>
    </div>
</footer>

<?php if ( $product && ! is_checkout() ) : ?>
<div class="sjp-sticky-mobile-cta" id="sjp-sticky-cta">
    <button class="sjp-btn sjp-btn--sticky sjp-buy-now-btn"
            data-product-id="<?php echo esc_attr( $product_id ); ?>">
        <?php echo esc_html( $cta_text ); ?> — <?php echo wp_kses_post( $price ); ?>
    </button>
</div>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
