<?php
/**
 * Reusable Header Navigation Component
 * 
 * Include this file in templates to render the site header
 * 
 * Usage: include(get_template_directory() . '/inc/header-nav.php');
 */

// Get library page URL for active state detection
$library_page = get_pages([
    'meta_key' => '_wp_page_template',
    'meta_value' => 'template-library.php',
    'number' => 1,
]);
$library_url = !empty($library_page) ? get_permalink($library_page[0]->ID) : home_url('/library/');

// Determine current page for active states
$is_library = is_page_template('template-library.php') || is_singular('product');
$is_blog = is_singular('post') || is_archive() || is_home() || is_search();

// Custom walker class for nav menu
class Headless_Nav_Walker extends Walker_Nav_Menu {
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $classes = [];
        
        // Check if current item
        if (in_array('current-menu-item', $item->classes) || 
            in_array('current_page_item', $item->classes) ||
            in_array('current-menu-ancestor', $item->classes)) {
            $classes[] = 'active';
        }
        
        // Check if this is the Library link and we're on a product page
        if (is_singular('product') && stripos($item->title, 'library') !== false) {
            $classes[] = 'active';
        }
        
        // Check if this is the Blog link and we're on a blog-related page
        if ((is_singular('post') || is_archive() || is_home() || is_search()) && 
            (stripos($item->title, 'blog') !== false || stripos($item->title, 'news') !== false || stripos($item->title, 'articles') !== false)) {
            $classes[] = 'active';
        }
        
        $class_attr = !empty($classes) ? ' class="' . implode(' ', $classes) . '"' : '';
        
        $output .= '<a href="' . esc_url($item->url) . '"' . $class_attr . '>';
        $output .= esc_html($item->title);
        $output .= '</a>';
    }
    
    function end_el(&$output, $item, $depth = 0, $args = null) {
        // No closing tag needed since we're outputting anchor tags directly
    }
}
?>

<header class="library-header">
    <div class="header-container">
        <a href="<?php echo home_url(); ?>" class="logo">
            <?php 
            // Try Custom Logo first, then Site Icon, then fallback to text
            $custom_logo_id = get_theme_mod('custom_logo');
            $site_icon_url = get_site_icon_url(512);
            
            if ($custom_logo_id) : 
                $logo_url = wp_get_attachment_image_url($custom_logo_id, 'full');
            ?>
                <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" class="logo-icon logo-image">
            <?php elseif ($site_icon_url) : ?>
                <img src="<?php echo esc_url($site_icon_url); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" class="logo-icon logo-image">
            <?php else : ?>
                <span class="logo-icon"><?php echo esc_html(mb_substr(get_bloginfo('name'), 0, 2)); ?></span>
            <?php endif; ?>
            <span class="logo-text"><?php echo esc_html(get_bloginfo('name')); ?></span>
        </a>
        
        <?php
        // Desktop Navigation
        wp_nav_menu([
            'menu' => 'menu',
            'container' => 'nav',
            'container_class' => 'header-nav header-nav-desktop',
            'menu_class' => '',
            'items_wrap' => '%3$s',
            'fallback_cb' => false,
            'link_before' => '',
            'link_after' => '',
            'walker' => new Headless_Nav_Walker(),
        ]);
        ?>
        
        <div class="header-search">
            <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.35-4.35"></path>
            </svg>
            <?php
            $search_value = '';
            if (isset($_GET['search'])) {
                $search_value = sanitize_text_field(wp_unslash($_GET['search']));
            }
            ?>
            <input type="text" id="header-search-input" placeholder="Search" value="<?php echo esc_attr($search_value); ?>">
        </div>
        
        <?php
        // User Account Menu
        $user_menu = function_exists('headless_cms_get_user_menu_data') ? headless_cms_get_user_menu_data() : null;
        if ($user_menu) :
        ?>
        <div class="header-user-menu">
            <?php if ($user_menu['logged_in']) : ?>
                <div class="user-dropdown">
                    <button class="user-dropdown-trigger" aria-expanded="false">
                        <img src="<?php echo esc_url($user_menu['avatar_url']); ?>" alt="" class="user-avatar">
                        <span class="user-name"><?php echo esc_html($user_menu['display_name']); ?></span>
                        <svg class="dropdown-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 9l6 6 6-6"/>
                        </svg>
                    </button>
                    <div class="user-dropdown-menu">
                        <div class="user-dropdown-header">
                            <span class="user-dropdown-name"><?php echo esc_html($user_menu['display_name']); ?></span>
                            <span class="user-dropdown-email"><?php echo esc_html($user_menu['email']); ?></span>
                        </div>
                        <div class="user-dropdown-items">
                            <?php if (current_user_can('edit_posts')) : ?>
                            <a href="<?php echo esc_url($user_menu['dashboard_url']); ?>" class="user-dropdown-item">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="7" height="9"/>
                                    <rect x="14" y="3" width="7" height="5"/>
                                    <rect x="14" y="12" width="7" height="9"/>
                                    <rect x="3" y="16" width="7" height="5"/>
                                </svg>
                                Dashboard
                            </a>
                            <?php endif; ?>
                            <a href="<?php echo esc_url($user_menu['profile_url']); ?>" class="user-dropdown-item">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                                Profile Settings
                            </a>
                        </div>
                        <div class="user-dropdown-footer">
                            <a href="<?php echo esc_url($user_menu['logout_url']); ?>" class="user-dropdown-item user-logout">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                    <polyline points="16,17 21,12 16,7"/>
                                    <line x1="21" y1="12" x2="9" y2="12"/>
                                </svg>
                                Sign Out
                            </a>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <a href="<?php echo esc_url($user_menu['login_url']); ?>" class="header-login-btn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                        <polyline points="10,17 15,12 10,7"/>
                        <line x1="15" y1="12" x2="3" y2="12"/>
                    </svg>
                    <span>Sign In</span>
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <!-- Mobile Menu Toggle -->
        <button class="mobile-menu-toggle" aria-label="Toggle menu" aria-expanded="false">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </button>
    </div>
