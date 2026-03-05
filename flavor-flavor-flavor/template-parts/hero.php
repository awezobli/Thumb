<?php
/**
 * Sekcja Hero — above the fold
 */
$product    = sjp_get_product();
if ( ! $product ) return;

$product_id = $product->get_id();
$price      = $product->get_price_html();
$name       = $product->get_name();
$image_id   = $product->get_image_id();
$image_url  = $image_id ? wp_get_attachment_image_url( $image_id, 'sjp-hero' ) : '';
$gallery    = $product->get_gallery_image_ids();

$headline    = get_theme_mod( 'sjp_hero_headline', '' );
$subheadline = get_theme_mod( 'sjp_hero_subheadline', '' );
$cta_text    = get_theme_mod( 'sjp_cta_text', __( 'Kup teraz', 'flavor-flavor-flavor' ) );
$badge_text  = get_theme_mod( 'sjp_hero_badge', '' );

$review_count = $product->get_review_count();
$avg_rating   = $product->get_average_rating();

$free_shipping  = get_theme_mod( 'sjp_free_shipping_text', __( 'Darmowa dostawa', 'flavor-flavor-flavor' ) );
$return_text    = get_theme_mod( 'sjp_return_text', __( '30 dni na zwrot', 'flavor-flavor-flavor' ) );

if ( ! $headline ) {
    $headline = $name;
}
?>

<section class="sjp-hero" id="sjp-hero">
    <div class="sjp-container sjp-hero__inner">

        <div class="sjp-hero__media">
            <?php if ( $badge_text ) : ?>
                <span class="sjp-hero__badge"><?php echo esc_html( $badge_text ); ?></span>
            <?php endif; ?>

            <?php if ( $image_url ) : ?>
                <img src="<?php echo esc_url( $image_url ); ?>"
                     alt="<?php echo esc_attr( $name ); ?>"
                     class="sjp-hero__image"
                     width="600" height="600"
                     loading="eager"
                     fetchpriority="high">
            <?php endif; ?>

            <?php if ( ! empty( $gallery ) ) : ?>
                <div class="sjp-hero__thumbnails">
                    <?php if ( $image_id ) : ?>
                        <button class="sjp-hero__thumb sjp-hero__thumb--active"
                                data-full="<?php echo esc_url( $image_url ); ?>">
                            <img src="<?php echo esc_url( wp_get_attachment_image_url( $image_id, 'sjp-thumbnail' ) ); ?>"
                                 alt="" width="80" height="80" loading="eager">
                        </button>
                    <?php endif; ?>
                    <?php foreach ( array_slice( $gallery, 0, 4 ) as $gal_id ) : ?>
                        <button class="sjp-hero__thumb"
                                data-full="<?php echo esc_url( wp_get_attachment_image_url( $gal_id, 'sjp-hero' ) ); ?>">
                            <img src="<?php echo esc_url( wp_get_attachment_image_url( $gal_id, 'sjp-thumbnail' ) ); ?>"
                                 alt="" width="80" height="80" loading="eager">
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="sjp-hero__content">
            <h1 class="sjp-hero__headline"><?php echo esc_html( $headline ); ?></h1>

            <?php if ( $subheadline ) : ?>
                <p class="sjp-hero__subheadline"><?php echo esc_html( $subheadline ); ?></p>
            <?php endif; ?>

            <?php if ( $review_count > 0 ) : ?>
                <div class="sjp-hero__rating">
                    <span class="sjp-stars" aria-label="<?php printf( esc_attr__( 'Ocena %s na 5', 'flavor-flavor-flavor' ), $avg_rating ); ?>">
                        <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                            <span class="sjp-star <?php echo $i <= round( $avg_rating ) ? 'sjp-star--filled' : ''; ?>">★</span>
                        <?php endfor; ?>
                    </span>
                    <span class="sjp-hero__review-count">
                        <?php printf( esc_html__( '%s zadowolonych klientów', 'flavor-flavor-flavor' ), number_format_i18n( $review_count ) ); ?>
                    </span>
                </div>
            <?php endif; ?>

            <div class="sjp-hero__price">
                <?php if ( $product->is_on_sale() ) : ?>
                    <span class="sjp-hero__price-old"><?php echo wp_kses_post( wc_price( $product->get_regular_price() ) ); ?></span>
                <?php endif; ?>
                <span class="sjp-hero__price-current"><?php echo wp_kses_post( $price ); ?></span>
            </div>

            <div class="sjp-hero__cta-wrap">
                <button class="sjp-btn sjp-btn--primary sjp-btn--lg sjp-buy-now-btn"
                        data-product-id="<?php echo esc_attr( $product_id ); ?>">
                    <?php echo esc_html( $cta_text ); ?>
                </button>
            </div>

            <div class="sjp-hero__guarantees">
                <?php if ( $free_shipping ) : ?>
                    <span class="sjp-hero__guarantee-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        <?php echo esc_html( $free_shipping ); ?>
                    </span>
                <?php endif; ?>
                <?php if ( $return_text ) : ?>
                    <span class="sjp-hero__guarantee-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        <?php echo esc_html( $return_text ); ?>
                    </span>
                <?php endif; ?>
                <span class="sjp-hero__guarantee-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    <?php esc_html_e( 'Bezpieczna płatność', 'flavor-flavor-flavor' ); ?>
                </span>
            </div>

            <?php
            $trust_logos = get_theme_mod( 'sjp_trust_logos', '' );
            if ( $trust_logos ) :
                $logos = array_filter( array_map( 'trim', explode( "\n", $trust_logos ) ) );
                if ( ! empty( $logos ) ) : ?>
                    <div class="sjp-hero__trust-logos">
                        <?php foreach ( $logos as $logo_url ) : ?>
                            <img src="<?php echo esc_url( $logo_url ); ?>" alt="" class="sjp-hero__trust-logo" loading="lazy" height="30">
                        <?php endforeach; ?>
                    </div>
                <?php endif;
            endif; ?>
        </div>

    </div>
</section>
