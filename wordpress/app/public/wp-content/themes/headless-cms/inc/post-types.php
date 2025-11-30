<?php

function headless_cms_register_post_types() {

    // Product Category Taxonomy
    register_taxonomy('product_cat', ['product'], [
        'labels' => [
            'name' => 'Product Categories',
            'singular_name' => 'Product Category',
            'menu_name' => 'Product Categories',
        ],
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => ['slug' => 'product-category'],
        'show_in_rest' => true, // Required for Gutenberg editor sidebar
        'show_in_graphql' => true,
        'graphql_single_name' => 'ProductCategory',
        'graphql_plural_name' => 'ProductCategories',
    ]);

    // Product Post Type
    register_post_type('product', [
        'labels' => [
            'name' => 'Products',
            'singular_name' => 'Product',
            'menu_name' => 'Products',
        ],
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_rest' => true, // Required for Gutenberg editor
        'supports' => ['title', 'editor', 'thumbnail', 'custom-fields'], // 'editor' maps to description if simple, but we have ACF description
        'menu_icon' => 'dashicons-cart',
        'has_archive' => true,
        'rewrite' => ['slug' => 'products'],
        'show_in_graphql' => true,
        'graphql_single_name' => 'Product',
        'graphql_plural_name' => 'Products',
    ]);

}
add_action('init', 'headless_cms_register_post_types');

