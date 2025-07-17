<?php
/**
 * Layout hooks and filters
 *
 * @package GP_Child_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Layout setup
function gp_layout_setup() {
    // General removals and filters
    remove_action( 'generate_after_entry_title', 'generate_post_meta' );
    remove_action( 'generate_after_entry_header', 'generate_post_meta' );
    remove_action( 'generate_after_entry_header', 'generate_post_image' );
    add_filter( 'generate_show_post_navigation', '__return_false' );

    // Hooks for singular posts
    if ( is_singular('post') ) {
        add_action( 'generate_before_entry_title', 'gp_breadcrumb_output', 5 );
        add_action( 'generate_after_entry_title', 'gp_meta_after_title', 10 );
        add_action( 'generate_after_entry_header', 'gp_featured_image_output', 15 );
        add_action( 'generate_after_entry_header', 'gp_insert_toc', 20 );
        add_action( 'generate_after_entry_content', 'gp_child_display_after_content_widget_area', 8 );
        add_action( 'generate_after_entry_content', 'gppress_tags_before_related', 9);
        add_action( 'generate_after_entry_content', 'gp_render_post_footer_sections', 11 );
        add_action( 'generate_after_entry_content', 'gp_series_posts_output', 15 );
        add_action( 'generate_after_entry_content', 'gp_custom_post_navigation_output', 20 );
    }

    // Hooks for archive/home pages
    if ( is_home() || is_archive() || is_search() ) {
        // We will render the summary card via a template part to ensure consistency
        remove_action( 'generate_before_entry_title', 'gp_breadcrumb_output', 5 );
        remove_action( 'generate_after_entry_header', 'gp_featured_image_output', 15 );
        remove_action( 'generate_before_entry_content', 'gp_featured_image_output', 5 );

        add_action( 'generate_before_entry_content', 'gp_render_list_post_summary', 10 );
        add_action( 'generate_after_entry_content', 'gp_render_list_post_footer_meta', 10 );
    }

    // Global hooks
    add_action( 'wp_footer', 'gp_add_footer_elements_and_scripts' );
}
add_action( 'wp', 'gp_layout_setup' );

// Function to render the post summary card for lists
function gp_render_list_post_summary() {
    ?>
    <header class="entry-header">
        <?php
        if (function_exists('gp_home_breadcrumb_output')) {
            gp_home_breadcrumb_output();
        }
        the_title(sprintf('<h2 class="entry-title" itemprop="headline"><a href="%s" rel="bookmark">', esc_url(get_permalink())), '</a></h2>');
        ?>
    </header>

    <?php
    if (function_exists('gp_featured_image_output')) {
        gp_featured_image_output();
    }
    ?>
    <?php
}

function gp_render_list_post_footer_meta() {
    ?>
    <footer class="entry-meta">
        <?php
        if (function_exists('gp_read_more_btn_output')) {
            gp_read_more_btn_output();
        }
        if (function_exists('gp_add_tags_to_list')) {
            gp_add_tags_to_list();
        }
        if (function_exists('gp_add_star_rating_to_list')) {
            gp_add_star_rating_to_list();
        }
        ?>
    </footer>
    <?php
}


// add_filter( 'generate_copyright', '__return_empty_string' );
add_filter( 'generate_show_categories', '__return_false' );
add_filter( 'generate_footer_entry_meta_items', function($items) { return array_diff($items, ['categories', 'tags', 'comments']); } );
add_filter( 'excerpt_length', function($length) { return 55; }, 999 );
add_filter( 'generate_excerpt_more_output', function() { return ' …'; } );

