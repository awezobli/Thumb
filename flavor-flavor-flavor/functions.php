<?php
/**
 * flavor-flavor-flavor — motyw jednoproduktowy dla WooCommerce
 *
 * Prefix: sjp_
 * Text Domain: flavor-flavor-flavor
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'SJP_VERSION', '1.0.0' );
define( 'SJP_DIR', get_template_directory() );
define( 'SJP_URI', get_template_directory_uri() );

/* ─── Theme Setup ─────────────────────────────────────── */

function sjp_setup() {
    load_theme_textdomain( 'flavor-flavor-flavor', SJP_DIR . '/languages' );

    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo', array(
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ) );
    add_theme_support( 'html5', array(
        'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
    ) );
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );

    add_image_size( 'sjp-hero', 1200, 1200, true );
    add_image_size( 'sjp-thumbnail', 600, 600, true );
}
add_action( 'after_setup_theme', 'sjp_setup' );

/* ─── Enqueue Assets ──────────────────────────────────── */

function sjp_enqueue_scripts() {
    wp_enqueue_style(
        'sjp-theme',
        SJP_URI . '/assets/css/theme.css',
        array(),
        SJP_VERSION
    );

    if ( function_exists( 'is_checkout' ) && is_checkout() ) {
        wp_enqueue_style(
            'sjp-checkout',
            SJP_URI . '/assets/css/checkout.css',
            array( 'sjp-theme' ),
            SJP_VERSION
        );
    }

    wp_enqueue_script(
        'sjp-theme',
        SJP_URI . '/assets/js/theme.js',
        array(),
        SJP_VERSION,
        true
    );

    $product_id = sjp_get_product_id();
    wp_localize_script( 'sjp-theme', 'sjpData', array(
        'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
        'checkoutUrl' => function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : '',
        'productId'   => $product_id,
        'nonce'       => wp_create_nonce( 'sjp-nonce' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'sjp_enqueue_scripts' );

/* ─── Include Modules ─────────────────────────────────── */

require_once SJP_DIR . '/inc/customizer.php';
require_once SJP_DIR . '/inc/woocommerce.php';

/* ─── Helper: get the single product ──────────────────── */

function sjp_get_product_id() {
    $id = get_theme_mod( 'sjp_product_id', 0 );
    if ( $id ) {
        return (int) $id;
    }
    if ( ! function_exists( 'wc_get_products' ) ) {
        return 0;
    }
    $products = wc_get_products( array(
        'limit'   => 1,
        'status'  => 'publish',
        'orderby' => 'date',
        'order'   => 'DESC',
    ) );
    return ! empty( $products ) ? $products[0]->get_id() : 0;
}

function sjp_get_product() {
    $id = sjp_get_product_id();
    if ( ! $id || ! function_exists( 'wc_get_product' ) ) {
        return null;
    }
    return wc_get_product( $id );
}

/* ─── AJAX: Kup teraz → dodaj do koszyka → checkout ──── */

function sjp_ajax_buy_now() {
    check_ajax_referer( 'sjp-nonce', 'nonce' );

    if ( ! function_exists( 'WC' ) ) {
        wp_send_json_error( 'WooCommerce nie jest aktywne.' );
    }

    $product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
    $quantity   = isset( $_POST['quantity'] )   ? max( 1, absint( $_POST['quantity'] ) ) : 1;

    if ( ! $product_id ) {
        wp_send_json_error( 'Brak ID produktu.' );
    }

    WC()->cart->empty_cart();
    $added = WC()->cart->add_to_cart( $product_id, $quantity );

    if ( $added ) {
        wp_send_json_success( array( 'redirect' => wc_get_checkout_url() ) );
    }

    wp_send_json_error( 'Nie udało się dodać produktu do koszyka.' );
}
add_action( 'wp_ajax_sjp_buy_now',        'sjp_ajax_buy_now' );
add_action( 'wp_ajax_nopriv_sjp_buy_now', 'sjp_ajax_buy_now' );

/* ─── Disable default WooCommerce styles ──────────────── */

add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

/* ─── Remove WooCommerce breadcrumbs ─────────────────── */

remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

/* ─── Widget area for footer ──────────────────────────── */

function sjp_widgets_init() {
    register_sidebar( array(
        'name'          => __( 'Stopka', 'flavor-flavor-flavor' ),
        'id'            => 'footer-1',
        'before_widget' => '<div class="sjp-footer-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="sjp-footer-widget__title">',
        'after_title'   => '</h4>',
    ) );
}
add_action( 'widgets_init', 'sjp_widgets_init' );
