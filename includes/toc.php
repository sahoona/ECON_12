<?php
/**
 * Table of Contents functions
 *
 * @package GP_Child_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Sanitize Korean for ID
function gp_sanitize_korean_for_id( $title ) {
    $title = mb_strtolower( $title, 'UTF-8' );
    $title = preg_replace( '/\s+/', '-', $title );
    $title = preg_replace( '/-+/', '-', $title );
    $title = preg_replace( '/[^\p{L}\p{N}-]/u', '', $title );
    $title = trim( $title, '-' );
    return $title;
}

// Collect headings for TOC
function gp_toc_collect_headings($content) {
    if (!is_single() || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    global $gp_toc_data;
    $gp_toc_data = [
        'headings' => [],
        'toc_html' => '',
        'id_counts' => [],
        'content_has_headings' => false,
    ];

    preg_match_all('/<h([2-6]).*?>(.*?)<\/h\1>/i', $content, $matches, PREG_SET_ORDER);

    if (empty($matches)) {
        return $content;
    }

    $gp_toc_data['content_has_headings'] = true;

    foreach ($matches as $match) {
        $level = intval($match[1]);
        $title = strip_tags($match[2]);
        $decoded_title = html_entity_decode($title, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $id_base = gp_sanitize_korean_for_id($decoded_title);

        if (isset($gp_toc_data['id_counts'][$id_base])) {
            $gp_toc_data['id_counts'][$id_base]++;
            $id = $id_base . '-' . $gp_toc_data['id_counts'][$id_base];
        } else {
            $gp_toc_data['id_counts'][$id_base] = 1;
            $id = $id_base;
        }
        $gp_toc_data['headings'][] = ['level' => $level, 'title' => $decoded_title, 'id' => $id];
    }

    if (!empty($gp_toc_data['headings'])) {
        $toc_list_items = '';
        $open_h2_list = false;
        $last_level = 0;
        $toc_list_items = '';
        foreach ($gp_toc_data['headings'] as $heading) {
            $level = $heading['level'];
            $title = $heading['title'];
            $id = $heading['id'];

            if ($level > $last_level) {
                $toc_list_items .= '<ol>';
            } elseif ($level < $last_level) {
                $toc_list_items .= str_repeat('</li></ol>', $last_level - $level);
            }

            if ($level <= $last_level) {
                $toc_list_items .= '</li>';
            }

            $class = 'toc-heading-level-' . $level;
            if ($level == 2) {
                $class .= ' toc-h2-item';
            }

            $toc_list_items .= '<li class="' . $class . '">';
            $toc_list_items .= '<a href="#' . esc_attr($id) . '">' . esc_html($title) . '</a>';

            $last_level = $level;
        }
        $toc_list_items .= str_repeat('</li></ol>', $last_level);

        if (!empty($toc_list_items)) {
            $gp_toc_data['toc_html'] = '<nav id="gp-toc-container" aria-label="Table of Contents" role="navigation">' .
                        '<h2 class="gp-toc-title">Table of Contents <span class="gp-toc-toggle" aria-label="Toggle table of contents">[Hide]</span></h2>' .
                        '<ol class="gp-toc-list" role="list">' . $toc_list_items . '</ol>' .
                        '</nav>';
        }
    }
    return $content;
}
add_filter('the_content', 'gp_toc_collect_headings', 5);

// Add IDs to headings
function gp_toc_add_ids_to_headings($content) {
    if (!is_single() || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    global $gp_toc_data;
    if (empty($gp_toc_data) || empty($gp_toc_data['headings'])) {
        return $content;
    }

    $heading_index = 0;
    return preg_replace_callback('/<h([2-6])([^>]*)>/i', function($matches) use (&$gp_toc_data, &$heading_index) {
        $attributes = $matches[2];
        if (strpos($attributes, 'id=') !== false) {
            return $matches[0];
        }
        if (isset($gp_toc_data['headings'][$heading_index])) {
            $id = $gp_toc_data['headings'][$heading_index]['id'];
            $heading_index++;
            return '<h' . $matches[1] . ' id="' . esc_attr($id) . '"' . $attributes . '>';
        }
        return $matches[0];
    }, $content);
}
add_filter('the_content', 'gp_toc_add_ids_to_headings', 15);

// Insert TOC into post
function gp_insert_toc() {
    if (!is_single()) return;
    echo '<nav id="gp-toc-container" aria-label="Table of Contents" role="navigation">' .
            '<h2 class="gp-toc-title">Table of Contents <span class="gp-toc-toggle" aria-label="Toggle table of contents">[Hide]</span></h2>' .
            '<ol class="gp-toc-list" role="list"></ol>' .
         '</nav>';
}
