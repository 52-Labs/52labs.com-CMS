<?php

// Initialize Theme
function headless_cms_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    
    // Gutenberg support
    add_theme_support('align-wide');
    add_theme_support('responsive-embeds');
    add_theme_support('editor-styles');
    add_theme_support('wp-block-styles');
    
    // Custom color palette for Gutenberg
    add_theme_support('editor-color-palette', [
        [
            'name'  => 'Primary Blue',
            'slug'  => 'primary',
            'color' => '#3B82F6',
        ],
        [
            'name'  => 'Primary Hover',
            'slug'  => 'primary-hover',
            'color' => '#2563EB',
        ],
        [
            'name'  => 'Text Dark',
            'slug'  => 'text-dark',
            'color' => '#1F2937',
        ],
        [
            'name'  => 'Text Secondary',
            'slug'  => 'text-secondary',
            'color' => '#6B7280',
        ],
        [
            'name'  => 'Text Muted',
            'slug'  => 'text-muted',
            'color' => '#9CA3AF',
        ],
        [
            'name'  => 'Background',
            'slug'  => 'background',
            'color' => '#FFFFFF',
        ],
        [
            'name'  => 'Background Subtle',
            'slug'  => 'background-subtle',
            'color' => '#F9FAFB',
        ],
        [
            'name'  => 'Border',
            'slug'  => 'border',
            'color' => '#E5E7EB',
        ],
    ]);
    
    // Custom font sizes for Gutenberg
    add_theme_support('editor-font-sizes', [
        [
            'name' => 'Small',
            'slug' => 'small',
            'size' => 13,
        ],
        [
            'name' => 'Normal',
            'slug' => 'normal',
            'size' => 16,
        ],
        [
            'name' => 'Medium',
            'slug' => 'medium',
            'size' => 18,
        ],
        [
            'name' => 'Large',
            'slug' => 'large',
            'size' => 24,
        ],
        [
            'name' => 'Extra Large',
            'slug' => 'extra-large',
            'size' => 32,
        ],
    ]);
    
    // Add editor stylesheet
    add_editor_style('assets/css/editor-style.css');
}
add_action('after_setup_theme', 'headless_cms_setup');

// Remove unnecessary head output
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

// Register Custom Post Types & Taxonomies
require_once get_template_directory() . '/inc/post-types.php';

// Register ACF Fields
require_once get_template_directory() . '/inc/acf-fields.php';

// ========================================
// Helper Functions
// ========================================

/**
 * Helper function to safely get post ID from various sources
 * Used in admin contexts where global $post may not be set
 * 
 * @return int Post ID or 0 if not found
 */
function headless_cms_get_post_id() {
    global $post;
    
    $post_id = 0;
    if (isset($_GET['post'])) {
        $post_id = absint($_GET['post']);
    } elseif (isset($_POST['post_ID'])) {
        $post_id = absint($_POST['post_ID']);
    } elseif (isset($post->ID)) {
        $post_id = $post->ID;
    }
    
    return $post_id;
}

/**
 * Add error logging helper (for development)
 * 
 * @param string $message Error message
 * @param mixed  $data    Additional data to log
 * @return void
 */
function headless_cms_log_error($message, $data = null) {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('Headless CMS: ' . $message);
        if ($data !== null) {
            error_log('Headless CMS Data: ' . print_r($data, true));
        }
    }
}

// ========================================
// CORS & API Support
// ========================================

/**
 * CORS Support for REST API and GraphQL
 * Only applies to API requests, not regular page loads
 * 
 * @param string $origin Allowed origin (can be configured via filter)
 */
function headless_cms_handle_cors() {
    // Only apply to REST API and GraphQL endpoints
    $request_uri = isset($_SERVER['REQUEST_URI']) ? esc_url_raw(wp_unslash($_SERVER['REQUEST_URI'])) : '';
    
    // Check if this is an API request
    $is_rest_api = (false !== strpos($request_uri, '/wp-json/'));
    $is_graphql = (false !== strpos($request_uri, '/graphql'));
    
    if (!$is_rest_api && !$is_graphql) {
        return;
    }
    
    // Allow configuration via filter (default: current origin or wildcard for development)
    $allowed_origin = apply_filters('headless_cms_cors_origin', '*');
    
    // Handle preflight OPTIONS request
    if ('OPTIONS' === $_SERVER['REQUEST_METHOD']) {
        header("Access-Control-Allow-Origin: {$allowed_origin}");
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        header("Access-Control-Max-Age: 86400");
        exit;
    }
    
    // Set CORS headers for actual requests
    header("Access-Control-Allow-Origin: {$allowed_origin}");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
}
add_action('rest_api_init', 'headless_cms_handle_cors', 1);
// Hook for WPGraphQL - runs before GraphQL processes the request
add_action('graphql_init', 'headless_cms_handle_cors', 1);

