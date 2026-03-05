<?php
/**
 * Sekcja FAQ — accordion, likwidacja obiekcji
 */
$section_title = get_theme_mod( 'sjp_faq_title', __( 'Najczęstsze pytania', 'flavor-flavor-flavor' ) );

$faqs = array();
for ( $i = 1; $i <= 8; $i++ ) {
    $q = get_theme_mod( "sjp_faq_{$i}_question", '' );
    $a = get_theme_mod( "sjp_faq_{$i}_answer", '' );
    if ( $q && $a ) {
        $faqs[] = array( 'q' => $q, 'a' => $a );
    }
}

if ( empty( $faqs ) ) return;
?>

<section class="sjp-section sjp-faq" id="sjp-faq">
    <div class="sjp-container sjp-container--narrow">
        <?php if ( $section_title ) : ?>
            <h2 class="sjp-section__title"><?php echo esc_html( $section_title ); ?></h2>
        <?php endif; ?>

        <div class="sjp-faq__list" itemscope itemtype="https://schema.org/FAQPage">
            <?php foreach ( $faqs as $faq ) : ?>
                <div class="sjp-faq__item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <button class="sjp-faq__question" aria-expanded="false" itemprop="name">
                        <span><?php echo esc_html( $faq['q'] ); ?></span>
                        <svg class="sjp-faq__chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </button>
                    <div class="sjp-faq__answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer" hidden>
                        <div itemprop="text">
                            <?php echo wp_kses_post( wpautop( $faq['a'] ) ); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
