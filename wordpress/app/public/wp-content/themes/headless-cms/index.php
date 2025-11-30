<?php
/**
 * Main Index Template
 * 
 * The main template file - used as fallback for all template types.
 * For posts display, loads the archive template.
 */

// If this is the blog home or any archive, use the archive template
if (is_home() || is_archive() || is_search()) {
    get_template_part('archive');
    exit;
}

// For single posts, use the single template
if (is_singular('post')) {
    get_template_part('single');
    exit;
}

// For single products, use the single-product template
if (is_singular('product')) {
    get_template_part('single-product');
    exit;
}

// Default fallback - basic page structure
get_header();
?>

<div class="library-wrapper">
    <?php include(get_template_directory() . '/inc/header-nav.php'); ?>

    <main class="library-main">
        <div class="library-container">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                        <h1><?php the_title(); ?></h1>
                        <div class="entry-content">
                            <?php the_content(); ?>
                        </div>
                    </article>
                <?php endwhile; ?>
            <?php else : ?>
                <p>No content found.</p>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php get_footer(); ?>
