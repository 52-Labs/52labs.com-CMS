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

    // Product Tags Taxonomy
    register_taxonomy('product_tag', ['product'], [
        'labels' => [
            'name' => 'Product Tags',
            'singular_name' => 'Product Tag',
            'menu_name' => 'Product Tags',
            'search_items' => 'Search Tags',
            'popular_items' => 'Popular Tags',
            'all_items' => 'All Tags',
            'edit_item' => 'Edit Tag',
            'update_item' => 'Update Tag',
            'add_new_item' => 'Add New Tag',
            'new_item_name' => 'New Tag Name',
            'separate_items_with_commas' => 'Separate tags with commas',
            'add_or_remove_items' => 'Add or remove tags',
            'choose_from_most_used' => 'Choose from the most used tags',
        ],
        'hierarchical' => false,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => ['slug' => 'product-tag'],
        'show_in_rest' => true, // Required for Gutenberg editor sidebar
        'show_in_graphql' => true,
        'graphql_single_name' => 'ProductTag',
        'graphql_plural_name' => 'ProductTags',
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
        'supports' => ['title', 'thumbnail', 'custom-fields'], // Editor removed - using ACF description instead
        'menu_icon' => 'dashicons-cart',
        'has_archive' => true,
        'rewrite' => ['slug' => 'products'],
        'show_in_graphql' => true,
        'graphql_single_name' => 'Product',
        'graphql_plural_name' => 'Products',
    ]);

}
add_action('init', 'headless_cms_register_post_types');

