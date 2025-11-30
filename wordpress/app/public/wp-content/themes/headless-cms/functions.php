<?php

// Initialize Theme
function headless_cms_setup() {
    add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'headless_cms_setup');

// Remove unnecessary head output
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

// Register Custom Post Types & Taxonomies
require_once get_template_directory() . '/inc/post-types.php';

// Register ACF Fields
require_once get_template_directory() . '/inc/acf-fields.php';

// CORS Support (Basic)
add_action('init', function() {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
});

// Hide content editor for Site Settings template
function hide_content_editor_for_site_settings() {
    $post_id = isset($_GET['post']) ? intval($_GET['post']) : (isset($_POST['post_ID']) ? intval($_POST['post_ID']) : 0);
    if ($post_id) {
        $template = get_page_template_slug($post_id);
        if ($template === 'template-site-settings.php') {
            remove_post_type_support('page', 'editor');
        }
    }
}
add_action('admin_init', 'hide_content_editor_for_site_settings');

