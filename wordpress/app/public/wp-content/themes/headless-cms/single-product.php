<?php
/**
 * Single Product Template
 * 
 * App Store-style individual product page with screenshots, instructions, and downloads
 */

get_header();

// Get product data
$product_id = get_the_ID();
$product_cats = get_the_terms($product_id, 'product_cat');
$category_name = (!empty($product_cats) && !is_wp_error($product_cats)) ? $product_cats[0]->name : '';
$category_slug = (!empty($product_cats) && !is_wp_error($product_cats)) ? $product_cats[0]->slug : '';

// Get ACF fields (top-level fields on product)
$logo = get_field('logo', $product_id);
$description = get_field('description', $product_id) ?: get_the_excerpt();
$background_color = get_field('backgroundColor', $product_id) ?: '#3B82F6';
$learn_more_url = get_field('learnMoreUrl', $product_id) ?: '';

// Product features (repeater field)
$product_features = get_field('productFeaturesRepeater', $product_id) ?: [];

// Screenshots/Gallery
$screenshots = get_field('media', $product_id) ?: [];

// Instructions (WYSIWYG fields)
$installation_instructions = get_field('installationinstructions', $product_id) ?: '';
$usage_instructions = get_field('usageinstructions', $product_id) ?: '';
$faqs = get_field('faqs', $product_id) ?: '';

// Download URL (link field returns array with url, title, target)
$download_link = get_field('downloadurl', $product_id);
$download_url = is_array($download_link) ? ($download_link['url'] ?? '') : '';

// Find library page for back link
$library_page = get_pages([
    'meta_key' => '_wp_page_template',
    'meta_value' => 'template-library.php',
    'number' => 1,
]);
$library_url = !empty($library_page) ? get_permalink($library_page[0]->ID) : home_url('/app-library/');

// Check if we have content for tabs
$has_features = !empty($product_features);
$has_how_to_use = !empty($usage_instructions);
$has_download_instructions = !empty($installation_instructions);
$has_faqs = !empty($faqs);
$tab_count = ($has_features ? 1 : 0) + ($has_how_to_use ? 1 : 0) + ($has_download_instructions ? 1 : 0) + ($has_faqs ? 1 : 0);
$has_tabs = $tab_count > 1;

