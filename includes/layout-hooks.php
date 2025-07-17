<?php
/**
 * Layout hooks and filters
 *
 * @package GP_Child_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Remove default actions
add_action('after_setup_theme', function() {
    remove_action( 'generate_after_entry_title', 'generate_post_meta' );
    remove_action( 'generate_after_entry_header', 'generate_post_meta' );
    remove_action( 'generate_after_entry_header', 'generate_post_image' );
});

// Add custom actions
function gp_child_theme_layout_hooks() {
    add_filter( 'generate_show_post_navigation', '__return_false' );

    add_action( 'generate_before_entry_title', 'gp_breadcrumb_output', 5 );
    add_action( 'generate_after_entry_title', 'gp_meta_after_title', 10 );
    add_action( 'generate_after_entry_header', 'gp_insert_toc', 20 );

    add_action( 'generate_after_entry_content', 'gppress_tags_before_related', 9);
    add_action( 'generate_after_entry_content', 'gp_render_post_footer_sections', 11 );
    add_action( 'generate_after_entry_content', 'gp_series_posts_output', 15 );
    add_action( 'generate_after_entry_content', 'gp_custom_post_navigation_output', 20 );

    if ( !is_singular() ) {
        add_action( 'generate_after_entry_header', 'gp_featured_image_output', 15 );
    } else {
        add_action( 'generate_before_entry_content', 'gp_featured_image_output', 5 );
    }
    add_action( 'generate_after_entry_content', 'gp_read_more_btn_output', 1 );
    add_action( 'generate_after_entry_content', 'gp_add_tags_to_list', 2 );
    add_action( 'generate_after_entry_content', 'gp_add_star_rating_to_list', 3 );

    add_action( 'wp_footer', 'gp_add_footer_elements_and_scripts' );
}
add_action( 'template_redirect', 'gp_child_theme_layout_hooks' );


// add_filter( 'generate_copyright', '__return_empty_string' );
add_filter( 'generate_show_categories', '__return_false' );
add_filter( 'generate_footer_entry_meta_items', function($items) { return array_diff($items, ['categories', 'tags', 'comments']); } );
add_filter( 'excerpt_length', function($length) { return 55; }, 999 );
add_filter( 'generate_excerpt_more_output', function() { return ' …'; } );
