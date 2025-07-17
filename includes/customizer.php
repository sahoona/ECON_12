<?php
/**
 * Customizer functions
 *
 * @package GP_Child_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Site additional info and SEO
if ( ! function_exists( 'gp_customize_register_additional_meta' ) ) {
    function gp_customize_register_additional_meta( $wp_customize ) {
        $wp_customize->add_section( 'gp_additional_meta_section', array(
            'title'    => __( '사이트 추가 정보 및 SEO', 'gp_child_theme' ),
            'priority' => 160,
        ) );
        $wp_customize->add_setting( 'website_schema_name', array(
            'type'              => 'theme_mod',
            'capability'        => 'edit_theme_options',
            'default'           => get_bloginfo( 'name' ),
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_control( 'website_schema_name_control', array(
            'label'    => __( '웹사이트 스키마 - 이름', 'gp_child_theme' ),
            'section'  => 'gp_additional_meta_section',
            'settings' => 'website_schema_name',
            'type'     => 'text',
        ) );
        $wp_customize->add_setting( 'website_schema_url', array(
            'type'              => 'theme_mod',
            'capability'        => 'edit_theme_options',
            'default'           => home_url( '/' ),
            'sanitize_callback' => 'esc_url_raw',
        ) );
        $wp_customize->add_control( 'website_schema_url_control', array(
            'label'    => __( '웹사이트 스키마 - URL', 'gp_child_theme' ),
            'section'  => 'gp_additional_meta_section',
            'settings' => 'website_schema_url',
            'type'     => 'url',
        ) );
    }
    add_action( 'customize_register', 'gp_customize_register_additional_meta' );
}

// Footer color settings
if ( ! function_exists( 'gp_customize_register_footer_colors' ) ) {
    function gp_customize_register_footer_colors( $wp_customize ) {
        $wp_customize->add_section( 'gp_footer_colors_section', array(
            'title'    => __( '푸터 색상 설정', 'gp_child_theme' ),
            'priority' => 170,
        ) );

        // Footer background color
        $wp_customize->add_setting( 'footer_background_color', array(
            'default'           => '#121212',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'footer_background_color_control', array(
            'label'    => __( '푸터 배경색', 'gp_child_theme' ),
            'section'  => 'gp_footer_colors_section',
            'settings' => 'footer_background_color',
        ) ) );

        // Footer text color
        $wp_customize->add_setting( 'footer_text_color', array(
            'default'           => '#999999',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'footer_text_color_control', array(
            'label'    => __( '푸터 텍스트 색상', 'gp_child_theme' ),
            'section'  => 'gp_footer_colors_section',
            'settings' => 'footer_text_color',
        ) ) );

        // Footer link color
        $wp_customize->add_setting( 'footer_link_color', array(
            'default'           => '#ffffff',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'footer_link_color_control', array(
            'label'    => __( '푸터 링크 색상', 'gp_child_theme' ),
            'section'  => 'gp_footer_colors_section',
            'settings' => 'footer_link_color',
        ) ) );

        // Footer border color
        $wp_customize->add_setting( 'footer_border_color', array(
            'default'           => '#FFC107',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'footer_border_color_control', array(
            'label'    => __( '푸터 상단 테두리 색상', 'gp_child_theme' ),
            'section'  => 'gp_footer_colors_section',
            'settings' => 'footer_border_color',
        ) ) );
    }
    add_action( 'customize_register', 'gp_customize_register_footer_colors' );
}

// Ad settings
function gp_customize_register_ad_settings( $wp_customize ) {
    $wp_customize->add_section( 'gp_ad_settings_section', array(
        'title'    => __( '광고 설정', 'gp_child_theme' ),
        'priority' => 180,
    ) );

    $ad_locations = [
        'ad_after_header' => '헤더 아래 광고',
        'ad_before_title' => '제목 위 광고',
        'ad_in_content' => '본문 중간 광고',
        'ad_before_footer' => '푸터 위 광고',
        'ad_sidebar_left' => '왼쪽 사이드바 광고',
        'ad_sidebar_right' => '오른쪽 사이드바 광고',
        'ad_anchor' => '앵커 광고',
    ];

    foreach ( $ad_locations as $key => $label ) {
        $wp_customize->add_setting( $key . '_enabled', array(
            'default'           => false,
            'sanitize_callback' => 'wp_validate_boolean',
        ) );
        $wp_customize->add_control( $key . '_enabled_control', array(
            'label'    => $label . ' 활성화',
            'section'  => 'gp_ad_settings_section',
            'settings' => $key . '_enabled',
            'type'     => 'checkbox',
        ) );
    }
}
add_action( 'customize_register', 'gp_customize_register_ad_settings' );

if ( ! function_exists( 'gp_output_customizer_header_meta' ) ) {
    function gp_output_customizer_header_meta() {
        $website_schema_name = get_theme_mod( 'website_schema_name', get_bloginfo( 'name' ) );
        $website_schema_url = get_theme_mod( 'website_schema_url', home_url( '/' ) );
        if ( ! empty( $website_schema_name ) && ! empty( $website_schema_url ) ) {
            $website_schema = array(
                '@context' => 'https://schema.org',
                '@type'    => 'WebSite',
                'name'     => esc_html( $website_schema_name ),
                'url'      => esc_url( $website_schema_url ),
            );
            echo '<script type="application/ld+json" class="gp-website-schema-customizer">' . wp_json_encode( $website_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
        }
    }
    add_action( 'wp_head', 'gp_output_customizer_header_meta', 6 );
}

if ( ! function_exists( 'gp_apply_footer_color_css' ) ) {
    function gp_apply_footer_color_css() {
        $footer_background_color = get_theme_mod( 'footer_background_color', '#121212' );
        $footer_text_color = get_theme_mod( 'footer_text_color', '#999999' );
        $footer_link_color = get_theme_mod( 'footer_link_color', '#ffffff' );
        $footer_border_color = get_theme_mod( 'footer_border_color', '#FFC107' );

        $custom_css = "
            :root {
                --footer-background-color: {$footer_background_color};
                --footer-text-color: {$footer_text_color};
                --footer-link-color: {$footer_link_color};
                --footer-border-color: {$footer_border_color};
            }
            .site-footer-container .footer-grid { font-size: 0 !important; }
            .site-footer-container .footer-grid > div { display: inline-block !important; vertical-align: top !important; font-size: 1rem !important; box-sizing: border-box !important; padding: 0 20px !important; }
            .site-footer-container .footer-about { width: 40% !important; }
            .site-footer-container .footer-links { width: 20% !important; }
        ";
        wp_add_inline_style( 'gp-child-style', $custom_css );
    }
    add_action( 'wp_enqueue_scripts', 'gp_apply_footer_color_css' );
}

if ( ! function_exists( 'gp_output_customizer_header_meta' ) ) {
    function gp_output_customizer_header_meta() {
        $website_schema_name = get_theme_mod( 'website_schema_name', get_bloginfo( 'name' ) );
        $website_schema_url = get_theme_mod( 'website_schema_url', home_url( '/' ) );
        if ( ! empty( $website_schema_name ) && ! empty( $website_schema_url ) ) {
            $website_schema = array(
                '@context' => 'https://schema.org',
                '@type'    => 'WebSite',
                'name'     => esc_html( $website_schema_name ),
                'url'      => esc_url( $website_schema_url ),
            );
            echo '<script type="application/ld+json" class="gp-website-schema-customizer">' . wp_json_encode( $website_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
        }
    }
    add_action( 'wp_head', 'gp_output_customizer_header_meta', 6 );
}

function econarc_customize_register( $wp_customize ) {
    // 1. '광고 설정' 섹션 추가
    $wp_customize->add_section( 'econarc_ad_settings', array(
        'title'    => __( '광고 설정', 'econarc' ),
        'priority' => 180,
    ) );

    // 2. 본문 내 광고 활성화 체크박스
    $wp_customize->add_setting( 'econarc_ads_enabled', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ) );
    $wp_customize->add_control( 'econarc_ads_enabled', array(
        'type'    => 'checkbox',
        'section' => 'econarc_ad_settings',
        'label'   => __( '본문 내 수동 광고 활성화', 'econarc' ),
    ) );

    // 3. 애드센스 게시자 ID 입력 필드
    $wp_customize->add_setting( 'econarc_ad_client', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'econarc_ad_client', array(
        'type'        => 'text',
        'section'     => 'econarc_ad_settings',
        'label'       => __( '애드센스 게시자 ID', 'econarc' ),
        'description' => __( '예: ca-pub-1234567890123456', 'econarc' ),
    ) );

    // 4. 광고 단위 ID 입력 필드
    $wp_customize->add_setting( 'econarc_ad_slot', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'econarc_ad_slot', array(
        'type'        => 'text',
        'section'     => 'econarc_ad_settings',
        'label'       => __( '광고 단위 ID', 'econarc' ),
        'description' => __( '예: 1234567890', 'econarc' ),
    ) );

// 5. 카드 사이 광고(인피드) 활성화 체크박스
$wp_customize->add_setting( 'econarc_infeed_ad_enabled', array(
    'default'           => false,
    'sanitize_callback' => 'wp_validate_boolean',
) );
$wp_customize->add_control( 'econarc_infeed_ad_enabled', array(
    'type'    => 'checkbox',
    'section' => 'econarc_ad_settings',
    'label'   => __( '카드 사이 광고 (인피드) 활성화', 'econarc' ),
) );

// 6. 인피드 광고 단위 ID 입력 필드
$wp_customize->add_setting( 'econarc_infeed_ad_slot', array(
    'default'           => '',
    'sanitize_callback' => 'sanitize_text_field',
) );
$wp_customize->add_control( 'econarc_infeed_ad_slot', array(
    'type'        => 'text',
    'section'     => 'econarc_ad_settings',
    'label'       => __( '카드 사이 광고 단위 ID', 'econarc' ),
) );

// 7. 목록 상단 광고 활성화 체크박스
$wp_customize->add_setting( 'econarc_top_ad_enabled', array(
    'default'           => false,
    'sanitize_callback' => 'wp_validate_boolean',
) );
$wp_customize->add_control( 'econarc_top_ad_enabled', array(
    'type'    => 'checkbox',
    'section' => 'econarc_ad_settings',
    'label'   => __( '목록 상단 광고 활성화', 'econarc' ),
) );

// 8. 목록 상단 광고 단위 ID 입력 필드
$wp_customize->add_setting( 'econarc_top_ad_slot', array(
    'default'           => '',
    'sanitize_callback' => 'sanitize_text_field',
) );
$wp_customize->add_control( 'econarc_top_ad_slot', array(
    'type'        => 'text',
    'section'     => 'econarc_ad_settings',
    'label'       => __( '목록 상단 광고 단위 ID', 'econarc' ),
) );
}
add_action( 'customize_register', 'econarc_customize_register' );
