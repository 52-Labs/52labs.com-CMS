<?php
/**
 * Template Name: Library
 * 
 * App Store-style library page displaying all products with filtering and search
 */

get_header();

// Get all product categories
$categories = get_terms([
    'taxonomy' => 'product_cat',
    'hide_empty' => true,
    'orderby' => 'name',
    'order' => 'ASC',
]);

// Get all product tags
$tags = get_terms([
    'taxonomy' => 'product_tag',
    'hide_empty' => true,
    'orderby' => 'name',
    'order' => 'ASC',
]);

// Get filter parameters
$selected_categories = isset($_GET['categories']) ? array_map('sanitize_text_field', (array)$_GET['categories']) : [];
$selected_tags = isset($_GET['tags']) ? array_map('sanitize_text_field', (array)$_GET['tags']) : [];
$search_query = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';

// Build product query with pagination
$product_args = [
    'post_type' => 'product',
    'orderby' => 'title',
    'order' => 'ASC',
    'post_status' => 'publish',
    'no_found_rows' => false, // Enable pagination info
];

// Add category and tag filters
$tax_queries = [];
if (!empty($selected_categories)) {
    $tax_queries[] = [
        'taxonomy' => 'product_cat',
        'field' => 'slug',
        'terms' => $selected_categories,
    ];
}
if (!empty($selected_tags)) {
    $tax_queries[] = [
        'taxonomy' => 'product_tag',
        'field' => 'slug',
        'terms' => $selected_tags,
    ];
}
if (!empty($tax_queries)) {
    $product_args['tax_query'] = $tax_queries;
    // If both filters are used, use AND relation (product must match both)
    if (count($tax_queries) > 1) {
        $product_args['tax_query']['relation'] = 'AND';
    }
}

// Add search filter
if (!empty($search_query)) {
    $product_args['s'] = $search_query;
}

// Use pagination for better performance
$paged = (get_query_var('paged')) ? absint(get_query_var('paged')) : 1;
$product_args['paged'] = $paged;
$product_args['posts_per_page'] = apply_filters('headless_cms_library_products_per_page', 24);

$products = new WP_Query($product_args);
?>

<div class="library-wrapper">
    <!-- Header -->
    <?php include(get_template_directory() . '/inc/header-nav.php'); ?>

    <!-- Main Content -->
    <main class="library-main">
        <div class="library-container">
            <!-- Page Title -->
            <h1 class="library-title">Explore Our Solutions</h1>

            <div class="library-content">
                <!-- Sidebar Filters -->
                <aside class="library-sidebar">
                    <!-- Category Filter -->
                    <div class="filter-section">
                        <button class="filter-header" aria-expanded="true">
                            <span>Category</span>
                            <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="18 15 12 9 6 15"></polyline>
                            </svg>
                        </button>
                        <div class="filter-options">
                            <?php if (!empty($categories) && !is_wp_error($categories)) : ?>
                                <?php foreach ($categories as $category) : ?>
                                    <label class="filter-checkbox">
                                        <input type="checkbox" 
                                               name="category" 
                                               value="<?php echo esc_attr($category->slug); ?>"
                                               <?php checked(in_array($category->slug, $selected_categories)); ?>>
                                        <span class="checkbox-custom"></span>
                                        <span class="checkbox-label"><?php echo esc_html($category->name); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Tag Filter -->
                    <div class="filter-section">
                        <button class="filter-header" aria-expanded="true">
                            <span>Tags</span>
                            <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="18 15 12 9 6 15"></polyline>
                            </svg>
                        </button>
                        <div class="filter-options">
                            <?php if (!empty($tags) && !is_wp_error($tags)) : ?>
                                <?php foreach ($tags as $tag) : ?>
                                    <label class="filter-checkbox">
                                        <input type="checkbox" 
                                               name="tag" 
                                               value="<?php echo esc_attr($tag->slug); ?>"
                                               <?php checked(in_array($tag->slug, $selected_tags)); ?>>
                                        <span class="checkbox-custom"></span>
                                        <span class="checkbox-label"><?php echo esc_html($tag->name); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <p class="no-tags">No tags available.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                </aside>

                <!-- Products Grid -->
                <div class="products-section">
                    <div class="products-grid" id="products-grid">
                        <?php if ($products->have_posts()) : ?>
                            <?php while ($products->have_posts()) : $products->the_post(); 
                                $product_id = get_the_ID();
                                
                                // Get categories
                                $product_cats = get_the_terms($product_id, 'product_cat');
                                $category_name = (!empty($product_cats) && !is_wp_error($product_cats)) ? $product_cats[0]->name : '';
                                $category_slug = (!empty($product_cats) && !is_wp_error($product_cats)) ? $product_cats[0]->slug : '';
                                
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
                                                <span class="icon-placeholder"><?php echo esc_html(mb_substr(get_the_title(), 0, 1)); ?></span>
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
                            <?php endwhile; ?>
                        <?php else : ?>
                            <div class="no-products">
                                <p>No products found matching your criteria.</p>
                            </div>
                        <?php endif; ?>
                        <?php wp_reset_postdata(); ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($products->max_num_pages > 1) : ?>
                    <nav class="library-pagination" aria-label="Product pagination">
                        <?php
                        echo paginate_links([
                            'total' => $products->max_num_pages,
                            'current' => $paged,
                            'prev_text' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg> Previous',
                            'next_text' => 'Next <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>',
                            'mid_size' => 2,
                            'end_size' => 1,
                        ]);
                        ?>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</div>

<?php get_footer(); ?>

