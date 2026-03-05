<?php
/**
 * Sekcja korzyści (nie cechy!)
 */
$section_title = get_theme_mod( 'sjp_benefits_title', __( 'Dlaczego klienci to kochają', 'flavor-flavor-flavor' ) );

$benefits = array();
for ( $i = 1; $i <= 6; $i++ ) {
    $title = get_theme_mod( "sjp_benefit_{$i}_title", '' );
    $desc  = get_theme_mod( "sjp_benefit_{$i}_desc", '' );
    $icon  = get_theme_mod( "sjp_benefit_{$i}_icon", '' );
    if ( $title ) {
        $benefits[] = array(
            'title' => $title,
            'desc'  => $desc,
            'icon'  => $icon,
        );
    }
}

if ( empty( $benefits ) ) return;
?>

<section class="sjp-section sjp-benefits" id="sjp-benefits">
    <div class="sjp-container">
        <?php if ( $section_title ) : ?>
            <h2 class="sjp-section__title"><?php echo esc_html( $section_title ); ?></h2>
        <?php endif; ?>

        <div class="sjp-benefits__grid">
            <?php foreach ( $benefits as $benefit ) : ?>
                <div class="sjp-benefits__item">
                    <?php if ( $benefit['icon'] ) : ?>
                        <div class="sjp-benefits__icon">
                            <?php if ( filter_var( $benefit['icon'], FILTER_VALIDATE_URL ) ) : ?>
                                <img src="<?php echo esc_url( $benefit['icon'] ); ?>" alt="" width="48" height="48" loading="lazy">
                            <?php else : ?>
                                <span class="sjp-benefits__emoji"><?php echo esc_html( $benefit['icon'] ); ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <h3 class="sjp-benefits__title"><?php echo esc_html( $benefit['title'] ); ?></h3>
                    <?php if ( $benefit['desc'] ) : ?>
                        <p class="sjp-benefits__desc"><?php echo esc_html( $benefit['desc'] ); ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
