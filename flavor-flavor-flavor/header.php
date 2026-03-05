<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php
$product    = sjp_get_product();
$price      = $product ? $product->get_price_html() : '';
$cta_text   = get_theme_mod( 'sjp_cta_text', __( 'Kup teraz', 'flavor-flavor-flavor' ) );
$product_id = sjp_get_product_id();
?>

<header class="sjp-header" id="sjp-header">
    <div class="sjp-header__inner">
        <div class="sjp-header__logo">
            <?php if ( has_custom_logo() ) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="sjp-header__site-name">
                    <?php bloginfo( 'name' ); ?>
                </a>
            <?php endif; ?>
        </div>

        <?php if ( $product ) : ?>
            <button class="sjp-btn sjp-btn--header sjp-buy-now-btn"
                    data-product-id="<?php echo esc_attr( $product_id ); ?>">
                <?php echo esc_html( $cta_text ); ?> — <?php echo wp_kses_post( $price ); ?>
            </button>
        <?php endif; ?>
    </div>
</header>

<main class="sjp-main" id="sjp-main">
