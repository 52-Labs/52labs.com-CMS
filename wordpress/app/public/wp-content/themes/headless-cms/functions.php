<?php

// Initialize Theme
function headless_cms_setup() {
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
 */
function headless_cms_filter_products() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'] ?? '', 'library_filter_nonce')) {
        wp_send_json_error(['message' => 'Invalid nonce']);
        return;
    }
    
    // Get filter parameters
    $categories = isset($_POST['categories']) ? array_map('sanitize_text_field', $_POST['categories']) : [];
    $tags = isset($_POST['tags']) ? array_map('sanitize_text_field', $_POST['tags']) : [];
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    // Build query
    $args = [
        'post_type' => 'product',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
        'post_status' => 'publish',
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
    
    $products = new WP_Query($args);
    
    // Build HTML output
    ob_start();
    
    if ($products->have_posts()) {
        while ($products->have_posts()) {
            $products->the_post();
            $product_id = get_the_ID();
            
            // Get categories
            $cats = get_the_terms($product_id, 'product_cat');
            $category_name = (!empty($cats) && !is_wp_error($cats)) ? $cats[0]->name : '';
            $category_slug = (!empty($cats) && !is_wp_error($cats)) ? $cats[0]->slug : '';
            
            // Get tags
            $product_tags = get_the_terms($product_id, 'product_tag');
            $tag_slugs = (!empty($product_tags) && !is_wp_error($product_tags)) ? array_map(function($tag) { return $tag->slug; }, $product_tags) : [];
            
            // Get ACF fields (top-level fields on product)
            $logo = get_field('logo', $product_id);
            $description = get_field('description', $product_id) ?: get_the_excerpt();
            $background_color = get_field('backgroundColor', $product_id) ?: '#3B82F6';
            ?>
            <article class="product-card" data-category="<?php echo esc_attr($category_slug); ?>" data-tags="<?php echo esc_attr(implode(' ', $tag_slugs)); ?>">
                <div class="product-card-inner">
                    <div class="product-icon" style="background-color: <?php echo esc_attr($background_color); ?>">
                        <?php if ($logo) : ?>
                            <img src="<?php echo esc_url($logo['url']); ?>" alt="<?php echo esc_attr(get_the_title()); ?> icon">
                        <?php else : ?>
                            <span class="icon-placeholder"><?php echo esc_html(substr(get_the_title(), 0, 1)); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3 class="product-name"><?php the_title(); ?></h3>
                        <span class="product-category"><?php echo esc_html($category_name); ?></span>
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
    
    wp_send_json_success(['html' => $html]);
}
add_action('wp_ajax_filter_products', 'headless_cms_filter_products');
add_action('wp_ajax_nopriv_filter_products', 'headless_cms_filter_products');

/**
 * Hide content editor for Library template
 */
function hide_content_editor_for_library() {
    $post_id = isset($_GET['post']) ? intval($_GET['post']) : (isset($_POST['post_ID']) ? intval($_POST['post_ID']) : 0);
    if ($post_id) {
        $template = get_page_template_slug($post_id);
        if ($template === 'template-library.php') {
            remove_post_type_support('page', 'editor');
        }
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
 */
function headless_cms_rewrite_rules() {
    add_rewrite_rule(
        'app-library/([^/]+)/?$',
        'index.php?product=$matches[1]',
        'top'
    );
}
add_action('init', 'headless_cms_rewrite_rules');

