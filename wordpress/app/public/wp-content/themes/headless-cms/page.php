<?php
/**
 * Default Page Template
 * 
 * Gutenberg-friendly page template with full block support
 */

get_header();

// Get page data
$page_id = get_the_ID();
$featured_image = get_the_post_thumbnail_url($page_id, 'full');
$featured_image_alt = get_post_meta(get_post_thumbnail_id($page_id), '_wp_attachment_image_alt', true);

// Check if page has a custom layout width (can be set via ACF or page meta)
$layout_width = get_field('layout_width', $page_id) ?: 'default'; // default, wide, full
$show_title = get_field('show_title', $page_id) !== false; // Default to true
$show_featured_image = get_field('show_featured_image', $page_id) !== false; // Default to true

// Determine content width class
$width_class = 'page-width-default';
if ($layout_width === 'wide') {
    $width_class = 'page-width-wide';
} elseif ($layout_width === 'full') {
    $width_class = 'page-width-full';
}
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php the_title(); ?> - <?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class('default-page'); ?>>

<div class="library-wrapper">
    <!-- Header -->
    <?php include(get_template_directory() . '/inc/header-nav.php'); ?>

    <!-- Main Content -->
    <main class="page-main">
        <article class="page-container <?php echo esc_attr($width_class); ?>">
            
            <?php if ($show_title && get_the_title()) : ?>
            <!-- Page Header -->
            <header class="page-header">
                <h1 class="page-title"><?php the_title(); ?></h1>
                <?php if (has_excerpt()) : ?>
                    <p class="page-excerpt"><?php echo esc_html(get_the_excerpt()); ?></p>
                <?php endif; ?>
            </header>
            <?php endif; ?>

            <?php if ($show_featured_image && $featured_image) : ?>
            <!-- Featured Image -->
            <div class="page-featured-image">
                <img src="<?php echo esc_url($featured_image); ?>" alt="<?php echo esc_attr($featured_image_alt ?: get_the_title()); ?>">
            </div>
            <?php endif; ?>

            <!-- Page Content (Gutenberg Blocks) -->
            <div class="page-content">
                <?php 
                while (have_posts()) : the_post();
                    the_content();
                endwhile;
                ?>
            </div>

        </article>
    </main>
</div>

<?php wp_footer(); ?>
</body>
</html>

