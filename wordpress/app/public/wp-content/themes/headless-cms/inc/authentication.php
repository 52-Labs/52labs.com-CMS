<?php
/**
 * Authentication & Content Gating Functions
 * 
 * Handles custom login, registration with domain restriction, and content gating.
 * 
 * ACF Fields Required in Site Settings (options page):
 * - allowed_domains (Repeater)
 *   - domain (Text) - e.g., "gate52.com", "gmail.com"
 * - enable_content_gating (True/False) - Default: true
 */

// ========================================
// Login Handler
// ========================================

/**
 * Handle custom login form submission
 */
function headless_cms_handle_login() {
    // Verify nonce
    if (!isset($_POST['login_nonce']) || !wp_verify_nonce($_POST['login_nonce'], 'headless_login_nonce')) {
        wp_die('Security check failed', 'Security Error', ['response' => 403]);
    }

    // Get login page URL for redirects
    $login_page = headless_cms_get_login_page_url();

    // Validate input
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $remember = isset($_POST['remember']) && $_POST['remember'] === '1';
    $redirect_to = isset($_POST['redirect_to']) ? esc_url_raw($_POST['redirect_to']) : home_url('/');

    if (empty($email) || empty($password)) {
        wp_safe_redirect(add_query_arg('error', 'empty_fields', $login_page));
        exit;
    }

    if (!is_email($email)) {
        wp_safe_redirect(add_query_arg('error', 'invalid_email', $login_page));
        exit;
    }

    // Get user by email
    $user = get_user_by('email', $email);

    if (!$user) {
        wp_safe_redirect(add_query_arg('error', 'invalid_credentials', $login_page));
        exit;
    }

    // Attempt login
    $credentials = [
        'user_login'    => $user->user_login,
        'user_password' => $password,
        'remember'      => $remember,
    ];

    $user = wp_signon($credentials, is_ssl());

    if (is_wp_error($user)) {
        wp_safe_redirect(add_query_arg('error', 'invalid_credentials', $login_page));
        exit;
    }

    // Success - redirect to requested page
    wp_safe_redirect($redirect_to);
    exit;
}
add_action('admin_post_nopriv_headless_login', 'headless_cms_handle_login');
add_action('admin_post_headless_login', 'headless_cms_handle_login');


// ========================================
// Registration Handler with Domain Restriction
// ========================================

/**
 * Handle custom registration form submission
 */
function headless_cms_handle_registration() {
    // Verify nonce
    if (!isset($_POST['register_nonce']) || !wp_verify_nonce($_POST['register_nonce'], 'headless_register_nonce')) {
        wp_die('Security check failed', 'Security Error', ['response' => 403]);
    }

    // Get login page URL for redirects
    $login_page = headless_cms_get_login_page_url();
    $register_page = add_query_arg('mode', 'register', $login_page);

    // Check if registration is enabled
    if (!get_option('users_can_register')) {
        wp_safe_redirect(add_query_arg('error', 'registration_disabled', $login_page));
        exit;
    }

    // Validate input
    $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';

    // Empty field check
    if (empty($name) || empty($email) || empty($password) || empty($password_confirm)) {
        wp_safe_redirect(add_query_arg('error', 'empty_fields', $register_page));
        exit;
    }

    // Validate email format
    if (!is_email($email)) {
        wp_safe_redirect(add_query_arg('error', 'invalid_email', $register_page));
        exit;
    }

    // Check if email already exists
    if (email_exists($email)) {
        wp_safe_redirect(add_query_arg('error', 'email_exists', $register_page));
        exit;
    }

    // Password match check
    if ($password !== $password_confirm) {
        wp_safe_redirect(add_query_arg('error', 'password_mismatch', $register_page));
        exit;
    }

    // Password strength check (minimum 8 characters)
    if (strlen($password) < 8) {
        wp_safe_redirect(add_query_arg('error', 'weak_password', $register_page));
        exit;
    }

    // Domain restriction check
    if (!headless_cms_is_email_domain_allowed($email)) {
        wp_safe_redirect(add_query_arg('error', 'domain_not_allowed', $register_page));
        exit;
    }

    // Create username from email
    $username = sanitize_user(current(explode('@', $email)), true);
    $base_username = $username;
    $counter = 1;

    // Ensure unique username
    while (username_exists($username)) {
        $username = $base_username . $counter;
        $counter++;
    }

    // Create user
    $user_id = wp_create_user($username, $password, $email);

    if (is_wp_error($user_id)) {
        headless_cms_log_error('Registration failed', $user_id->get_error_message());
        wp_safe_redirect(add_query_arg('error', 'registration_failed', $register_page));
        exit;
    }

    // Update user display name
    wp_update_user([
        'ID'           => $user_id,
        'display_name' => $name,
        'first_name'   => current(explode(' ', $name)),
        'last_name'    => count(explode(' ', $name)) > 1 ? end(explode(' ', $name)) : '',
    ]);

    // Set user role (default to subscriber)
    $user = new WP_User($user_id);
    $user->set_role('subscriber');

    // Send new user notification
    wp_new_user_notification($user_id, null, 'both');

    // Success - redirect to login with success message
    wp_safe_redirect(add_query_arg('success', 'registered', $login_page));
    exit;
}
add_action('admin_post_nopriv_headless_register', 'headless_cms_handle_registration');
add_action('admin_post_headless_register', 'headless_cms_handle_registration');


