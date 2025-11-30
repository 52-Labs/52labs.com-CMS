<?php
/**
 * Archive Template
 * 
 * Blog-style archive page displaying posts with filtering and categories
 */

get_header();

// Get all categories
$categories = get_categories([
    'hide_empty' => true,
    'orderby' => 'name',
    'order' => 'ASC',
]);

// Get filter parameters
$selected_category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';
$search_query = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';

// Determine archive type and title
$archive_title = 'Latest Articles';
$archive_description = '';

if (is_category()) {
    $archive_title = single_cat_title('', false);
    $archive_description = category_description();
    $selected_category = get_queried_object()->slug;
} elseif (is_tag()) {
    $archive_title = 'Posts tagged: ' . single_tag_title('', false);
    $archive_description = tag_description();
} elseif (is_author()) {
    $archive_title = 'Posts by ' . get_the_author();
    $archive_description = get_the_author_meta('description');
} elseif (is_date()) {
    if (is_day()) {
        $archive_title = 'Posts from ' . get_the_date('F j, Y');
    } elseif (is_month()) {
        $archive_title = 'Posts from ' . get_the_date('F Y');
    } elseif (is_year()) {
        $archive_title = 'Posts from ' . get_the_date('Y');
    }
} elseif (is_search()) {
    $archive_title = 'Search Results';
    $search_query = get_search_query();
}

// Build posts query with pagination
$paged = (get_query_var('paged')) ? absint(get_query_var('paged')) : 1;
$posts_per_page = apply_filters('headless_cms_archive_posts_per_page', 12);
$posts_args = [
    'post_type' => 'post',
    'posts_per_page' => $posts_per_page,
    'paged' => $paged,
    'orderby' => 'date',
    'order' => 'DESC',
    'post_status' => 'publish',
];

// Add category filter
if (!empty($selected_category)) {
    $posts_args['category_name'] = $selected_category;
}

// Add search filter
if (!empty($search_query)) {
    $posts_args['s'] = $search_query;
}

$posts_query = new WP_Query($posts_args);
$total_posts = $posts_query->found_posts;
$total_pages = $posts_query->max_num_pages;
?>

