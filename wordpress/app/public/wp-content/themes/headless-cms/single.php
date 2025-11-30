<?php
/**
 * Single Post Template
 * 
 * Blog-style individual post page with featured image, author info, and content
 */

get_header();

// Get post data
$post_id = get_the_ID();
$categories = get_the_category($post_id);
$category_name = (!empty($categories)) ? $categories[0]->name : '';
$category_slug = (!empty($categories)) ? $categories[0]->slug : '';
$category_link = (!empty($categories)) ? get_category_link($categories[0]->term_id) : '';

// Get post meta
$author_id = get_the_author_meta('ID');
$author_name = get_the_author();
$author_avatar = get_avatar_url($author_id, ['size' => 96]);
$author_bio = get_the_author_meta('description');
$post_date = get_the_date('F j, Y');
$reading_time = ceil(str_word_count(strip_tags(get_the_content())) / 200);

// Get featured image
$featured_image = get_the_post_thumbnail_url($post_id, 'large');
$featured_image_alt = get_post_meta(get_post_thumbnail_id($post_id), '_wp_attachment_image_alt', true);

// Get tags
$post_tags = get_the_tags($post_id);

// Find blog archive page for back link
$blog_page_id = get_option('page_for_posts');
$blog_url = $blog_page_id ? get_permalink($blog_page_id) : home_url('/blog/');

// Get related posts (same category)
$related_posts = new WP_Query([
    'post_type' => 'post',
    'posts_per_page' => 3,
    'post__not_in' => [$post_id],
    'category__in' => wp_list_pluck($categories, 'term_id'),
    'orderby' => 'date',
    'order' => 'DESC',
]);
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php the_title(); ?> - <?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class('single-post-page'); ?>>

<div class="library-wrapper">
    <!-- Header -->
    <?php include(get_template_directory() . '/inc/header-nav.php'); ?>

    <!-- Main Content -->
    <main class="post-main">
        <article class="post-container">
            <!-- Post Header -->
            <header class="post-header">
                <?php if ($category_name) : ?>
                    <a href="<?php echo esc_url($category_link); ?>" class="post-category-badge">
                        <?php echo esc_html($category_name); ?>
                    </a>
                <?php endif; ?>
                
                <h1 class="post-title"><?php the_title(); ?></h1>
                
                <?php if (has_excerpt()) : ?>
                    <p class="post-excerpt"><?php echo esc_html(get_the_excerpt()); ?></p>
                <?php endif; ?>
                
                <div class="post-meta">
                    <div class="post-author">
                        <img src="<?php echo esc_url($author_avatar); ?>" alt="<?php echo esc_attr($author_name); ?>" class="author-avatar">
                        <div class="author-info">
                            <span class="author-name"><?php echo esc_html($author_name); ?></span>
                            <span class="post-date"><?php echo esc_html($post_date); ?> · <?php echo esc_html($reading_time); ?> min read</span>
                        </div>
                    </div>
                    
                    <div class="post-share">
                        <button class="share-btn" onclick="navigator.share ? navigator.share({title: '<?php echo esc_js(get_the_title()); ?>', url: '<?php echo esc_js(get_permalink()); ?>'}) : null" aria-label="Share post">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="18" cy="5" r="3"></circle>
                                <circle cx="6" cy="12" r="3"></circle>
                                <circle cx="18" cy="19" r="3"></circle>
                                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                                <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                            </svg>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Featured Image -->
            <?php if ($featured_image) : ?>
            <div class="post-featured-image">
                <img src="<?php echo esc_url($featured_image); ?>" alt="<?php echo esc_attr($featured_image_alt ?: get_the_title()); ?>">
            </div>
            <?php endif; ?>

            <!-- Post Content -->
            <div class="post-content-wrapper">
                <div class="post-content wysiwyg-content">
                    <?php the_content(); ?>
                </div>

                <!-- Tags -->
                <?php if (!empty($post_tags)) : ?>
                <div class="post-tags">
                    <span class="tags-label">Tags:</span>
                    <div class="tags-list">
                        <?php foreach ($post_tags as $tag) : ?>
                            <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="tag-link">
                                <?php echo esc_html($tag->name); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Author Box -->
            <?php if ($author_bio) : ?>
            <div class="author-box">
                <img src="<?php echo esc_url($author_avatar); ?>" alt="<?php echo esc_attr($author_name); ?>" class="author-box-avatar">
                <div class="author-box-content">
                    <span class="author-box-label">Written by</span>
                    <h3 class="author-box-name"><?php echo esc_html($author_name); ?></h3>
                    <p class="author-box-bio"><?php echo esc_html($author_bio); ?></p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Post Navigation -->
            <nav class="post-navigation">
                <?php
                $prev_post = get_previous_post();
                $next_post = get_next_post();
                ?>
                
                <?php if ($prev_post) : ?>
                <a href="<?php echo esc_url(get_permalink($prev_post->ID)); ?>" class="post-nav-link post-nav-prev">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                    <div class="post-nav-content">
                        <span class="post-nav-label">Previous</span>
                        <span class="post-nav-title"><?php echo esc_html($prev_post->post_title); ?></span>
                    </div>
                </a>
                <?php else : ?>
                <div class="post-nav-link post-nav-placeholder"></div>
                <?php endif; ?>
                
                <?php if ($next_post) : ?>
                <a href="<?php echo esc_url(get_permalink($next_post->ID)); ?>" class="post-nav-link post-nav-next">
                    <div class="post-nav-content">
                        <span class="post-nav-label">Next</span>
                        <span class="post-nav-title"><?php echo esc_html($next_post->post_title); ?></span>
                    </div>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
                <?php endif; ?>
            </nav>

            <!-- Related Posts -->
            <?php if ($related_posts->have_posts()) : ?>
            <section class="related-posts">
                <h2 class="related-posts-title">Related Articles</h2>
                <div class="related-posts-grid">
                    <?php while ($related_posts->have_posts()) : $related_posts->the_post(); ?>
                        <?php
                        $rel_featured = get_the_post_thumbnail_url(get_the_ID(), 'medium');
                        $rel_categories = get_the_category();
                        $rel_category = (!empty($rel_categories)) ? $rel_categories[0]->name : '';
                        ?>
                        <article class="related-post-card">
                            <?php if ($rel_featured) : ?>
                            <div class="related-post-image">
                                <img src="<?php echo esc_url($rel_featured); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                            </div>
                            <?php endif; ?>
                            <div class="related-post-content">
                                <?php if ($rel_category) : ?>
                                    <span class="related-post-category"><?php echo esc_html($rel_category); ?></span>
                                <?php endif; ?>
                                <h3 class="related-post-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <span class="related-post-date"><?php echo get_the_date('M j, Y'); ?></span>
                            </div>
                        </article>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                </div>
            </section>
            <?php endif; ?>

            <!-- Back to Blog -->
            <div class="back-to-blog">
                <a href="<?php echo esc_url($blog_url); ?>" class="back-link">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                    Back to Blog
                </a>
            </div>
        </article>
    </main>
</div>

<?php wp_footer(); ?>
</body>
</html>