// ========================================
// Domain Restriction Functions
// ========================================

/**
 * Check if an email's domain is allowed for registration
 * 
 * @param string $email The email address to check
 * @return bool True if domain is allowed or no restrictions set, false otherwise
 */
function headless_cms_is_email_domain_allowed($email) {
    // Get allowed domains from ACF
    $allowed_domains = headless_cms_get_allowed_domains();

    // If no domains configured, allow all
    if (empty($allowed_domains)) {
        return true;
    }

    // Extract domain from email
    $email_parts = explode('@', strtolower($email));
    if (count($email_parts) !== 2) {
        return false;
    }

    $email_domain = $email_parts[1];

    // Check if domain is in allowed list
    return in_array($email_domain, $allowed_domains, true);
}

/**
 * Get list of allowed email domains from ACF Site Settings
 * 
 * @return array List of allowed domains (lowercase)
 */
function headless_cms_get_allowed_domains() {
    $allowed_domains = [];

    if (!function_exists('get_field')) {
        return $allowed_domains;
    }

    // Get repeater field from options page
    $domains_repeater = get_field('allowed_domains', 'option');

    if ($domains_repeater && is_array($domains_repeater)) {
        foreach ($domains_repeater as $row) {
            if (isset($row['domain']) && !empty($row['domain'])) {
                // Clean and normalize domain
                $domain = strtolower(trim($row['domain']));
                // Remove @ if user accidentally added it
                $domain = ltrim($domain, '@');
                if (!empty($domain)) {
                    $allowed_domains[] = $domain;
                }
            }
        }
    }

    return $allowed_domains;
}


// ========================================
// Content Gating Functions
// ========================================

/**
 * Check if content gating is enabled
 * 
 * ACF Field: enable_content_gating (True/False)
 * Default: true (ON)
 * 
 * @return bool True if content gating is enabled
 */
function headless_cms_is_content_gating_enabled() {
    if (!function_exists('get_field')) {
        return true; // Default to enabled if ACF not available
    }

    // Get the value from ACF options
    // Field name: enable_content_gating
    $gating_enabled = get_field('enable_content_gating', 'option');

    // If field doesn't exist yet, default to true (ON)
    if ($gating_enabled === null) {
        return true;
    }

    return (bool) $gating_enabled;
}

/**
 * Check if current page should bypass content gating
 * Certain pages like login, lost password, etc. should not be gated
 * 
 * @return bool True if page should bypass gating
 */
function headless_cms_should_bypass_gating() {
    // Always allow WP admin
    if (is_admin()) {
        return true;
    }

    // Allow AJAX/API requests
    if (wp_doing_ajax() || defined('REST_REQUEST') || (defined('GRAPHQL_REQUEST') && GRAPHQL_REQUEST)) {
        return true;
    }

    // Allow login page
    if (is_page_template('template-login.php')) {
        return true;
    }

    // Allow lost password page
    if (strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false) {
        return true;
    }

    // Allow cron
    if (wp_doing_cron()) {
        return true;
    }

    // Allow robots.txt and favicon
    if (strpos($_SERVER['REQUEST_URI'], 'robots.txt') !== false || 
        strpos($_SERVER['REQUEST_URI'], 'favicon') !== false) {
        return true;
    }

    // Allow specific pages by slug (customizable via filter)
    $bypass_pages = apply_filters('headless_cms_gating_bypass_pages', [
        'terms',
        'privacy',
        'privacy-policy',
        'terms-of-service',
    ]);

    global $post;
    if ($post && in_array($post->post_name, $bypass_pages, true)) {
        return true;
    }

    return false;
}

/**
 * Redirect non-logged-in users to login page if content gating is enabled
 */
function headless_cms_content_gating_redirect() {
    // Skip if gating is disabled
    if (!headless_cms_is_content_gating_enabled()) {
        return;
    }

    // Skip if user is logged in
    if (is_user_logged_in()) {
        return;
    }

    // Skip for allowed pages
    if (headless_cms_should_bypass_gating()) {
        return;
    }

    // Get login page URL
    $login_page = headless_cms_get_login_page_url();

    if (!$login_page) {
        // Fallback to wp-login.php if no custom login page
        $login_page = wp_login_url();
    }

    // Add current URL as redirect_to parameter
    $current_url = home_url(add_query_arg([], $_SERVER['REQUEST_URI']));
    $login_url = add_query_arg('redirect_to', urlencode($current_url), $login_page);

    wp_safe_redirect($login_url);
    exit;
}
add_action('template_redirect', 'headless_cms_content_gating_redirect');


