<?php
/**
 * Sekcja opinii klientów
 *
 * Pobiera opinie z WooCommerce (komentarze produktu) lub z Customizera.
 */
$product    = sjp_get_product();
$product_id = $product ? $product->get_id() : 0;

$section_title = get_theme_mod( 'sjp_testimonials_title', __( 'Co mówią nasi klienci', 'flavor-flavor-flavor' ) );
$source        = get_theme_mod( 'sjp_testimonials_source', 'woocommerce' );

$testimonials = array();

if ( $source === 'woocommerce' && $product_id ) {
    $comments = get_comments( array(
        'post_id' => $product_id,
        'status'  => 'approve',
        'type'    => 'review',
        'number'  => 6,
        'orderby' => 'comment_date',
        'order'   => 'DESC',
    ) );

    foreach ( $comments as $comment ) {
        $rating = get_comment_meta( $comment->comment_ID, 'rating', true );
        $testimonials[] = array(
            'name'   => $comment->comment_author,
            'text'   => $comment->comment_content,
            'rating' => $rating ? (int) $rating : 5,
            'avatar' => get_avatar_url( $comment->comment_author_email, array( 'size' => 80 ) ),
            'verified' => true,
        );
    }
} else {
    for ( $i = 1; $i <= 6; $i++ ) {
        $name   = get_theme_mod( "sjp_testimonial_{$i}_name", '' );
        $text   = get_theme_mod( "sjp_testimonial_{$i}_text", '' );
        $rating = get_theme_mod( "sjp_testimonial_{$i}_rating", 5 );
        $photo  = get_theme_mod( "sjp_testimonial_{$i}_photo", '' );
        if ( $name && $text ) {
            $testimonials[] = array(
                'name'     => $name,
                'text'     => $text,
                'rating'   => (int) $rating,
                'avatar'   => $photo,
                'verified' => true,
            );
        }
    }
}

if ( empty( $testimonials ) ) return;
?>

<section class="sjp-section sjp-testimonials" id="sjp-testimonials">
    <div class="sjp-container">
        <?php if ( $section_title ) : ?>
            <h2 class="sjp-section__title"><?php echo esc_html( $section_title ); ?></h2>
        <?php endif; ?>

        <div class="sjp-testimonials__grid">
            <?php foreach ( $testimonials as $t ) : ?>
                <div class="sjp-testimonials__card">
                    <div class="sjp-testimonials__stars">
                        <?php for ( $s = 1; $s <= 5; $s++ ) : ?>
                            <span class="sjp-star <?php echo $s <= $t['rating'] ? 'sjp-star--filled' : ''; ?>">★</span>
                        <?php endfor; ?>
                    </div>
                    <blockquote class="sjp-testimonials__text">
                        <?php echo esc_html( $t['text'] ); ?>
                    </blockquote>
                    <div class="sjp-testimonials__author">
                        <?php if ( ! empty( $t['avatar'] ) ) : ?>
                            <img src="<?php echo esc_url( $t['avatar'] ); ?>"
                                 alt="<?php echo esc_attr( $t['name'] ); ?>"
                                 class="sjp-testimonials__avatar"
                                 width="40" height="40" loading="lazy">
                        <?php endif; ?>
                        <div>
                            <span class="sjp-testimonials__name"><?php echo esc_html( $t['name'] ); ?></span>
                            <?php if ( $t['verified'] ) : ?>
                                <span class="sjp-testimonials__verified">
                                    ✓ <?php esc_html_e( 'Zweryfikowany zakup', 'flavor-flavor-flavor' ); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
