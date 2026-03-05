<?php
/**
 * Sekcja "Jak to działa?" — 3 proste kroki
 */
$section_title = get_theme_mod( 'sjp_howto_title', __( 'Jak to działa?', 'flavor-flavor-flavor' ) );

$steps = array();
for ( $i = 1; $i <= 3; $i++ ) {
    $title = get_theme_mod( "sjp_howto_step_{$i}_title", '' );
    $desc  = get_theme_mod( "sjp_howto_step_{$i}_desc", '' );
    $icon  = get_theme_mod( "sjp_howto_step_{$i}_icon", '' );

    if ( ! $title ) {
        $defaults = array(
            1 => array( __( 'Zamów online', 'flavor-flavor-flavor' ),   __( 'Zamówienie zajmie Ci 60 sekund', 'flavor-flavor-flavor' ) ),
            2 => array( __( 'Rozpakuj i użyj', 'flavor-flavor-flavor' ), __( 'Gotowe do użycia od pierwszej chwili', 'flavor-flavor-flavor' ) ),
            3 => array( __( 'Ciesz się efektem', 'flavor-flavor-flavor' ), __( 'Zobaczysz różnicę od pierwszego dnia', 'flavor-flavor-flavor' ) ),
        );
        $title = $defaults[ $i ][0];
        $desc  = $desc ?: $defaults[ $i ][1];
    }

    $steps[] = array(
        'title' => $title,
        'desc'  => $desc,
        'icon'  => $icon,
    );
}
?>

<section class="sjp-section sjp-howto" id="sjp-howto">
    <div class="sjp-container">
        <?php if ( $section_title ) : ?>
            <h2 class="sjp-section__title"><?php echo esc_html( $section_title ); ?></h2>
        <?php endif; ?>

        <div class="sjp-howto__steps">
            <?php foreach ( $steps as $idx => $step ) : ?>
                <div class="sjp-howto__step">
                    <div class="sjp-howto__number"><?php echo esc_html( $idx + 1 ); ?></div>
                    <?php if ( $step['icon'] ) : ?>
                        <div class="sjp-howto__icon">
                            <?php if ( filter_var( $step['icon'], FILTER_VALIDATE_URL ) ) : ?>
                                <img src="<?php echo esc_url( $step['icon'] ); ?>" alt="" width="64" height="64" loading="lazy">
                            <?php else : ?>
                                <span><?php echo esc_html( $step['icon'] ); ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <h3 class="sjp-howto__title"><?php echo esc_html( $step['title'] ); ?></h3>
                    <?php if ( $step['desc'] ) : ?>
                        <p class="sjp-howto__desc"><?php echo esc_html( $step['desc'] ); ?></p>
                    <?php endif; ?>
                </div>

                <?php if ( $idx < 2 ) : ?>
                    <div class="sjp-howto__arrow" aria-hidden="true">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
