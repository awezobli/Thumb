<?php
/**
 * Sekcja porównania Before/After lub vs. alternatywy
 */
$section_title = get_theme_mod( 'sjp_comparison_title', __( 'Dlaczego warto?', 'flavor-flavor-flavor' ) );
$left_label    = get_theme_mod( 'sjp_comparison_left_label', __( 'Stary sposób', 'flavor-flavor-flavor' ) );
$right_label   = get_theme_mod( 'sjp_comparison_right_label', __( 'Z naszym produktem', 'flavor-flavor-flavor' ) );

$left_image  = get_theme_mod( 'sjp_comparison_left_image', '' );
$right_image = get_theme_mod( 'sjp_comparison_right_image', '' );

$items_left  = array();
$items_right = array();
for ( $i = 1; $i <= 5; $i++ ) {
    $l = get_theme_mod( "sjp_comparison_left_{$i}", '' );
    $r = get_theme_mod( "sjp_comparison_right_{$i}", '' );
    if ( $l ) $items_left[]  = $l;
    if ( $r ) $items_right[] = $r;
}

if ( empty( $items_left ) && empty( $items_right ) ) return;
?>

<section class="sjp-section sjp-comparison" id="sjp-comparison">
    <div class="sjp-container">
        <?php if ( $section_title ) : ?>
            <h2 class="sjp-section__title"><?php echo esc_html( $section_title ); ?></h2>
        <?php endif; ?>

        <div class="sjp-comparison__grid">
            <div class="sjp-comparison__side sjp-comparison__side--old">
                <?php if ( $left_image ) : ?>
                    <img src="<?php echo esc_url( $left_image ); ?>" alt="<?php echo esc_attr( $left_label ); ?>" class="sjp-comparison__image" loading="lazy">
                <?php endif; ?>
                <h3 class="sjp-comparison__label sjp-comparison__label--old">
                    <?php echo esc_html( $left_label ); ?>
                </h3>
                <ul class="sjp-comparison__list sjp-comparison__list--old">
                    <?php foreach ( $items_left as $item ) : ?>
                        <li><span class="sjp-comparison__icon sjp-comparison__icon--bad">✗</span> <?php echo esc_html( $item ); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="sjp-comparison__divider">
                <span>VS</span>
            </div>

            <div class="sjp-comparison__side sjp-comparison__side--new">
                <?php if ( $right_image ) : ?>
                    <img src="<?php echo esc_url( $right_image ); ?>" alt="<?php echo esc_attr( $right_label ); ?>" class="sjp-comparison__image" loading="lazy">
                <?php endif; ?>
                <h3 class="sjp-comparison__label sjp-comparison__label--new">
                    <?php echo esc_html( $right_label ); ?>
                </h3>
                <ul class="sjp-comparison__list sjp-comparison__list--new">
                    <?php foreach ( $items_right as $item ) : ?>
                        <li><span class="sjp-comparison__icon sjp-comparison__icon--good">✓</span> <?php echo esc_html( $item ); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</section>
