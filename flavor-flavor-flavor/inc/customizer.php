<?php
/**
 * Customizer — pełna konfiguracja wszystkich sekcji motywu
 *
 * Wszystkie ustawienia dostępne z Wygląd → Dostosuj
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'customize_register', function ( WP_Customize_Manager $wp_customize ) {

    /* ═══════════════════════════════════════════════════════
       PANEL: Sklep jednoproduktowy
       ═══════════════════════════════════════════════════════ */

    $wp_customize->add_panel( 'sjp_panel', array(
        'title'    => __( 'Sklep jednoproduktowy', 'flavor-flavor-flavor' ),
        'priority' => 30,
    ) );

    /* ─── SECTION: Produkt ────────────────────────────── */

    $wp_customize->add_section( 'sjp_product_section', array(
        'title' => __( 'Produkt', 'flavor-flavor-flavor' ),
        'panel' => 'sjp_panel',
        'priority' => 10,
    ) );

    $wp_customize->add_setting( 'sjp_product_id', array(
        'default'           => 0,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'sjp_product_id', array(
        'label'       => __( 'ID produktu WooCommerce', 'flavor-flavor-flavor' ),
        'description' => __( 'Wpisz ID produktu. Zostaw 0, aby użyć najnowszego.', 'flavor-flavor-flavor' ),
        'section'     => 'sjp_product_section',
        'type'        => 'number',
        'input_attrs' => array( 'min' => 0 ),
    ) );

    /* ─── SECTION: Hero ───────────────────────────────── */

    $wp_customize->add_section( 'sjp_hero_section', array(
        'title' => __( 'Sekcja Hero', 'flavor-flavor-flavor' ),
        'panel' => 'sjp_panel',
        'priority' => 20,
    ) );

    $hero_settings = array(
        'sjp_hero_headline' => array(
            'label'   => __( 'Nagłówek', 'flavor-flavor-flavor' ),
            'desc'    => __( 'Nagłówek skupiony na problemie/rozwiązaniu. Zostaw puste, by użyć nazwy produktu.', 'flavor-flavor-flavor' ),
            'type'    => 'text',
            'default' => '',
        ),
        'sjp_hero_subheadline' => array(
            'label'   => __( 'Podnagłówek', 'flavor-flavor-flavor' ),
            'type'    => 'textarea',
            'default' => '',
        ),
        'sjp_hero_badge' => array(
            'label'   => __( 'Plakietka na zdjęciu', 'flavor-flavor-flavor' ),
            'desc'    => __( 'Np. "BESTSELLER", "-50%", "NOWOŚĆ"', 'flavor-flavor-flavor' ),
            'type'    => 'text',
            'default' => '',
        ),
        'sjp_cta_text' => array(
            'label'   => __( 'Tekst przycisku CTA', 'flavor-flavor-flavor' ),
            'type'    => 'text',
            'default' => __( 'Kup teraz', 'flavor-flavor-flavor' ),
        ),
        'sjp_free_shipping_text' => array(
            'label'   => __( 'Tekst darmowej dostawy', 'flavor-flavor-flavor' ),
            'type'    => 'text',
            'default' => __( 'Darmowa dostawa', 'flavor-flavor-flavor' ),
        ),
        'sjp_return_text' => array(
            'label'   => __( 'Tekst zwrotu', 'flavor-flavor-flavor' ),
            'type'    => 'text',
            'default' => __( '30 dni na zwrot', 'flavor-flavor-flavor' ),
        ),
        'sjp_support_text' => array(
            'label'   => __( 'Tekst wsparcia', 'flavor-flavor-flavor' ),
            'type'    => 'text',
            'default' => __( 'Wsparcie 7 dni w tygodniu', 'flavor-flavor-flavor' ),
        ),
        'sjp_trust_logos' => array(
            'label'   => __( 'Loga zaufania (URL, po jednym na linię)', 'flavor-flavor-flavor' ),
            'type'    => 'textarea',
            'default' => '',
        ),
    );

    foreach ( $hero_settings as $id => $s ) {
        $wp_customize->add_setting( $id, array(
            'default'           => $s['default'],
            'sanitize_callback' => ( $s['type'] === 'textarea' ) ? 'sanitize_textarea_field' : 'sanitize_text_field',
        ) );
        $control_args = array(
            'label'   => $s['label'],
            'section' => 'sjp_hero_section',
            'type'    => $s['type'],
        );
        if ( isset( $s['desc'] ) ) {
            $control_args['description'] = $s['desc'];
        }
        $wp_customize->add_control( $id, $control_args );
    }

    /* ─── SECTION: Wideo ──────────────────────────────── */

    $wp_customize->add_section( 'sjp_video_section', array(
        'title' => __( 'Sekcja Wideo', 'flavor-flavor-flavor' ),
        'panel' => 'sjp_panel',
        'priority' => 30,
    ) );

    $wp_customize->add_setting( 'sjp_video_show', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ) );
    $wp_customize->add_control( 'sjp_video_show', array(
        'label'   => __( 'Pokaż sekcję wideo', 'flavor-flavor-flavor' ),
        'section' => 'sjp_video_section',
        'type'    => 'checkbox',
    ) );

    $wp_customize->add_setting( 'sjp_video_url', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'sjp_video_url', array(
        'label'       => __( 'URL wideo', 'flavor-flavor-flavor' ),
        'description' => __( 'YouTube, Vimeo lub bezpośredni URL do pliku .mp4', 'flavor-flavor-flavor' ),
        'section'     => 'sjp_video_section',
        'type'        => 'url',
    ) );

    $wp_customize->add_setting( 'sjp_video_title', array(
        'default'           => __( 'Zobacz jak to działa', 'flavor-flavor-flavor' ),
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'sjp_video_title', array(
        'label'   => __( 'Tytuł sekcji', 'flavor-flavor-flavor' ),
        'section' => 'sjp_video_section',
        'type'    => 'text',
    ) );

    $wp_customize->add_setting( 'sjp_video_description', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'sjp_video_description', array(
        'label'   => __( 'Opis pod tytułem', 'flavor-flavor-flavor' ),
        'section' => 'sjp_video_section',
        'type'    => 'text',
    ) );

    /* ─── SECTION: Korzyści ───────────────────────────── */

    $wp_customize->add_section( 'sjp_benefits_section', array(
        'title' => __( 'Sekcja Korzyści', 'flavor-flavor-flavor' ),
        'panel' => 'sjp_panel',
        'priority' => 40,
    ) );

    $wp_customize->add_setting( 'sjp_benefits_show', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ) );
    $wp_customize->add_control( 'sjp_benefits_show', array(
        'label'   => __( 'Pokaż sekcję korzyści', 'flavor-flavor-flavor' ),
        'section' => 'sjp_benefits_section',
        'type'    => 'checkbox',
    ) );

    $wp_customize->add_setting( 'sjp_benefits_title', array(
        'default'           => __( 'Dlaczego klienci to kochają', 'flavor-flavor-flavor' ),
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'sjp_benefits_title', array(
        'label'   => __( 'Tytuł sekcji', 'flavor-flavor-flavor' ),
        'section' => 'sjp_benefits_section',
        'type'    => 'text',
    ) );

    for ( $i = 1; $i <= 6; $i++ ) {
        $wp_customize->add_setting( "sjp_benefit_{$i}_icon", array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_control( "sjp_benefit_{$i}_icon", array(
            'label'       => sprintf( __( 'Korzyść %d — Ikona', 'flavor-flavor-flavor' ), $i ),
            'description' => __( 'Emoji lub URL do obrazka', 'flavor-flavor-flavor' ),
            'section'     => 'sjp_benefits_section',
            'type'        => 'text',
        ) );

        $wp_customize->add_setting( "sjp_benefit_{$i}_title", array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_control( "sjp_benefit_{$i}_title", array(
            'label'   => sprintf( __( 'Korzyść %d — Tytuł', 'flavor-flavor-flavor' ), $i ),
            'section' => 'sjp_benefits_section',
            'type'    => 'text',
        ) );

        $wp_customize->add_setting( "sjp_benefit_{$i}_desc", array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_control( "sjp_benefit_{$i}_desc", array(
            'label'   => sprintf( __( 'Korzyść %d — Opis', 'flavor-flavor-flavor' ), $i ),
            'section' => 'sjp_benefits_section',
            'type'    => 'text',
        ) );
    }

    /* ─── SECTION: Porównanie ─────────────────────────── */

    $wp_customize->add_section( 'sjp_comparison_section', array(
        'title' => __( 'Sekcja Porównanie', 'flavor-flavor-flavor' ),
        'panel' => 'sjp_panel',
        'priority' => 50,
    ) );

    $wp_customize->add_setting( 'sjp_comparison_show', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ) );
    $wp_customize->add_control( 'sjp_comparison_show', array(
        'label'   => __( 'Pokaż sekcję porównania', 'flavor-flavor-flavor' ),
        'section' => 'sjp_comparison_section',
        'type'    => 'checkbox',
    ) );

    $wp_customize->add_setting( 'sjp_comparison_title', array(
        'default'           => __( 'Dlaczego warto?', 'flavor-flavor-flavor' ),
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'sjp_comparison_title', array(
        'label'   => __( 'Tytuł sekcji', 'flavor-flavor-flavor' ),
        'section' => 'sjp_comparison_section',
        'type'    => 'text',
    ) );

    $wp_customize->add_setting( 'sjp_comparison_left_label', array(
        'default'           => __( 'Stary sposób', 'flavor-flavor-flavor' ),
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'sjp_comparison_left_label', array(
        'label'   => __( 'Etykieta lewej strony (negatywna)', 'flavor-flavor-flavor' ),
        'section' => 'sjp_comparison_section',
        'type'    => 'text',
    ) );

    $wp_customize->add_setting( 'sjp_comparison_right_label', array(
        'default'           => __( 'Z naszym produktem', 'flavor-flavor-flavor' ),
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'sjp_comparison_right_label', array(
        'label'   => __( 'Etykieta prawej strony (pozytywna)', 'flavor-flavor-flavor' ),
        'section' => 'sjp_comparison_section',
        'type'    => 'text',
    ) );

    $wp_customize->add_setting( 'sjp_comparison_left_image', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'sjp_comparison_left_image', array(
        'label'   => __( 'Obrazek — stary sposób', 'flavor-flavor-flavor' ),
        'section' => 'sjp_comparison_section',
    ) ) );

    $wp_customize->add_setting( 'sjp_comparison_right_image', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'sjp_comparison_right_image', array(
        'label'   => __( 'Obrazek — z naszym produktem', 'flavor-flavor-flavor' ),
        'section' => 'sjp_comparison_section',
    ) ) );

    for ( $i = 1; $i <= 5; $i++ ) {
        $wp_customize->add_setting( "sjp_comparison_left_{$i}", array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_control( "sjp_comparison_left_{$i}", array(
            'label'   => sprintf( __( 'Wada %d (stary sposób)', 'flavor-flavor-flavor' ), $i ),
            'section' => 'sjp_comparison_section',
            'type'    => 'text',
        ) );

        $wp_customize->add_setting( "sjp_comparison_right_{$i}", array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_control( "sjp_comparison_right_{$i}", array(
            'label'   => sprintf( __( 'Zaleta %d (nasz produkt)', 'flavor-flavor-flavor' ), $i ),
            'section' => 'sjp_comparison_section',
            'type'    => 'text',
        ) );
    }

    /* ─── SECTION: Opinie ─────────────────────────────── */

    $wp_customize->add_section( 'sjp_testimonials_section', array(
        'title' => __( 'Sekcja Opinie', 'flavor-flavor-flavor' ),
        'panel' => 'sjp_panel',
        'priority' => 60,
    ) );

    $wp_customize->add_setting( 'sjp_testimonials_show', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ) );
    $wp_customize->add_control( 'sjp_testimonials_show', array(
        'label'   => __( 'Pokaż sekcję opinii', 'flavor-flavor-flavor' ),
        'section' => 'sjp_testimonials_section',
        'type'    => 'checkbox',
    ) );

    $wp_customize->add_setting( 'sjp_testimonials_title', array(
        'default'           => __( 'Co mówią nasi klienci', 'flavor-flavor-flavor' ),
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'sjp_testimonials_title', array(
        'label'   => __( 'Tytuł sekcji', 'flavor-flavor-flavor' ),
        'section' => 'sjp_testimonials_section',
        'type'    => 'text',
    ) );

    $wp_customize->add_setting( 'sjp_testimonials_source', array(
        'default'           => 'woocommerce',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'sjp_testimonials_source', array(
        'label'   => __( 'Źródło opinii', 'flavor-flavor-flavor' ),
        'section' => 'sjp_testimonials_section',
        'type'    => 'select',
        'choices' => array(
            'woocommerce' => __( 'Opinie WooCommerce (z produktu)', 'flavor-flavor-flavor' ),
            'custom'      => __( 'Własne (wpisane poniżej)', 'flavor-flavor-flavor' ),
        ),
    ) );

    for ( $i = 1; $i <= 6; $i++ ) {
        $wp_customize->add_setting( "sjp_testimonial_{$i}_name", array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_control( "sjp_testimonial_{$i}_name", array(
            'label'   => sprintf( __( 'Opinia %d — Imię', 'flavor-flavor-flavor' ), $i ),
            'section' => 'sjp_testimonials_section',
            'type'    => 'text',
        ) );

        $wp_customize->add_setting( "sjp_testimonial_{$i}_text", array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_textarea_field',
        ) );
        $wp_customize->add_control( "sjp_testimonial_{$i}_text", array(
            'label'   => sprintf( __( 'Opinia %d — Treść', 'flavor-flavor-flavor' ), $i ),
            'section' => 'sjp_testimonials_section',
            'type'    => 'textarea',
        ) );

        $wp_customize->add_setting( "sjp_testimonial_{$i}_rating", array(
            'default'           => 5,
            'sanitize_callback' => 'absint',
        ) );
        $wp_customize->add_control( "sjp_testimonial_{$i}_rating", array(
            'label'       => sprintf( __( 'Opinia %d — Ocena (1-5)', 'flavor-flavor-flavor' ), $i ),
            'section'     => 'sjp_testimonials_section',
            'type'        => 'number',
            'input_attrs' => array( 'min' => 1, 'max' => 5 ),
        ) );

        $wp_customize->add_setting( "sjp_testimonial_{$i}_photo", array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ) );
        $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, "sjp_testimonial_{$i}_photo", array(
            'label'   => sprintf( __( 'Opinia %d — Zdjęcie', 'flavor-flavor-flavor' ), $i ),
            'section' => 'sjp_testimonials_section',
        ) ) );
    }

    /* ─── SECTION: Jak to działa ──────────────────────── */

    $wp_customize->add_section( 'sjp_howto_section', array(
        'title' => __( 'Sekcja "Jak to działa"', 'flavor-flavor-flavor' ),
        'panel' => 'sjp_panel',
        'priority' => 70,
    ) );

    $wp_customize->add_setting( 'sjp_howto_show', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ) );
    $wp_customize->add_control( 'sjp_howto_show', array(
        'label'   => __( 'Pokaż sekcję', 'flavor-flavor-flavor' ),
        'section' => 'sjp_howto_section',
        'type'    => 'checkbox',
    ) );

    $wp_customize->add_setting( 'sjp_howto_title', array(
        'default'           => __( 'Jak to działa?', 'flavor-flavor-flavor' ),
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'sjp_howto_title', array(
        'label'   => __( 'Tytuł sekcji', 'flavor-flavor-flavor' ),
        'section' => 'sjp_howto_section',
        'type'    => 'text',
    ) );

    for ( $i = 1; $i <= 3; $i++ ) {
        $wp_customize->add_setting( "sjp_howto_step_{$i}_icon", array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_control( "sjp_howto_step_{$i}_icon", array(
            'label'       => sprintf( __( 'Krok %d — Ikona', 'flavor-flavor-flavor' ), $i ),
            'description' => __( 'Emoji lub URL obrazka', 'flavor-flavor-flavor' ),
            'section'     => 'sjp_howto_section',
            'type'        => 'text',
        ) );

        $wp_customize->add_setting( "sjp_howto_step_{$i}_title", array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_control( "sjp_howto_step_{$i}_title", array(
            'label'   => sprintf( __( 'Krok %d — Tytuł', 'flavor-flavor-flavor' ), $i ),
            'section' => 'sjp_howto_section',
            'type'    => 'text',
        ) );

        $wp_customize->add_setting( "sjp_howto_step_{$i}_desc", array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_control( "sjp_howto_step_{$i}_desc", array(
            'label'   => sprintf( __( 'Krok %d — Opis', 'flavor-flavor-flavor' ), $i ),
            'section' => 'sjp_howto_section',
            'type'    => 'text',
        ) );
    }

    /* ─── SECTION: FAQ ────────────────────────────────── */

    $wp_customize->add_section( 'sjp_faq_section', array(
        'title' => __( 'Sekcja FAQ', 'flavor-flavor-flavor' ),
        'panel' => 'sjp_panel',
        'priority' => 80,
    ) );

    $wp_customize->add_setting( 'sjp_faq_show', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ) );
    $wp_customize->add_control( 'sjp_faq_show', array(
        'label'   => __( 'Pokaż sekcję FAQ', 'flavor-flavor-flavor' ),
        'section' => 'sjp_faq_section',
        'type'    => 'checkbox',
    ) );

    $wp_customize->add_setting( 'sjp_faq_title', array(
        'default'           => __( 'Najczęstsze pytania', 'flavor-flavor-flavor' ),
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'sjp_faq_title', array(
        'label'   => __( 'Tytuł sekcji', 'flavor-flavor-flavor' ),
        'section' => 'sjp_faq_section',
        'type'    => 'text',
    ) );

    for ( $i = 1; $i <= 8; $i++ ) {
        $wp_customize->add_setting( "sjp_faq_{$i}_question", array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_control( "sjp_faq_{$i}_question", array(
            'label'   => sprintf( __( 'FAQ %d — Pytanie', 'flavor-flavor-flavor' ), $i ),
            'section' => 'sjp_faq_section',
            'type'    => 'text',
        ) );

        $wp_customize->add_setting( "sjp_faq_{$i}_answer", array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_textarea_field',
        ) );
        $wp_customize->add_control( "sjp_faq_{$i}_answer", array(
            'label'   => sprintf( __( 'FAQ %d — Odpowiedź', 'flavor-flavor-flavor' ), $i ),
            'section' => 'sjp_faq_section',
            'type'    => 'textarea',
        ) );
    }

    /* ─── SECTION: Gwarancja ──────────────────────────── */

    $wp_customize->add_section( 'sjp_guarantee_section', array(
        'title' => __( 'Sekcja Gwarancja', 'flavor-flavor-flavor' ),
        'panel' => 'sjp_panel',
        'priority' => 90,
    ) );

    $wp_customize->add_setting( 'sjp_guarantee_title', array(
        'default'           => __( 'Gwarancja satysfakcji', 'flavor-flavor-flavor' ),
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'sjp_guarantee_title', array(
        'label'   => __( 'Tytuł gwarancji', 'flavor-flavor-flavor' ),
        'section' => 'sjp_guarantee_section',
        'type'    => 'text',
    ) );

    $wp_customize->add_setting( 'sjp_guarantee_text', array(
        'default'           => __( 'Jeśli z jakiegokolwiek powodu nie będziesz zadowolony — zwrócimy Ci 100% pieniędzy. Bez pytań. Bez haczyków.', 'flavor-flavor-flavor' ),
        'sanitize_callback' => 'sanitize_textarea_field',
    ) );
    $wp_customize->add_control( 'sjp_guarantee_text', array(
        'label'   => __( 'Treść gwarancji', 'flavor-flavor-flavor' ),
        'section' => 'sjp_guarantee_section',
        'type'    => 'textarea',
    ) );

    $wp_customize->add_setting( 'sjp_guarantee_days', array(
        'default'           => '30',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'sjp_guarantee_days', array(
        'label'   => __( 'Liczba dni gwarancji', 'flavor-flavor-flavor' ),
        'section' => 'sjp_guarantee_section',
        'type'    => 'text',
    ) );

    $wp_customize->add_setting( 'sjp_guarantee_icon', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'sjp_guarantee_icon', array(
        'label'   => __( 'Ikona gwarancji (opcjonalnie)', 'flavor-flavor-flavor' ),
        'section' => 'sjp_guarantee_section',
    ) ) );

    /* ─── SECTION: Checkout ───────────────────────────── */

    $wp_customize->add_section( 'sjp_checkout_section', array(
        'title' => __( 'Checkout', 'flavor-flavor-flavor' ),
        'panel' => 'sjp_panel',
        'priority' => 100,
    ) );

    $wp_customize->add_setting( 'sjp_checkout_button_text', array(
        'default'           => __( 'Zamawiam i płacę', 'flavor-flavor-flavor' ),
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'sjp_checkout_button_text', array(
        'label'   => __( 'Tekst przycisku zamówienia', 'flavor-flavor-flavor' ),
        'section' => 'sjp_checkout_section',
        'type'    => 'text',
    ) );

    $wp_customize->add_setting( 'sjp_thankyou_text', array(
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post',
    ) );
    $wp_customize->add_control( 'sjp_thankyou_text', array(
        'label'       => __( 'Tekst strony "Dziękujemy"', 'flavor-flavor-flavor' ),
        'description' => __( 'Zostaw puste dla domyślnego. Obsługuje HTML.', 'flavor-flavor-flavor' ),
        'section'     => 'sjp_checkout_section',
        'type'        => 'textarea',
    ) );

    $wp_customize->add_setting( 'sjp_referral_text', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'sjp_referral_text', array(
        'label'       => __( 'Tekst programu poleceń', 'flavor-flavor-flavor' ),
        'description' => __( 'Np. "Poleć znajomemu i otrzymaj 20 PLN zniżki"', 'flavor-flavor-flavor' ),
        'section'     => 'sjp_checkout_section',
        'type'        => 'text',
    ) );

    $wp_customize->add_setting( 'sjp_referral_url', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'sjp_referral_url', array(
        'label'   => __( 'URL do udostępnienia (polecenia)', 'flavor-flavor-flavor' ),
        'section' => 'sjp_checkout_section',
        'type'    => 'url',
    ) );

    /* ─── SECTION: Kolory ─────────────────────────────── */

    $wp_customize->add_section( 'sjp_colors_section', array(
        'title' => __( 'Kolory', 'flavor-flavor-flavor' ),
        'panel' => 'sjp_panel',
        'priority' => 110,
    ) );

    $colors = array(
        'sjp_color_primary'  => array( __( 'Kolor główny (CTA)', 'flavor-flavor-flavor' ), '#2563eb' ),
        'sjp_color_accent'   => array( __( 'Kolor akcentu (potwierdzenia)', 'flavor-flavor-flavor' ), '#16a34a' ),
        'sjp_color_text'     => array( __( 'Kolor tekstu', 'flavor-flavor-flavor' ), '#111827' ),
        'sjp_color_bg'       => array( __( 'Kolor tła', 'flavor-flavor-flavor' ), '#ffffff' ),
    );

    foreach ( $colors as $id => $c ) {
        $wp_customize->add_setting( $id, array(
            'default'           => $c[1],
            'sanitize_callback' => 'sanitize_hex_color',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $id, array(
            'label'   => $c[0],
            'section' => 'sjp_colors_section',
        ) ) );
    }

    /* ─── SECTION: Dane firmy (stopka / prawne) ───────── */

    $wp_customize->add_section( 'sjp_legal_section', array(
        'title' => __( 'Dane firmy i prawne', 'flavor-flavor-flavor' ),
        'panel' => 'sjp_panel',
        'priority' => 120,
    ) );

    $legal_settings = array(
        'sjp_company_name'   => array( __( 'Nazwa firmy', 'flavor-flavor-flavor' ), 'text', '' ),
        'sjp_contact_email'  => array( __( 'E-mail kontaktowy', 'flavor-flavor-flavor' ), 'email', '' ),
        'sjp_regulations_url'=> array( __( 'URL regulaminu', 'flavor-flavor-flavor' ), 'url', '' ),
        'sjp_privacy_url'    => array( __( 'URL polityki prywatności', 'flavor-flavor-flavor' ), 'url', '' ),
    );

    foreach ( $legal_settings as $id => $s ) {
        $sanitize = 'sanitize_text_field';
        if ( $s[1] === 'url' )   $sanitize = 'esc_url_raw';
        if ( $s[1] === 'email' ) $sanitize = 'sanitize_email';

        $wp_customize->add_setting( $id, array(
            'default'           => $s[2],
            'sanitize_callback' => $sanitize,
        ) );
        $wp_customize->add_control( $id, array(
            'label'   => $s[0],
            'section' => 'sjp_legal_section',
            'type'    => $s[1],
        ) );
    }
} );

/* ─── Output custom colors as CSS variables ───────────── */

add_action( 'wp_head', function () {
    $primary = get_theme_mod( 'sjp_color_primary', '#2563eb' );
    $accent  = get_theme_mod( 'sjp_color_accent', '#16a34a' );
    $text    = get_theme_mod( 'sjp_color_text', '#111827' );
    $bg      = get_theme_mod( 'sjp_color_bg', '#ffffff' );

    if (
        $primary === '#2563eb' &&
        $accent  === '#16a34a' &&
        $text    === '#111827' &&
        $bg      === '#ffffff'
    ) {
        return;
    }

    $primary_hover = sjp_adjust_brightness( $primary, -15 );
    $accent_hover  = sjp_adjust_brightness( $accent, -15 );

    echo '<style id="sjp-custom-colors">:root{';
    echo '--sjp-primary:' . esc_attr( $primary ) . ';';
    echo '--sjp-primary-hover:' . esc_attr( $primary_hover ) . ';';
    echo '--sjp-accent:' . esc_attr( $accent ) . ';';
    echo '--sjp-accent-hover:' . esc_attr( $accent_hover ) . ';';
    echo '--sjp-text:' . esc_attr( $text ) . ';';
    echo '--sjp-bg:' . esc_attr( $bg ) . ';';
    echo '}</style>' . "\n";
}, 20 );

function sjp_adjust_brightness( $hex, $percent ) {
    $hex = ltrim( $hex, '#' );
    $r = max( 0, min( 255, hexdec( substr( $hex, 0, 2 ) ) + (int) ( 255 * $percent / 100 ) ) );
    $g = max( 0, min( 255, hexdec( substr( $hex, 2, 2 ) ) + (int) ( 255 * $percent / 100 ) ) );
    $b = max( 0, min( 255, hexdec( substr( $hex, 4, 2 ) ) + (int) ( 255 * $percent / 100 ) ) );
    return sprintf( '#%02x%02x%02x', $r, $g, $b );
}
