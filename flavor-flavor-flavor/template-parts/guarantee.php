<?php
/**
 * Sekcja gwarancji + finalne CTA
 */
$product    = sjp_get_product();
if ( ! $product ) return;

$product_id = $product->get_id();
$price      = $product->get_price_html();
$cta_text   = get_theme_mod( 'sjp_cta_text', __( 'Kup teraz', 'flavor-flavor-flavor' ) );

$guarantee_title = get_theme_mod( 'sjp_guarantee_title', __( 'Gwarancja satysfakcji', 'flavor-flavor-flavor' ) );
$guarantee_text  = get_theme_mod( 'sjp_guarantee_text',
    __( 'Jeśli z jakiegokolwiek powodu nie będziesz zadowolony — zwrócimy Ci 100% pieniędzy. Bez pytań. Bez haczyków.', 'flavor-flavor-flavor' )
);
$guarantee_days  = get_theme_mod( 'sjp_guarantee_days', '30' );
$guarantee_icon  = get_theme_mod( 'sjp_guarantee_icon', '' );

$free_shipping = get_theme_mod( 'sjp_free_shipping_text', __( 'Darmowa dostawa', 'flavor-flavor-flavor' ) );
$return_text   = get_theme_mod( 'sjp_return_text', __( '30 dni na zwrot', 'flavor-flavor-flavor' ) );
$support_text  = get_theme_mod( 'sjp_support_text', __( 'Wsparcie 7 dni w tygodniu', 'flavor-flavor-flavor' ) );
?>

<section class="sjp-section sjp-guarantee" id="sjp-guarantee">
    <div class="sjp-container sjp-container--narrow">
        <div class="sjp-guarantee__card">

            <div class="sjp-guarantee__shield">
                <?php if ( $guarantee_icon ) : ?>
                    <img src="<?php echo esc_url( $guarantee_icon ); ?>" alt="" width="64" height="64">
                <?php else : ?>
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        <path d="M9 12l2 2 4-4"/>
                    </svg>
                <?php endif; ?>
            </div>

            <h2 class="sjp-guarantee__title">
                <?php if ( $guarantee_days ) : ?>
                    <?php printf( esc_html__( '%s dni — %s', 'flavor-flavor-flavor' ), esc_html( $guarantee_days ), esc_html( $guarantee_title ) ); ?>
                <?php else : ?>
                    <?php echo esc_html( $guarantee_title ); ?>
                <?php endif; ?>
            </h2>

            <?php if ( $guarantee_text ) : ?>
                <p class="sjp-guarantee__text"><?php echo esc_html( $guarantee_text ); ?></p>
            <?php endif; ?>

            <div class="sjp-guarantee__cta-wrap">
                <button class="sjp-btn sjp-btn--primary sjp-btn--lg sjp-buy-now-btn"
                        data-product-id="<?php echo esc_attr( $product_id ); ?>">
                    <?php echo esc_html( $cta_text ); ?> — <?php echo wp_kses_post( $price ); ?>
                </button>
            </div>

            <div class="sjp-guarantee__badges">
                <?php if ( $free_shipping ) : ?>
                    <span class="sjp-guarantee__badge">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                        <?php echo esc_html( $free_shipping ); ?>
                    </span>
                <?php endif; ?>
                <?php if ( $return_text ) : ?>
                    <span class="sjp-guarantee__badge">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                        <?php echo esc_html( $return_text ); ?>
                    </span>
                <?php endif; ?>
                <?php if ( $support_text ) : ?>
                    <span class="sjp-guarantee__badge">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        <?php echo esc_html( $support_text ); ?>
                    </span>
                <?php endif; ?>
                <span class="sjp-guarantee__badge">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    <?php esc_html_e( 'Bezpieczna płatność', 'flavor-flavor-flavor' ); ?>
                </span>
            </div>

        </div>
    </div>
</section>
