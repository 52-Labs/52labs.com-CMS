<?php
/**
 * Template Name: Login / Register
 * 
 * Custom login and registration page template with domain restriction
 */

// If user is already logged in, redirect to home or requested page
if (is_user_logged_in()) {
    $redirect = isset($_GET['redirect_to']) ? esc_url_raw($_GET['redirect_to']) : home_url('/');
    wp_safe_redirect($redirect);
    exit;
}

// Get site name and custom logo
$site_name = get_bloginfo('name');
$site_logo = '';

// Try to get custom logo from Site Settings ACF options page
if (function_exists('get_field')) {
    $logo_field = get_field('site_logo', 'option');
    if ($logo_field && is_array($logo_field) && isset($logo_field['url'])) {
        $site_logo = $logo_field['url'];
    } elseif ($logo_field && is_string($logo_field)) {
        $site_logo = $logo_field;
    }
}

// Fallback to WordPress custom logo
if (empty($site_logo) && has_custom_logo()) {
    $custom_logo_id = get_theme_mod('custom_logo');
    $site_logo = wp_get_attachment_image_url($custom_logo_id, 'medium');
}

// Determine current mode (login or register)
$mode = isset($_GET['mode']) && $_GET['mode'] === 'register' ? 'register' : 'login';

// Get messages and errors from query params
$error_message = '';
$success_message = '';
$info_message = '';

if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'invalid_credentials':
            $error_message = 'Invalid email or password. Please try again.';
            break;
        case 'empty_fields':
            $error_message = 'Please fill in all required fields.';
            break;
        case 'invalid_email':
            $error_message = 'Please enter a valid email address.';
            break;
        case 'email_exists':
            $error_message = 'An account with this email already exists.';
            break;
        case 'domain_not_allowed':
            $error_message = 'Registration is restricted to approved email domains only.';
            break;
        case 'password_mismatch':
            $error_message = 'Passwords do not match.';
            break;
        case 'weak_password':
            $error_message = 'Password must be at least 8 characters long.';
            break;
        case 'registration_failed':
            $error_message = 'Registration failed. Please try again.';
            break;
        case 'registration_disabled':
            $error_message = 'New user registration is currently disabled.';
            break;
        default:
            $error_message = 'An error occurred. Please try again.';
    }
}

if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'registered':
            $success_message = 'Account created successfully! Please log in.';
            $mode = 'login';
            break;
        case 'logged_out':
            $info_message = 'You have been logged out successfully.';
            break;
        case 'password_reset':
            $success_message = 'Password reset email sent. Please check your inbox.';
            break;
    }
}

// Get allowed domains for display (if registration is enabled)
$allowed_domains = [];
if (function_exists('get_field')) {
    $domains_repeater = get_field('allowed_domains', 'option');
    if ($domains_repeater && is_array($domains_repeater)) {
        foreach ($domains_repeater as $row) {
            if (isset($row['domain']) && !empty($row['domain'])) {
                $allowed_domains[] = strtolower(trim($row['domain']));
            }
        }
    }
}

// Check if registration is enabled
$registration_enabled = get_option('users_can_register');

// Get redirect URL
$redirect_to = isset($_GET['redirect_to']) ? esc_url($_GET['redirect_to']) : home_url('/');

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $mode === 'register' ? 'Create Account' : 'Sign In'; ?> - <?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/login.css?v=<?php echo filemtime(get_template_directory() . '/assets/css/login.css'); ?>">
</head>
<body class="auth-page">