</header>

<!-- Mobile Menu Overlay -->
<div class="mobile-menu-overlay" aria-hidden="true">
    <div class="mobile-menu-container">
        <div class="mobile-menu-header">
            <a href="<?php echo home_url(); ?>" class="logo">
                <?php if ($custom_logo_id) : ?>
                    <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" class="logo-icon logo-image">
                <?php elseif ($site_icon_url) : ?>
                    <img src="<?php echo esc_url($site_icon_url); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" class="logo-icon logo-image">
                <?php else : ?>
                    <span class="logo-icon"><?php echo esc_html(mb_substr(get_bloginfo('name'), 0, 2)); ?></span>
                <?php endif; ?>
                <span class="logo-text"><?php echo esc_html(get_bloginfo('name')); ?></span>
            </a>
            <button class="mobile-menu-close" aria-label="Close menu">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6L6 18M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <?php
        // Mobile Navigation
        wp_nav_menu([
            'menu' => 'menu',
            'container' => 'nav',
            'container_class' => 'mobile-nav',
            'menu_class' => '',
            'items_wrap' => '%3$s',
            'fallback_cb' => false,
            'link_before' => '',
            'link_after' => '',
            'walker' => new Headless_Nav_Walker(),
        ]);
        ?>
        
        <div class="mobile-menu-search">
            <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.35-4.35"></path>
            </svg>
            <input type="text" class="mobile-search-input" placeholder="Search" value="<?php echo esc_attr($search_value); ?>">
        </div>
    </div>
</div>

<script>
(function() {
    const menuToggle = document.querySelector('.mobile-menu-toggle');
    const menuOverlay = document.querySelector('.mobile-menu-overlay');
    const menuClose = document.querySelector('.mobile-menu-close');
    const body = document.body;
    
    function openMenu() {
        menuOverlay.classList.add('active');
        menuToggle.classList.add('active');
        menuToggle.setAttribute('aria-expanded', 'true');
        menuOverlay.setAttribute('aria-hidden', 'false');
        body.style.overflow = 'hidden';
    }
    
    function closeMenu() {
        menuOverlay.classList.remove('active');
        menuToggle.classList.remove('active');
        menuToggle.setAttribute('aria-expanded', 'false');
        menuOverlay.setAttribute('aria-hidden', 'true');
        body.style.overflow = '';
    }
    
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            if (menuOverlay.classList.contains('active')) {
                closeMenu();
            } else {
                openMenu();
            }
        });
    }
    
    if (menuClose) {
        menuClose.addEventListener('click', closeMenu);
    }
    
    // Close on overlay click (outside menu container)
    if (menuOverlay) {
        menuOverlay.addEventListener('click', function(e) {
            if (e.target === menuOverlay) {
                closeMenu();
            }
        });
    }
    
    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && menuOverlay.classList.contains('active')) {
            closeMenu();
        }
    });
    
    // Sync mobile search with header search
    const mobileSearchInput = document.querySelector('.mobile-search-input');
    const headerSearchInput = document.getElementById('header-search-input');
    
    if (mobileSearchInput && headerSearchInput) {
        mobileSearchInput.addEventListener('input', function() {
            headerSearchInput.value = this.value;
            headerSearchInput.dispatchEvent(new Event('input', { bubbles: true }));
        });
        
        mobileSearchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                headerSearchInput.dispatchEvent(new KeyboardEvent('keypress', { key: 'Enter', bubbles: true }));
            }
        });
    }
    
    // User Dropdown Menu
    const userDropdown = document.querySelector('.user-dropdown');
    const dropdownTrigger = document.querySelector('.user-dropdown-trigger');
    
    if (userDropdown && dropdownTrigger) {
        dropdownTrigger.addEventListener('click', function(e) {
            e.stopPropagation();
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !isExpanded);
            userDropdown.classList.toggle('active');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userDropdown.contains(e.target)) {
                dropdownTrigger.setAttribute('aria-expanded', 'false');
                userDropdown.classList.remove('active');
            }
        });
        
        // Close dropdown on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && userDropdown.classList.contains('active')) {
                dropdownTrigger.setAttribute('aria-expanded', 'false');
                userDropdown.classList.remove('active');
            }
        });
    }
})();
</script>

