<?php
/**
 * Integracja WooCommerce — uproszczenia dla sklepu jednoproduktowego
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ─── Skip Cart — redirect add-to-cart straight to checkout ── */

add_filter( 'woocommerce_add_to_cart_redirect', function () {
    return wc_get_checkout_url();
} );

add_filter( 'wc_add_to_cart_message_html', '__return_empty_string' );

/* ─── Redirect single product page to front page ─────── */

add_action( 'template_redirect', function () {
    if ( is_product() ) {
        wp_safe_redirect( home_url( '/' ), 301 );
        exit;
    }
} );

/* ─── Redirect cart page to checkout ──────────────────── */

add_action( 'template_redirect', function () {
    if ( function_exists( 'is_cart' ) && is_cart() ) {
        wp_safe_redirect( wc_get_checkout_url(), 302 );
        exit;
    }
} );

/* ─── Redirect shop page to front page ────────────────── */

add_action( 'template_redirect', function () {
    if ( function_exists( 'is_shop' ) && is_shop() ) {
        wp_safe_redirect( home_url( '/' ), 301 );
        exit;
    }
} );

/* ─── Simplify checkout fields ────────────────────────── */

add_filter( 'woocommerce_checkout_fields', function ( $fields ) {
    $remove_fields = get_theme_mod( 'sjp_checkout_remove_fields', array() );

    if ( ! is_array( $remove_fields ) ) {
        $remove_fields = array();
    }

    $optional_fields = array(
        'billing_company',
        'billing_address_2',
        'billing_state',
        'order_comments',
    );

    foreach ( $optional_fields as $field ) {
        if ( in_array( $field, $remove_fields, true ) || empty( $remove_fields ) ) {
            $parts = explode( '_', $field, 2 );
            $group = $parts[0];
            $key   = $field;

            if ( $group === 'order' ) {
                unset( $fields['order'][ $key ] );
            } else {
                unset( $fields['billing'][ $key ] );
            }
        }
    }

    if ( isset( $fields['billing']['billing_phone'] ) ) {
        $fields['billing']['billing_phone']['required'] = true;
    }

    $priority = 10;
    $order = array(
        'billing_first_name',
        'billing_last_name',
        'billing_email',
        'billing_phone',
        'billing_address_1',
        'billing_postcode',
        'billing_city',
        'billing_country',
    );

    foreach ( $order as $field_name ) {
        if ( isset( $fields['billing'][ $field_name ] ) ) {
            $fields['billing'][ $field_name ]['priority'] = $priority;
            $priority += 10;
        }
    }

    return $fields;
} );

/* ─── Change "Place order" button text ────────────────── */

add_filter( 'woocommerce_order_button_text', function () {
    $text = get_theme_mod( 'sjp_checkout_button_text', '' );
    return $text ?: __( 'Zamawiam i płacę', 'flavor-flavor-flavor' );
} );

/* ─── Add trust badges after checkout submit ──────────── */

add_action( 'woocommerce_review_order_after_submit', function () {
    $free_shipping = get_theme_mod( 'sjp_free_shipping_text', __( 'Darmowa dostawa', 'flavor-flavor-flavor' ) );
    $return_text   = get_theme_mod( 'sjp_return_text', __( '30 dni na zwrot', 'flavor-flavor-flavor' ) );
    ?>
    <div class="sjp-checkout-trust">
        <span class="sjp-checkout-trust__item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
            <?php esc_html_e( 'Bezpieczna płatność SSL', 'flavor-flavor-flavor' ); ?>
        </span>
        <?php if ( $free_shipping ) : ?>
            <span class="sjp-checkout-trust__item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                <?php echo esc_html( $free_shipping ); ?>
            </span>
        <?php endif; ?>
        <?php if ( $return_text ) : ?>
            <span class="sjp-checkout-trust__item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                <?php echo esc_html( $return_text ); ?>
            </span>
        <?php endif; ?>
    </div>
    <?php
} );

/* ─── Customize "Thank you" page ──────────────────────── */

