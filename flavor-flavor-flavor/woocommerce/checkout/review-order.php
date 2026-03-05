<?php
/**
 * Uproszczone podsumowanie zamówienia na checkout
 *
 * Nadpisuje: woocommerce/templates/checkout/review-order.php
 */

defined( 'ABSPATH' ) || exit;
?>

<table class="shop_table woocommerce-checkout-review-order-table">
    <thead>
        <tr>
            <th class="product-name"><?php esc_html_e( 'Produkt', 'flavor-flavor-flavor' ); ?></th>
            <th class="product-total"><?php esc_html_e( 'Razem', 'flavor-flavor-flavor' ); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        do_action( 'woocommerce_review_order_before_cart_contents' );

        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
            $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

            if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                ?>
                <tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
                    <td class="product-name">
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <?php
                            $thumbnail = $_product->get_image( array( 48, 48 ) );
                            if ( $thumbnail ) {
                                echo '<div style="flex-shrink:0;width:48px;height:48px;border-radius:6px;overflow:hidden;">' . $thumbnail . '</div>';
                            }
                            ?>
                            <div>
                                <strong><?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) ); ?></strong>
                                <span style="display:block;font-size:0.8125rem;color:var(--sjp-text-muted);">
                                    <?php echo apply_filters( 'woocommerce_checkout_cart_item_quantity', ' &times;&nbsp;' . $cart_item['quantity'], $cart_item, $cart_item_key ); ?>
                                </span>
                                <?php echo wc_get_formatted_cart_item_data( $cart_item ); ?>
                            </div>
                        </div>
                    </td>
                    <td class="product-total">
                        <?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?>
                    </td>
                </tr>
                <?php
            }
        }

        do_action( 'woocommerce_review_order_after_cart_contents' );
        ?>
    </tbody>
    <tfoot>
        <tr class="cart-subtotal">
            <th><?php esc_html_e( 'Suma częściowa', 'flavor-flavor-flavor' ); ?></th>
            <td><?php wc_cart_totals_subtotal_html(); ?></td>
        </tr>

        <?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
            <tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
                <th><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
                <td><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
            </tr>
        <?php endforeach; ?>

        <?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
            <tr class="fee">
                <th><?php echo esc_html( $fee->name ); ?></th>
                <td><?php wc_cart_totals_fee_html( $fee ); ?></td>
            </tr>
        <?php endforeach; ?>

        <?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
            <?php if ( 'itemized' === get_option( 'woocommerce_tax_display_cart' ) ) : ?>
                <?php foreach ( WC()->cart->get_tax_totals() as $code => $tax_total ) : ?>
                    <tr class="tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
                        <th><?php echo esc_html( $tax_total->label ); ?></th>
                        <td><?php echo wp_kses_post( $tax_total->formatted_amount ); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr class="tax-total">
                    <th><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></th>
                    <td><?php wc_cart_totals_taxes_total_html(); ?></td>
                </tr>
            <?php endif; ?>
        <?php endif; ?>

        <?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

        <tr class="order-total">
            <th><?php esc_html_e( 'Do zapłaty', 'flavor-flavor-flavor' ); ?></th>
            <td><?php wc_cart_totals_order_total_html(); ?></td>
        </tr>

        <?php do_action( 'woocommerce_review_order_after_order_total' ); ?>
    </tfoot>
</table>