/**
 * Hide content editor for Site Settings template
 * 
 * @return void
 */
function hide_content_editor_for_site_settings() {
    $post_id = headless_cms_get_post_id();
    
    if (!$post_id) {
        return;
    }
    
    $template = get_page_template_slug($post_id);
    if ($template === 'template-site-settings.php') {
        remove_post_type_support('page', 'editor');
    }
}
add_action('admin_init', 'hide_content_editor_for_site_settings');

// ========================================
// Library Assets & Functionality
// ========================================

/**
 * Enqueue Library and Blog styles and scripts
 */
function headless_cms_library_assets() {
    // Check if we're on a library page, single product, blog, archive, or regular page
    $is_library_page = is_page_template('template-library.php');
    $is_single_product = is_singular('product');
    $is_single_post = is_singular('post');
    $is_archive = is_archive() || is_home() || is_search();
    $is_page = is_page();
    
    if ($is_library_page || $is_single_product || $is_single_post || $is_archive || $is_page) {
        // Enqueue library CSS (contains both library and blog styles)
        wp_enqueue_style(
            'library-styles',
            get_template_directory_uri() . '/assets/css/library.css',
            [],
            filemtime(get_template_directory() . '/assets/css/library.css')
        );
        
        // Enqueue library JavaScript
        wp_enqueue_script(
            'library-scripts',
            get_template_directory_uri() . '/assets/js/library.js',
            [],
            filemtime(get_template_directory() . '/assets/js/library.js'),
            true
        );
        
        // Localize script with AJAX URL and nonce
        wp_localize_script('library-scripts', 'libraryData', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('library_filter_nonce'),
            'templateUrl' => get_template_directory_uri(),
        ]);
    }
}
add_action('wp_enqueue_scripts', 'headless_cms_library_assets');

/**
 * AJAX handler for product filtering
 * 
 * Note: No capability check since this displays public product data.
 * Nonce verification ensures the request came from our site.
 */
