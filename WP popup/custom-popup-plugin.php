<?php
/**
 * Plugin Name: Custom Pop-up Plugin
 * Plugin URI: https://github.com/mrl4n
 * Description: This plugin adds a custom pop-up to your WordPress site.
 * Version: 1.0
 * Author: mrl4n
 * Author URI: https://github.com/mrl4n
 * License: GPLv2 or later
 * Text Domain: custom-popup-plugin
 */

// Enqueue necessary scripts and styles
function custom_popup_scripts() {
    // Enqueue jQuery if not already loaded
    if (!wp_script_is('jquery', 'enqueued')) {
        wp_enqueue_script('jquery');
    }

    // Enqueue plugin script
    wp_enqueue_script('custom-popup-script', plugins_url('js/custom-popup.js', __FILE__), array('jquery'), '1.0', true);

    // Enqueue plugin styles
    wp_enqueue_style('custom-popup-style', plugins_url('css/custom-popup.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'custom_popup_scripts');

// Add pop-up HTML markup to the footer
function custom_popup_markup() {
    // Generate a nonce
    $nonce = wp_create_nonce('custom_popup_nonce');
    ?>
    <div id="custom-popup" class="custom-popup">
        <div class="custom-popup-content">
            <img src="<?php echo plugins_url('images/logo.png', __FILE__); ?>" alt="Logo">
            <h3>Welcome to our website!</h3>
            <p>Enjoy your stay and check out our amazing offers.</p>

            <!-- Form -->
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="custom_popup_submit">
                <input type="hidden" name="custom_popup_nonce" value="<?php echo esc_attr($nonce); ?>">
                <input type="text" name="custom_popup_input" placeholder="Enter your input">
                <button type="submit">Submit</button>
            </form>

            <button id="custom-popup-close">Close</button>
        </div>
    </div>
    <?php
}
add_action('wp_footer', 'custom_popup_markup');

// Initialize the pop-up functionality
function custom_popup_initialize() {
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Show the pop-up
            $('#custom-popup').fadeIn();

            // Close the pop-up when the close button is clicked
            $('#custom-popup-close').on('click', function() {
                $('#custom-popup').fadeOut();
            });
        });
    </script>
    <?php
}
add_action('wp_footer', 'custom_popup_initialize');

// Add settings menu in the WordPress backend
function custom_popup_settings_menu() {
    add_options_page(
        'Custom Pop-up Settings',
        'Custom Pop-up',
        'manage_options',
        'custom-popup-settings',
        'custom_popup_settings_page'
    );
}
add_action('admin_menu', 'custom_popup_settings_menu');

// Create the settings page
function custom_popup_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Save settings
    if (isset($_POST['custom_popup_settings_save'])) {
        update_option('custom_popup_enabled', isset($_POST['custom_popup_enabled']) ? true : false);
        update_option('custom_popup_message', sanitize_text_field($_POST['custom_popup_message']));
        update_option('custom_popup_button_text', sanitize_text_field($_POST['custom_popup_button_text']));
    }

    // Retrieve current settings
    $enabled = get_option('custom_popup_enabled');
    $message = get_option('custom_popup_message');
    $button_text = get_option('custom_popup_button_text');
    
    // Display the settings page markup
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row">Enable Pop-up</th>
                    <td>
                        <label>
                            <input type="checkbox" name="custom_popup_enabled" value="1" <?php checked($enabled, true); ?>>
                            Enable the custom pop-up
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Pop-up Message</th>
                    <td>
                        <input type="text" name="custom_popup_message" value="<?php echo esc_attr($message); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Button Text</th>
                    <td>
                        <input type="text" name="custom_popup_button_text" value="<?php echo esc_attr($button_text); ?>" class="regular-text">
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <input type="submit" name="custom_popup_settings_save" class="button-primary" value="Save Changes">
            </p>
        </form>
    </div>
    <?php
}

    // Retrieve current settings
    $enabled = get_option('custom_popup_enabled');