add_filter( 'woocommerce_thankyou_order_received_text', function ( $text, $order ) {
    if ( ! $order ) return $text;

    $custom_text = get_theme_mod( 'sjp_thankyou_text', '' );
    if ( $custom_text ) {
        return wp_kses_post( $custom_text );
    }

    return sprintf(
        __( 'Dziękujemy za zamówienie! Potwierdzenie wysłaliśmy na %s. Przygotowujemy Twoją paczkę.', 'flavor-flavor-flavor' ),
        '<strong>' . esc_html( $order->get_billing_email() ) . '</strong>'
    );
}, 10, 2 );

/* ─── Add referral section to thank you page ──────────── */

add_action( 'woocommerce_thankyou', function ( $order_id ) {
    $referral_text = get_theme_mod( 'sjp_referral_text', '' );
    $referral_url  = get_theme_mod( 'sjp_referral_url', '' );

    if ( ! $referral_text && ! $referral_url ) return;
    ?>
    <div class="sjp-thankyou-referral" style="margin-top:2rem;padding:1.5rem;background:var(--sjp-primary-light);border-radius:var(--sjp-radius);text-align:center;">
        <?php if ( $referral_text ) : ?>
            <p style="font-size:1rem;font-weight:600;margin-bottom:0.75rem;">
                <?php echo esc_html( $referral_text ); ?>
            </p>
        <?php endif; ?>
        <?php if ( $referral_url ) : ?>
            <div style="display:flex;gap:0.5rem;justify-content:center;flex-wrap:wrap;">
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode( $referral_url ); ?>"
                   target="_blank" rel="noopener" class="sjp-btn sjp-btn--primary" style="font-size:0.875rem;padding:0.5rem 1rem;">
                    Facebook
                </a>
                <button onclick="navigator.clipboard.writeText('<?php echo esc_js( $referral_url ); ?>').then(function(){alert('Link skopiowany!')})"
                        class="sjp-btn sjp-btn--primary" style="font-size:0.875rem;padding:0.5rem 1rem;background:var(--sjp-accent);">
                    <?php esc_html_e( 'Kopiuj link', 'flavor-flavor-flavor' ); ?>
                </button>
            </div>
        <?php endif; ?>
    </div>
    <?php
} );

/* ─── Disable WooCommerce account/login requirement ───── */

add_filter( 'pre_option_woocommerce_enable_guest_checkout', function () {
    return 'yes';
} );

add_filter( 'pre_option_woocommerce_enable_signup_and_login_from_checkout', function () {
    return 'no';
} );

/* ─── Remove unnecessary checkout scripts ─────────────── */

add_action( 'wp_enqueue_scripts', function () {
    if ( ! is_checkout() ) {
        wp_dequeue_script( 'wc-checkout' );
        wp_dequeue_script( 'wc-cart-fragments' );
    }

    wp_dequeue_style( 'wc-blocks-style' );
    wp_dequeue_style( 'wc-blocks-vendors-style' );
}, 99 );

/* ─── JSON-LD structured data for the product ─────────── */

add_action( 'wp_head', function () {
    if ( ! is_front_page() ) return;

    $product = sjp_get_product();
    if ( ! $product ) return;

    $image_id  = $product->get_image_id();
    $image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '';

    $schema = array(
        '@context'    => 'https://schema.org',
        '@type'       => 'Product',
        'name'        => $product->get_name(),
        'description' => wp_strip_all_tags( $product->get_short_description() ?: $product->get_description() ),
        'image'       => $image_url,
        'offers'      => array(
            '@type'         => 'Offer',
            'price'         => $product->get_price(),
            'priceCurrency' => get_woocommerce_currency(),
            'availability'  => $product->is_in_stock()
                ? 'https://schema.org/InStock'
                : 'https://schema.org/OutOfStock',
            'url'           => home_url( '/' ),
        ),
    );

    $avg = $product->get_average_rating();
    $cnt = $product->get_review_count();
    if ( $avg && $cnt ) {
        $schema['aggregateRating'] = array(
            '@type'       => 'AggregateRating',
            'ratingValue' => $avg,
            'reviewCount' => $cnt,
        );
    }

    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
} );