<div class="library-wrapper">
    <!-- Header -->
    <?php include(get_template_directory() . '/inc/header-nav.php'); ?>

    <!-- Main Content -->
    <main class="library-main">
        <div class="library-container">
            <!-- Page Header -->
            <header class="archive-header">
                <h1 class="library-title"><?php echo esc_html($archive_title); ?></h1>
                <?php if ($archive_description) : ?>
                    <p class="archive-description"><?php echo wp_kses_post($archive_description); ?></p>
                <?php endif; ?>
                <div class="archive-meta">
                    <span class="posts-count"><?php echo esc_html($total_posts); ?> article<?php echo $total_posts !== 1 ? 's' : ''; ?></span>
                </div>
            </header>

            <div class="library-content">
                <!-- Sidebar Filters -->
                <aside class="library-sidebar">
                    <!-- Category Filter -->
                    <div class="filter-section">
                        <button class="filter-header" aria-expanded="true">
                            <span>Categories</span>
                            <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="18 15 12 9 6 15"></polyline>
                            </svg>
                        </button>
                        <div class="filter-options">
                            <label class="filter-radio">
                                <input type="radio" 
                                       name="category" 
                                       value=""
                                       <?php checked(empty($selected_category)); ?>>
                                <span class="radio-custom"></span>
                                <span class="radio-label">All Categories</span>
                            </label>
                            <?php if (!empty($categories)) : ?>
                                <?php foreach ($categories as $category) : ?>
                                    <label class="filter-radio">
                                        <input type="radio" 
                                               name="category" 
                                               value="<?php echo esc_attr($category->slug); ?>"
                                               <?php checked($category->slug === $selected_category); ?>>
                                        <span class="radio-custom"></span>
                                        <span class="radio-label"><?php echo esc_html($category->name); ?></span>
                                        <span class="category-count"><?php echo esc_html($category->count); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Recent Posts Widget -->
                    <?php
                    $recent_posts = new WP_Query([
                        'post_type' => 'post',
                        'posts_per_page' => 5,
                        'orderby' => 'date',
                        'order' => 'DESC',
                    ]);
                    
                    if ($recent_posts->have_posts()) :
                    ?>
                    <div class="filter-section recent-posts-widget">
                        <div class="filter-header static" aria-expanded="true">
                            <span>Recent Posts</span>
                        </div>
                        <div class="recent-posts-list">
                            <?php while ($recent_posts->have_posts()) : $recent_posts->the_post(); ?>
                                <a href="<?php the_permalink(); ?>" class="recent-post-item">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="recent-post-thumb">
                                            <?php the_post_thumbnail('thumbnail'); ?>
                                        </div>
                                    <?php else : ?>
                                        <div class="recent-post-thumb recent-post-thumb-placeholder">
                                            <span><?php echo esc_html(substr(get_the_title(), 0, 1)); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="recent-post-info">
                                        <span class="recent-post-title"><?php echo esc_html(wp_trim_words(get_the_title(), 6)); ?></span>
                                        <span class="recent-post-date"><?php echo get_the_date('M j'); ?></span>
                                    </div>
                                </a>
                            <?php endwhile; ?>
                            <?php wp_reset_postdata(); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </aside>

                <!-- Posts Grid -->
                <div class="products-section">
                    <div class="posts-grid" id="posts-grid">
                        <?php if ($posts_query->have_posts()) : ?>
                            <?php while ($posts_query->have_posts()) : $posts_query->the_post(); 
                                $post_id = get_the_ID();
                                
                                // Get categories
                                $post_cats = get_the_category($post_id);
                                $cat_name = (!empty($post_cats)) ? $post_cats[0]->name : '';
                                $cat_slug = (!empty($post_cats)) ? $post_cats[0]->slug : '';
                                $cat_link = (!empty($post_cats)) ? get_category_link($post_cats[0]->term_id) : '';
                                
                                // Get featured image
                                $thumbnail = get_the_post_thumbnail_url($post_id, 'medium_large');
                                
                                // Calculate reading time
                                $reading_time = ceil(str_word_count(strip_tags(get_the_content())) / 200);
                                
                                // Get author
                                $author_name = get_the_author();
                            ?>
                                <article class="post-card" data-category="<?php echo esc_attr($cat_slug); ?>">
                                    <a href="<?php the_permalink(); ?>" class="post-card-link">
                                        <div class="post-card-inner">
                                            <?php if ($thumbnail) : ?>
                                            <div class="post-card-image">
                                                <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                                            </div>
                                            <?php else : ?>
                                            <div class="post-card-image post-card-image-placeholder">
                                                <span class="post-card-icon">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                        <path d="M19 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2z"></path>
                                                        <polyline points="9 7 9 11 12 9"></polyline>
                                                        <line x1="3" y1="21" x2="21" y2="3"></line>
                                                    </svg>
                                                </span>
                                            </div>
                                            <?php endif; ?>
                                            <div class="post-card-content">
                                                <?php if ($cat_name) : ?>
                                                    <span class="post-card-category"><?php echo esc_html($cat_name); ?></span>
                                                <?php endif; ?>
                                                <h3 class="post-card-title"><?php the_title(); ?></h3>
                                                <p class="post-card-excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 18, '...')); ?></p>
                                                <div class="post-card-meta">
                                                    <span class="post-card-author"><?php echo esc_html($author_name); ?></span>
                                                    <span class="post-card-date"><?php echo get_the_date('M j, Y'); ?></span>
                                                    <span class="post-card-reading-time"><?php echo esc_html($reading_time); ?> min</span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </article>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <div class="no-posts">
                                <div class="no-posts-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                        <line x1="12" y1="18" x2="12" y2="12"></line>
                                        <line x1="9" y1="15" x2="15" y2="15"></line>
                                    </svg>
                                </div>
                                <h3>No posts found</h3>
                                <p>We couldn't find any articles matching your criteria. Try adjusting your filters or search terms.</p>
                            </div>
                        <?php endif; ?>
                        <?php wp_reset_postdata(); ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1) : ?>
                    <nav class="archive-pagination">
                        <?php
                        $pagination_args = [
                            'total' => $total_pages,
                            'current' => $paged,
                            'prev_text' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg> Previous',
                            'next_text' => 'Next <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>',
                            'mid_size' => 2,
                            'end_size' => 1,
                        ];
                        
                        echo paginate_links($pagination_args);
                        ?>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</div>

<?php get_footer(); ?>