<div class="auth-wrapper">
    <div class="auth-container">
        <div class="auth-card">
            <!-- Header -->
            <div class="auth-header">
                <div class="auth-logo">
                    <div class="auth-logo-icon">
                        <?php if ($site_logo) : ?>
                            <img src="<?php echo esc_url($site_logo); ?>" alt="<?php echo esc_attr($site_name); ?>">
                        <?php else : ?>
                            <span><?php echo esc_html(mb_substr($site_name, 0, 2)); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <h1 class="auth-title">
                    <?php echo $mode === 'register' ? 'Create an account' : 'Welcome back'; ?>
                </h1>
                <p class="auth-subtitle">
                    <?php echo $mode === 'register' 
                        ? 'Join ' . esc_html($site_name) . ' to access all features' 
                        : 'Sign in to access your account'; 
                    ?>
                </p>
            </div>

            <!-- Body -->
            <div class="auth-body">
                <?php if ($error_message) : ?>
                    <div class="auth-alert auth-alert-error">
                        <svg class="auth-alert-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="auth-alert-content"><?php echo esc_html($error_message); ?></div>
                    </div>
                <?php endif; ?>

                <?php if ($success_message) : ?>
                    <div class="auth-alert auth-alert-success">
                        <svg class="auth-alert-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="auth-alert-content"><?php echo esc_html($success_message); ?></div>
                    </div>
                <?php endif; ?>

                <?php if ($info_message) : ?>
                    <div class="auth-alert auth-alert-info">
                        <svg class="auth-alert-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="auth-alert-content"><?php echo esc_html($info_message); ?></div>
                    </div>
                <?php endif; ?>

                <?php if ($mode === 'login') : ?>
                    <!-- Login Form -->
                    <form class="auth-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
                        <input type="hidden" name="action" value="headless_login">
                        <input type="hidden" name="redirect_to" value="<?php echo esc_attr($redirect_to); ?>">
                        <?php wp_nonce_field('headless_login_nonce', 'login_nonce'); ?>

                        <div class="auth-field">
                            <label class="auth-label" for="login_email">Email address</label>
                            <div class="auth-input-wrapper">
                                <svg class="auth-input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <input 
                                    type="email" 
                                    id="login_email" 
                                    name="email" 
                                    class="auth-input has-icon" 
                                    placeholder="Enter your email"
                                    autocomplete="email"
                                    required
                                >
                            </div>
                        </div>

                        <div class="auth-field">
                            <label class="auth-label" for="login_password">Password</label>
                            <div class="auth-input-wrapper">
                                <svg class="auth-input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <input 
                                    type="password" 
                                    id="login_password" 
                                    name="password" 
                                    class="auth-input has-icon" 
                                    placeholder="Enter your password"
                                    autocomplete="current-password"
                                    required
                                >
                                <button type="button" class="auth-password-toggle" onclick="togglePassword('login_password', this)">
                                    <svg class="eye-open" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg class="eye-closed" style="display:none;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="auth-options">
                            <label class="auth-checkbox">
                                <input type="checkbox" name="remember" value="1">
                                <span class="auth-checkbox-custom"></span>
                                <span class="auth-checkbox-label">Remember me</span>
                            </label>
                            <a href="<?php echo esc_url(wp_lostpassword_url()); ?>" class="auth-forgot-link">Forgot password?</a>
                        </div>

                        <button type="submit" class="auth-submit">
                            <span class="auth-submit-text">Sign In</span>
                            <svg class="auth-submit-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </button>
                    </form>

                <?php else : ?>
                    <!-- Registration Form -->
                    <?php if (!$registration_enabled) : ?>
                        <div class="auth-alert auth-alert-warning">
                            <svg class="auth-alert-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <div class="auth-alert-content">
                                <strong>Registration Disabled</strong>
                                New user registration is currently not available. Please contact an administrator.
                            </div>
                        </div>
                    <?php else : ?>

                        <?php if (!empty($allowed_domains)) : ?>
                            <div class="auth-domain-info">
                                <svg class="auth-domain-info-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div class="auth-domain-info-content">
                                    <p class="auth-domain-info-title">Restricted Registration</p>
                                    <p class="auth-domain-info-text">Registration is limited to the following email domains:</p>
                                    <div class="auth-domain-list">
                                        <?php foreach ($allowed_domains as $domain) : ?>
                                            <span class="auth-domain-tag">@<?php echo esc_html($domain); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <form class="auth-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
                            <input type="hidden" name="action" value="headless_register">
                            <input type="hidden" name="redirect_to" value="<?php echo esc_attr($redirect_to); ?>">
                            <?php wp_nonce_field('headless_register_nonce', 'register_nonce'); ?>

                            <div class="auth-field">
                                <label class="auth-label" for="register_name">Full name</label>
                                <div class="auth-input-wrapper">
                                    <svg class="auth-input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <input 
                                        type="text" 
                                        id="register_name" 
                                        name="name" 
                                        class="auth-input has-icon" 
                                        placeholder="Enter your full name"
                                        autocomplete="name"
                                        required
                                    >
                                </div>
                            </div>

                            <div class="auth-field">
                                <label class="auth-label" for="register_email">Email address</label>
                                <div class="auth-input-wrapper">
                                    <svg class="auth-input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <input 
                                        type="email" 
                                        id="register_email" 
                                        name="email" 
                                        class="auth-input has-icon" 
                                        placeholder="Enter your email"
                                        autocomplete="email"
                                        required
                                    >
                                </div>
                            </div>

                            <div class="auth-field">
                                <label class="auth-label" for="register_password">Password</label>
                                <div class="auth-input-wrapper">
                                    <svg class="auth-input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    <input 
                                        type="password" 
                                        id="register_password" 
                                        name="password" 
                                        class="auth-input has-icon" 
                                        placeholder="Create a password"
                                        autocomplete="new-password"
                                        minlength="8"
                                        required
                                    >
                                    <button type="button" class="auth-password-toggle" onclick="togglePassword('register_password', this)">
                                        <svg class="eye-open" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <svg class="eye-closed" style="display:none;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="auth-field">
                                <label class="auth-label" for="register_password_confirm">Confirm password</label>
                                <div class="auth-input-wrapper">
                                    <svg class="auth-input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    <input 
                                        type="password" 
                                        id="register_password_confirm" 
                                        name="password_confirm" 
                                        class="auth-input has-icon" 
                                        placeholder="Confirm your password"
                                        autocomplete="new-password"
                                        required
                                    >
                                </div>
                            </div>

                            <div class="auth-terms">
                                By creating an account, you agree to our 
                                <a href="<?php echo esc_url(home_url('/terms')); ?>">Terms of Service</a> and 
                                <a href="<?php echo esc_url(home_url('/privacy')); ?>">Privacy Policy</a>.
                            </div>

                            <button type="submit" class="auth-submit">
                                <span class="auth-submit-text">Create Account</span>
                                <svg class="auth-submit-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                            </button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- Footer -->
            <div class="auth-footer">
                <?php if ($mode === 'login') : ?>
                    <?php if ($registration_enabled) : ?>
                        <p class="auth-footer-text">
                            Don't have an account? 
                            <a href="<?php echo esc_url(add_query_arg('mode', 'register', get_permalink())); ?>" class="auth-footer-link">Create one</a>
                        </p>
                    <?php else : ?>
                        <p class="auth-footer-text">
                            <a href="<?php echo esc_url(home_url('/')); ?>" class="auth-footer-link">← Back to site</a>
                        </p>
                    <?php endif; ?>
                <?php else : ?>
                    <p class="auth-footer-text">
                        Already have an account? 
                        <a href="<?php echo esc_url(remove_query_arg('mode', get_permalink())); ?>" class="auth-footer-link">Sign in</a>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Branding -->
        <div class="auth-branding">
            <p class="auth-branding-text">
                <span class="auth-branding-logo">
                    <span><?php echo esc_html(mb_substr($site_name, 0, 2)); ?></span>
                </span>
                <?php echo esc_html($site_name); ?>
            </p>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId, button) {
    const input = document.getElementById(inputId);
    const eyeOpen = button.querySelector('.eye-open');
    const eyeClosed = button.querySelector('.eye-closed');
    
    if (input.type === 'password') {
        input.type = 'text';
        eyeOpen.style.display = 'none';
        eyeClosed.style.display = 'block';
    } else {
        input.type = 'password';
        eyeOpen.style.display = 'block';
        eyeClosed.style.display = 'none';
    }
}

// Add loading state to form submission
document.querySelectorAll('.auth-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const button = this.querySelector('.auth-submit');
        button.classList.add('is-loading');
    });
});
</script>

<?php wp_footer(); ?>
</body>
</html>
