<?php
/**
 * GP Child Theme Functions
 *
 * @package    GP_Child_Theme
 * @version    22.7.16
 * @author     sh k & GP AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Load core theme functionality
require_once get_stylesheet_directory() . '/includes/core-setup.php';
require_once get_stylesheet_directory() . '/includes/assets.php';
require_once get_stylesheet_directory() . '/includes/optimization.php';
require_once get_stylesheet_directory() . '/includes/seo.php';
require_once get_stylesheet_directory() . '/includes/layout-hooks.php';
require_once get_stylesheet_directory() . '/includes/template-tags.php';
require_once get_stylesheet_directory() . '/includes/toc.php';
require_once get_stylesheet_directory() . '/includes/post-features.php';
require_once get_stylesheet_directory() . '/includes/related-posts.php';
require_once get_stylesheet_directory() . '/includes/ajax-handlers.php';
require_once get_stylesheet_directory() . '/includes/helpers.php';
require_once get_stylesheet_directory() . '/includes/admin.php';
require_once get_stylesheet_directory() . '/includes/customizer.php';

// Load components
require_once get_stylesheet_directory() . '/components/ads/ads.php';

function custom_excerpt_length( $length ) {
    return 60;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

// Register "After Entry Content" widget area
function gp_child_register_widget_areas() {
    register_sidebar( array(
        'name'          => '본문 끝 위젯 영역',
        'id'            => 'after_entry_content_widget_area',
        'description'   => '글 본문 내용이 끝나는 지점, 태그 박스 위에 표시됩니다.',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ) );
}
add_action( 'widgets_init', 'gp_child_register_widget_areas' );

// Display the widget area after the entry content
function gp_child_display_after_content_widget_area() {
    if ( is_singular( 'post' ) && is_active_sidebar( 'after_entry_content_widget_area' ) ) {
        echo '<div class="after-entry-content-widget-area">';
        dynamic_sidebar( 'after_entry_content_widget_area' );
        echo '</div>';
    }
}
add_action( 'generate_after_entry_content', 'gp_child_display_after_content_widget_area' );