<div class="library-wrapper">
    <!-- Header -->
    <?php include(get_template_directory() . '/inc/header-nav.php'); ?>

    <!-- Main Content -->
    <main class="product-main">
        <div class="product-container">
            <!-- Product Hero -->
            <section class="product-hero">
                <div class="product-hero-content">
                    <div class="product-hero-icon" style="background-color: <?php echo esc_attr($background_color); ?>">
                        <?php if ($logo) : ?>
                            <img src="<?php echo esc_url($logo['url']); ?>" alt="<?php echo esc_attr(get_the_title()); ?> icon">
                        <?php else : ?>
                            <span class="icon-placeholder"><?php echo esc_html(substr(get_the_title(), 0, 1)); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="product-hero-info">
                        <div class="product-hero-top">
                            <div>
                                <h1 class="product-hero-title"><?php the_title(); ?></h1>
                                <span class="product-hero-category"><?php echo esc_html($category_name); ?></span>
                            </div>
                            <?php if ($download_url) : ?>
                                <a href="<?php echo esc_url($download_url); ?>" class="btn btn-primary btn-download" target="_blank" rel="noopener">
                                    <svg class="download-icon-small" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="7 10 12 15 17 10"></polyline>
                                        <line x1="12" y1="15" x2="12" y2="3"></line>
                                    </svg>
                                    Get It
                                </a>
                            <?php endif; ?>
                        </div>
                        <p class="product-hero-description"><?php echo esc_html($description); ?></p>
                        
                        <?php if (!empty($screenshots)) : ?>
                        <!-- Screenshots Thumbnails -->
                        <div class="screenshots-inline">
                            <?php foreach ($screenshots as $index => $screenshot) : ?>
                                <button class="screenshot-thumb" data-index="<?php echo $index; ?>" aria-label="View screenshot <?php echo $index + 1; ?>">
                                    <img src="<?php echo esc_url($screenshot['sizes']['medium'] ?? $screenshot['url']); ?>" 
                                         alt="<?php echo esc_attr($screenshot['alt'] ?: get_the_title() . ' screenshot ' . ($index + 1)); ?>">
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <!-- Lightbox -->
            <?php if (!empty($screenshots)) : ?>
            <div class="lightbox" id="screenshot-lightbox" aria-hidden="true">
                <div class="lightbox-overlay"></div>
                <div class="lightbox-content">
                    <button class="lightbox-close" aria-label="Close lightbox">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                    <button class="lightbox-nav lightbox-prev" aria-label="Previous screenshot">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                    </button>
                    <div class="lightbox-image-container">
                        <?php foreach ($screenshots as $index => $screenshot) : ?>
                            <img class="lightbox-image <?php echo $index === 0 ? 'active' : ''; ?>" 
                                 src="<?php echo esc_url($screenshot['url']); ?>" 
                                 alt="<?php echo esc_attr($screenshot['alt'] ?: get_the_title() . ' screenshot ' . ($index + 1)); ?>"
                                 data-index="<?php echo $index; ?>">
                        <?php endforeach; ?>
                    </div>
                    <button class="lightbox-nav lightbox-next" aria-label="Next screenshot">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </button>
                    <div class="lightbox-counter">
                        <span class="lightbox-current">1</span> / <span class="lightbox-total"><?php echo count($screenshots); ?></span>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Content Section -->
            <?php if ($has_features || $has_how_to_use || $has_download_instructions || $has_faqs) : ?>
            <section class="product-content-section">
                <?php if ($has_tabs) : ?>
                <!-- Tab Navigation -->
                <div class="tab-navigation">
                    <?php $first_tab = true; ?>
                    <?php if ($has_features) : ?>
                        <button class="tab-btn <?php echo $first_tab ? 'active' : ''; ?>" data-tab="features">Key Features</button>
                        <?php $first_tab = false; ?>
                    <?php endif; ?>
                    <?php if ($has_download_instructions) : ?>
                        <button class="tab-btn <?php echo $first_tab ? 'active' : ''; ?>" data-tab="download">Download Instructions</button>
                        <?php $first_tab = false; ?>
                    <?php endif; ?>
                    <?php if ($has_how_to_use) : ?>
                        <button class="tab-btn <?php echo $first_tab ? 'active' : ''; ?>" data-tab="how-to-use">How to Use</button>
                        <?php $first_tab = false; ?>
                    <?php endif; ?>
                    <?php if ($has_faqs) : ?>
                        <button class="tab-btn <?php echo $first_tab ? 'active' : ''; ?>" data-tab="faqs">FAQs</button>
                    <?php endif; ?>
                </div>
                
                <!-- Tab Content -->
                <div class="tab-content">
                    <?php $first_panel = true; ?>
                    
                    <?php if ($has_features) : ?>
                    <!-- Key Features Tab -->
                    <div class="tab-panel <?php echo $first_panel ? 'active' : ''; ?>" id="tab-features">
                        <div class="features-list">
                            <?php foreach ($product_features as $index => $feature_item) : ?>
                                <div class="feature-item">
                                    <span class="feature-icon">
                                        <?php
                                        $icons = ['⭐', '🎯', '⚡', '🔧'];
                                        echo $icons[$index % count($icons)];
                                        ?>
                                    </span>
                                    <span class="feature-text"><?php echo esc_html($feature_item['productfeatures']); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php $first_panel = false; ?>
                    <?php endif; ?>
                    
                    <?php if ($has_download_instructions) : ?>
                    <!-- Download Instructions Tab -->
                    <div class="tab-panel <?php echo $first_panel ? 'active' : ''; ?>" id="tab-download">
                        <?php if ($download_url) : ?>
                        <div class="download-cta">
                            <a href="<?php echo esc_url($download_url); ?>" class="btn btn-primary btn-download-large" target="_blank" rel="noopener">
                                <svg class="download-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7 10 12 15 17 10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                                <?php 
                                $link_title = is_array($download_link) && !empty($download_link['title']) 
                                    ? $download_link['title'] 
                                    : 'Get It';
                                echo esc_html($link_title);
                                ?>
                            </a>
                        </div>
                        <?php endif; ?>
                        <div class="wysiwyg-content">
                            <?php echo wp_kses_post($installation_instructions); ?>
                        </div>
                    </div>
                    <?php $first_panel = false; ?>
                    <?php endif; ?>
                    
                    <?php if ($has_how_to_use) : ?>
                    <!-- How to Use Tab -->
                    <div class="tab-panel <?php echo $first_panel ? 'active' : ''; ?>" id="tab-how-to-use">
                        <div class="wysiwyg-content">
                            <?php echo wp_kses_post($usage_instructions); ?>
                        </div>
                    </div>
                    <?php $first_panel = false; ?>
                    <?php endif; ?>
                    
                    <?php if ($has_faqs) : ?>
                    <!-- FAQs Tab -->
                    <div class="tab-panel <?php echo $first_panel ? 'active' : ''; ?>" id="tab-faqs">
                        <div class="wysiwyg-content faq-content">
                            <?php echo wp_kses_post($faqs); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php elseif ($has_features) : ?>
                <!-- Only Features (no tabs needed) -->
                <h2 class="section-title">Key Features</h2>
                <div class="features-list">
                    <?php foreach ($product_features as $index => $feature_item) : ?>
                        <div class="feature-item">
                            <span class="feature-icon">
                                <?php
                                $icons = ['⭐', '🎯', '⚡', '🔧'];
                                echo $icons[$index % count($icons)];
                                ?>
                            </span>
                            <span class="feature-text"><?php echo esc_html($feature_item['productfeatures']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php elseif ($has_how_to_use) : ?>
                <!-- Only How to Use (no tabs needed) -->
                <h2 class="section-title">How to Use</h2>
                <div class="wysiwyg-content">
                    <?php echo wp_kses_post($usage_instructions); ?>
                </div>
                
                <?php elseif ($has_download_instructions) : ?>
                <!-- Only Download Instructions (no tabs needed) -->
                <h2 class="section-title">Download Instructions</h2>
                <?php if ($download_url) : ?>
                <div class="download-cta">
                    <a href="<?php echo esc_url($download_url); ?>" class="btn btn-primary btn-download-large" target="_blank" rel="noopener">
                        <svg class="download-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        <?php 
                        $link_title = is_array($download_link) && !empty($download_link['title']) 
                            ? $download_link['title'] 
                            : 'Get It';
                        echo esc_html($link_title);
                        ?>
                    </a>
                </div>
                <?php endif; ?>
                <div class="wysiwyg-content">
                    <?php echo wp_kses_post($installation_instructions); ?>
                </div>
                
                <?php elseif ($has_faqs) : ?>
                <!-- Only FAQs (no tabs needed) -->
                <h2 class="section-title">FAQs</h2>
                <div class="wysiwyg-content faq-content">
                    <?php echo wp_kses_post($faqs); ?>
                </div>
                <?php endif; ?>
            </section>
            <?php endif; ?>

            <!-- Back to Library -->
            <div class="back-to-library">
                <a href="<?php echo esc_url($library_url); ?>" class="back-link">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                    Back to Library
                </a>
            </div>
        </div>
    </main>
</div>

<?php get_footer(); ?>