// ========================================
// Helper Functions
// ========================================

/**
 * Get the URL of the login page (using template-login.php)
 * 
 * @return string|false Login page URL or false if not found
 */
function headless_cms_get_login_page_url() {
    // Find page using login template
    $login_pages = get_posts([
        'post_type'      => 'page',
        'posts_per_page' => 1,
        'meta_key'       => '_wp_page_template',
        'meta_value'     => 'template-login.php',
        'post_status'    => 'publish',
    ]);

    if (!empty($login_pages)) {
        return get_permalink($login_pages[0]->ID);
    }

    // Fallback - return false
    return false;
}

/**
 * Custom logout URL that redirects to login page
 * 
 * @param string $logout_url The default logout URL
 * @return string Modified logout URL
 */
function headless_cms_custom_logout_url($logout_url) {
    $login_page = headless_cms_get_login_page_url();

    if ($login_page) {
        $success_url = add_query_arg('success', 'logged_out', $login_page);
        return wp_logout_url($success_url);
    }

    return $logout_url;
}


// ========================================
// Override Default WordPress Login
// ========================================

/**
 * Redirect default wp-login.php to custom login page
 */
function headless_cms_redirect_login_page() {
    $login_page = headless_cms_get_login_page_url();

    if (!$login_page) {
        return;
    }

    global $pagenow;

    // Only redirect for login page, not admin pages
    if ($pagenow === 'wp-login.php' && !is_user_logged_in()) {
        // Preserve action and redirect_to parameters
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        
        // Allow certain actions to proceed (password reset, etc.)
        $allowed_actions = ['logout', 'lostpassword', 'retrievepassword', 'resetpass', 'rp', 'register'];
        
        if (in_array($action, $allowed_actions)) {
            return;
        }

        // Redirect to custom login page
        $redirect_to = isset($_GET['redirect_to']) ? $_GET['redirect_to'] : '';
        $login_url = $login_page;

        if ($redirect_to) {
            $login_url = add_query_arg('redirect_to', urlencode($redirect_to), $login_url);
        }

        wp_safe_redirect($login_url);
        exit;
    }
}
add_action('init', 'headless_cms_redirect_login_page');

/**
 * Override login URL globally
 * 
 * @param string $login_url The default login URL
 * @param string $redirect  Redirect URL after login
 * @return string Custom login URL
 */
function headless_cms_login_url($login_url, $redirect = '') {
    $custom_login = headless_cms_get_login_page_url();

    if ($custom_login) {
        if (!empty($redirect)) {
            return add_query_arg('redirect_to', urlencode($redirect), $custom_login);
        }
        return $custom_login;
    }

    return $login_url;
}
add_filter('login_url', 'headless_cms_login_url', 10, 2);


// ========================================
// Shortcode for Logout Link
// ========================================

/**
 * Shortcode to display logout link
 * Usage: [logout_link text="Sign Out" class="my-class"]
 */
function headless_cms_logout_link_shortcode($atts) {
    if (!is_user_logged_in()) {
        return '';
    }

    $atts = shortcode_atts([
        'text'  => 'Sign Out',
        'class' => 'logout-link',
    ], $atts);

    $login_page = headless_cms_get_login_page_url();
    $redirect = $login_page ? add_query_arg('success', 'logged_out', $login_page) : home_url('/');
    $logout_url = wp_logout_url($redirect);

    return sprintf(
        '<a href="%s" class="%s">%s</a>',
        esc_url($logout_url),
        esc_attr($atts['class']),
        esc_html($atts['text'])
    );
}
add_shortcode('logout_link', 'headless_cms_logout_link_shortcode');


// ========================================
// User Menu Helper
// ========================================

/**
 * Get user menu data for templates
 * 
 * @return array User menu data
 */
function headless_cms_get_user_menu_data() {
    $login_page = headless_cms_get_login_page_url();
    
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $logout_redirect = $login_page ? add_query_arg('success', 'logged_out', $login_page) : home_url('/');

        return [
            'logged_in'     => true,
            'display_name'  => $current_user->display_name,
            'email'         => $current_user->user_email,
            'avatar_url'    => get_avatar_url($current_user->ID, ['size' => 64]),
            'dashboard_url' => admin_url(),
            'profile_url'   => admin_url('profile.php'),
            'logout_url'    => wp_logout_url($logout_redirect),
        ];
    }

    return [
        'logged_in'    => false,
        'login_url'    => $login_page ?: wp_login_url(),
        'register_url' => $login_page ? add_query_arg('mode', 'register', $login_page) : wp_registration_url(),
    ];
}
