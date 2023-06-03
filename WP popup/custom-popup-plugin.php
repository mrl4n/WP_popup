<?php
/**
Plugin Name: Custom Popup Plugin
Plugin URI: https://github.com/mrl4n/WP_popup
Description: A customizable popup plugin for WordPress
Update URI: https://github.com/mrl4n/WP_popup
Author: mrl4n
Author URI: https://github.com/mrl4n/WP_popup
Text Domain: wp-custom-popup
Domain Path: /languages
License: GPLv2 or later
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
            <h3><?php echo esc_html(get_option('custom_popup_message', 'Welcome to our website!')); ?></h3>
            <p><?php echo esc_html(get_option('custom_popup_description', 'Enjoy your stay and check out our amazing offers.')); ?></p>

            <!-- Form -->
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="custom_popup_submit">
                <input type="hidden" name="custom_popup_nonce" value="<?php echo esc_attr($nonce); ?>">
                <input type="text" name="custom_popup_input" placeholder="<?php echo esc_attr(get_option('custom_popup_placeholder', 'Enter your input')); ?>">
                <button type="submit"><?php echo esc_html(get_option('custom_popup_button_text', 'Submit')); ?></button>
            </form>

            <button id="custom-popup-close"><?php echo esc_html(get_option('custom_popup_close_text', 'Close')); ?></button>
        </div>
    </div>
    <?php
}
add_action('wp_footer', 'custom_popup_markup');

// Initialize the pop-up functionality
function custom_popup_initialize() {
    if (should_display_custom_popup()) {
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
}
add_action('wp_footer', 'custom_popup_initialize');

// Add settings menu in the WordPress backend
function custom_popup_settings_menu() {
    add_options_page(
        __('Custom Pop-up Settings', 'custom-popup-plugin'),
        __('Custom Pop-up', 'custom-popup-plugin'),
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
        update_option('custom_popup_description', sanitize_text_field($_POST['custom_popup_description']));
        update_option('custom_popup_button_text', sanitize_text_field($_POST['custom_popup_button_text']));
        update_option('custom_popup_placeholder', sanitize_text_field($_POST['custom_popup_placeholder']));
        update_option('custom_popup_close_text', sanitize_text_field($_POST['custom_popup_close_text']));
        update_option('custom_popup_selected_pages', isset($_POST['custom_popup_selected_pages']) ? array_map('intval', $_POST['custom_popup_selected_pages']) : array());

        // Display a success message
        echo '<div class="notice notice-success"><p>' . __('Settings saved successfully.', 'custom-popup-plugin') . '</p></div>';
    }

    // Retrieve current settings
    $enabled = get_option('custom_popup_enabled');
    $message = get_option('custom_popup_message');
    $description = get_option('custom_popup_description');
    $button_text = get_option('custom_popup_button_text');
    $placeholder = get_option('custom_popup_placeholder');
    $close_text = get_option('custom_popup_close_text');
    $selected_pages = get_option('custom_popup_selected_pages', array());

    // Get all published pages
    $pages = get_pages();

    // Display the settings page form
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e('Enable Custom Pop-up', 'custom-popup-plugin'); ?></th>
                    <td>
                        <label><input type="checkbox" name="custom_popup_enabled" value="1" <?php checked($enabled, true); ?>><?php _e('Enable the custom pop-up', 'custom-popup-plugin'); ?></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Pop-up Message', 'custom-popup-plugin'); ?></th>
                    <td>
                        <input type="text" name="custom_popup_message" class="regular-text" value="<?php echo esc_attr($message); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Pop-up Description', 'custom-popup-plugin'); ?></th>
                    <td>
                        <textarea name="custom_popup_description" class="regular-text"><?php echo esc_textarea($description); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Button Text', 'custom-popup-plugin'); ?></th>
                    <td>
                        <input type="text" name="custom_popup_button_text" class="regular-text" value="<?php echo esc_attr($button_text); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Input Placeholder', 'custom-popup-plugin'); ?></th>
                    <td>
                        <input type="text" name="custom_popup_placeholder" class="regular-text" value="<?php echo esc_attr($placeholder); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Close Button Text', 'custom-popup-plugin'); ?></th>
                    <td>
                        <input type="text" name="custom_popup_close_text" class="regular-text" value="<?php echo esc_attr($close_text); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Display on Selected Pages', 'custom-popup-plugin'); ?></th>
                    <td>
                        <?php foreach ($pages as $page) : ?>
                            <label>
                                <input type="checkbox" name="custom_popup_selected_pages[]" value="<?php echo esc_attr($page->ID); ?>" <?php checked(in_array($page->ID, $selected_pages), true); ?>>
                                <?php echo esc_html($page->post_title); ?>
                            </label><br>
                        <?php endforeach; ?>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="custom_popup_settings_save" class="button-primary" value="<?php _e('Save Settings', 'custom-popup-plugin'); ?>">
            </p>
        </form>
    </div>
    <?php
}

// Helper function to check if the pop-up should be displayed on the current page
function should_display_custom_popup() {
    $enabled = get_option('custom_popup_enabled', false);
    $selected_pages = get_option('custom_popup_selected_pages', array());

    // Display the pop-up if it is enabled and the current page is in the selected pages list
    if ($enabled && is_page($selected_pages)) {
        return true;
    }

    return false;
}