function headless_cms_filter_products() {
    // Verify nonce - ensures request originated from our site
    $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
    if (!wp_verify_nonce($nonce, 'library_filter_nonce')) {
        wp_send_json_error(['message' => 'Invalid security token'], 403);
        return;
    }
    
    // Get filter parameters with proper sanitization
    $categories = isset($_POST['categories']) && is_array($_POST['categories']) 
        ? array_map('sanitize_text_field', wp_unslash($_POST['categories'])) 
        : [];
    $tags = isset($_POST['tags']) && is_array($_POST['tags']) 
        ? array_map('sanitize_text_field', wp_unslash($_POST['tags'])) 
        : [];
    $search = isset($_POST['search']) ? sanitize_text_field(wp_unslash($_POST['search'])) : '';
    
    // Build query with pagination support
    // Use a reasonable limit to prevent performance issues
    $posts_per_page = apply_filters('headless_cms_filter_products_per_page', 100);
    
    $args = [
        'post_type' => 'product',
        'posts_per_page' => $posts_per_page,
        'orderby' => 'title',
        'order' => 'ASC',
        'post_status' => 'publish',
        'no_found_rows' => false, // Enable pagination info
    ];
    
    // Add category and tag filters
    $tax_queries = [];
    if (!empty($categories)) {
        $tax_queries[] = [
            'taxonomy' => 'product_cat',
            'field' => 'slug',
            'terms' => $categories,
        ];
    }
    if (!empty($tags)) {
        $tax_queries[] = [
            'taxonomy' => 'product_tag',
            'field' => 'slug',
            'terms' => $tags,
        ];
    }
    if (!empty($tax_queries)) {
        $args['tax_query'] = $tax_queries;
        // If both filters are used, use AND relation (product must match both)
        if (count($tax_queries) > 1) {
            $args['tax_query']['relation'] = 'AND';
        }
    }
    
    if (!empty($search)) {
        $args['s'] = $search;
    }
    
    // Execute query
    $products = new WP_Query($args);
    
    // Build HTML output
    ob_start();
    
    if ($products->have_posts()) {
        while ($products->have_posts()) {
            $products->the_post();
            $product_id = get_the_ID();
            
            // Get categories with error handling
            $cats = get_the_terms($product_id, 'product_cat');
            $category_name = '';
            $category_slug = '';
            if (!empty($cats) && !is_wp_error($cats)) {
                $category_name = esc_html($cats[0]->name);
                $category_slug = esc_attr($cats[0]->slug);
            }
            
            // Get tags with error handling
            $product_tags = get_the_terms($product_id, 'product_tag');
            $tag_slugs = [];
            if (!empty($product_tags) && !is_wp_error($product_tags)) {
                $tag_slugs = array_map(function($tag) { 
                    return esc_attr($tag->slug); 
                }, $product_tags);
            }
            
            // Get ACF fields with fallbacks
            $logo = get_field('logo', $product_id);
            $description = get_field('description', $product_id);
            if (empty($description)) {
                $description = get_the_excerpt();
            }
            $background_color = get_field('backgroundColor', $product_id);
            if (empty($background_color)) {
                $background_color = '#3B82F6';
            }
            
            // Output product card
            ?>
            <article class="product-card" data-category="<?php echo esc_attr($category_slug); ?>" data-tags="<?php echo esc_attr(implode(' ', $tag_slugs)); ?>">
                <div class="product-card-inner">
                    <div class="product-icon" style="background-color: <?php echo esc_attr($background_color); ?>">
                        <?php if (!empty($logo) && is_array($logo) && isset($logo['url'])) : ?>
                            <img src="<?php echo esc_url($logo['url']); ?>" alt="<?php echo esc_attr(get_the_title()); ?> icon">
                        <?php else : ?>
                            <span class="icon-placeholder"><?php echo esc_html(mb_substr(get_the_title(), 0, 1)); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3 class="product-name"><?php the_title(); ?></h3>
                        <?php if ($category_name) : ?>
                            <span class="product-category"><?php echo esc_html($category_name); ?></span>
                        <?php endif; ?>
                        <p class="product-description"><?php echo esc_html(wp_trim_words($description, 15, '...')); ?></p>
                    </div>
                    <a href="<?php the_permalink(); ?>" class="product-link">Learn More</a>
                </div>
            </article>
            <?php
        }
    } else {
        ?>
        <div class="no-products">
            <p>No products found matching your criteria.</p>
        </div>
        <?php
    }
    
    wp_reset_postdata();
    
    $html = ob_get_clean();
    
    // Include pagination info if needed
    $response = [
        'html' => $html,
        'found_posts' => $products->found_posts,
        'max_pages' => $products->max_num_pages,
    ];
    
    wp_send_json_success($response);
}
add_action('wp_ajax_filter_products', 'headless_cms_filter_products');
add_action('wp_ajax_nopriv_filter_products', 'headless_cms_filter_products');

/**
 * Hide content editor for Library template
 * 
 * @return void
 */
function hide_content_editor_for_library() {
    $post_id = headless_cms_get_post_id();
    
    if (!$post_id) {
        return;
    }
    
    $template = get_page_template_slug($post_id);
    if ($template === 'template-library.php') {
        remove_post_type_support('page', 'editor');
    }
}
add_action('admin_init', 'hide_content_editor_for_library');

/**
 * Add custom body classes for library, blog, and page templates
 */
function headless_cms_body_classes($classes) {
    if (is_page_template('template-library.php')) {
        $classes[] = 'library-page';
    }
    if (is_singular('product')) {
        $classes[] = 'single-product-page';
    }
    if (is_singular('post')) {
        $classes[] = 'single-post-page';
    }
    if (is_archive() || is_home() || is_search()) {
        $classes[] = 'blog-archive-page';
    }
    if (is_page() && !is_page_template()) {
        $classes[] = 'default-page';
    }
    return $classes;
}
add_filter('body_class', 'headless_cms_body_classes');

/**
 * Custom rewrite rules for products
 * 
 * @return void
 */
function headless_cms_rewrite_rules() {
    add_rewrite_rule(
        'app-library/([^/]+)/?$',
        'index.php?product=$matches[1]',
        'top'
    );
}
add_action('init', 'headless_cms_rewrite_rules');

/**
 * Flush rewrite rules on theme activation
 * 
 * @return void
 */
function headless_cms_flush_rewrite_rules() {
    headless_cms_rewrite_rules();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'headless_cms_flush_rewrite_rules');
