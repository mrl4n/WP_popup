<?php
/**
 * Plugin Name: Custom Popup Plugin
 * Plugin URI: https://github.com/mrl4n/WP_popup
 * Description: A customizable popup plugin for WordPress
 * Version: 1.0
 * Author: mrl4n
 * Author URI: https://github.com/mrl4n/WP_popup
 * Text Domain: wp-custom-popup
 * Domain Path: /languages
 * License: GPLv2 or later
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
            <div style="margin-top: 20px;">
            <h3><?php echo esc_html(get_option('custom_popup_message', 'Welcome to our website!')); ?></h3></div>
            <div style="margin-top: 40px;">
            <p><?php echo esc_html(get_option('custom_popup_description', 'Enjoy your stay and check out our amazing offers.')); ?></p></div>

            <!-- Form -->
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <div class="custom-popup-form-row">
					<input type="hidden" name="action" value="custom_popup_submit">
                    <input type="hidden" name="custom_popup_nonce" value="<?php echo esc_attr($nonce); ?>">
                </div>
                <div class="custom-popup-form-row">
                    <input type="text" name="custom_popup_input" placeholder="<?php echo esc_attr(get_option('custom_popup_placeholder', 'Enter your input')); ?>">
                </div>
                <div style="margin-top: 40px;">
                <div class="custom-popup-form-row">
                    <button type="submit"><?php echo esc_html(get_option('custom_popup_button_text', 'Submit')); ?></button></div>
                </div>
            </form>
			<div style="margin-top: 40px;">
            <button id="custom-popup-close"><?php echo esc_html(get_option('custom_popup_close_text', 'Close')); ?></button></div>
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

                // Submit the form via Ajax
                $('#custom-popup form').on('submit', function(e) {
                    e.preventDefault();

                    var $form = $(this);
                    var formData = $form.serialize();

                    // Send the form data via Ajax
                    $.ajax({
                        type: 'POST',
                        url: $form.attr('action'),
                        data: formData,
                        success: function(response) {
                            // Process the response
                            if (response.success) {
                                // Display success message
                                alert('Form submitted successfully.');

                                // Hide the popup after form submission
                                $('#custom-popup').fadeOut();
                            } else {
                                // Display error message
                                alert('Error submitting the form. Please try again.');
                            }
                        },
                        error: function() {
                            // Display error message
                            alert('Error submitting the form. Please try again.');
                        }
                    });
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

// Display the custom pop-up settings page
function custom_popup_settings_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'custom-popup-plugin'));
    }

    if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
        echo '<div class="notice notice-success"><p>' . __('Settings updated successfully.', 'custom-popup-plugin') . '</p></div>';
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('Custom Pop-up Settings', 'custom-popup-plugin'); ?></h1>

        <form method="post" action="<?php echo esc_url(admin_url('options.php')); ?>">
            <?php settings_fields('custom_popup_settings_group'); ?>
            <?php do_settings_sections('custom-popup-settings'); ?>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Register and sanitize settings
function custom_popup_register_settings() {
    register_setting('custom_popup_settings_group', 'custom_popup_enabled');
    register_setting('custom_popup_settings_group', 'custom_popup_selected_pages');
    register_setting('custom_popup_settings_group', 'custom_popup_message');
    register_setting('custom_popup_settings_group', 'custom_popup_description');
    register_setting('custom_popup_settings_group', 'custom_popup_placeholder');
    register_setting('custom_popup_settings_group', 'custom_popup_button_text');
    register_setting('custom_popup_settings_group', 'custom_popup_close_text');
}
add_action('admin_init', 'custom_popup_register_settings');

// Add sections and fields to the settings page
function custom_popup_settings_sections() {
    add_settings_section('custom_popup_general_section', __('General Settings', 'custom-popup-plugin'), 'custom_popup_general_section_callback', 'custom-popup-settings');

    add_settings_field('custom_popup_enabled', __('Enable Popup', 'custom-popup-plugin'), 'custom_popup_enabled_field_callback', 'custom-popup-settings', 'custom_popup_general_section');
    add_settings_field('custom_popup_selected_pages', __('Select Pages', 'custom-popup-plugin'), 'custom_popup_selected_pages_field_callback', 'custom-popup-settings', 'custom_popup_general_section');
    add_settings_field('custom_popup_message', __('Popup Message', 'custom-popup-plugin'), 'custom_popup_message_field_callback', 'custom-popup-settings', 'custom_popup_general_section');
    add_settings_field('custom_popup_description', __('Popup Description', 'custom-popup-plugin'), 'custom_popup_description_field_callback', 'custom-popup-settings', 'custom_popup_general_section');
    add_settings_field('custom_popup_placeholder', __('Placeholder Text', 'custom-popup-plugin'), 'custom_popup_placeholder_field_callback', 'custom-popup-settings', 'custom_popup_general_section');
    add_settings_field('custom_popup_button_text', __('Button Text', 'custom-popup-plugin'), 'custom_popup_button_text_field_callback', 'custom-popup-settings', 'custom_popup_general_section');
    add_settings_field('custom_popup_close_text', __('Close Button Text', 'custom-popup-plugin'), 'custom_popup_close_text_field_callback', 'custom-popup-settings', 'custom_popup_general_section');
}
add_action('admin_init', 'custom_popup_settings_sections');

// Section callbacks
function custom_popup_general_section_callback() {
    echo '<p>' . __('General settings for the custom pop-up plugin.', 'custom-popup-plugin') . '</p>';
}

// Field callbacks
function custom_popup_enabled_field_callback() {
    $enabled = get_option('custom_popup_enabled', '1');
    echo '<input type="checkbox" name="custom_popup_enabled" value="1" ' . checked($enabled, '1', false) . '>';
}

function custom_popup_selected_pages_field_callback() {
    $selected_pages = get_option('custom_popup_selected_pages', array());
    $pages = get_pages();

    echo '<select name="custom_popup_selected_pages[]" multiple>';
    foreach ($pages as $page) {
        echo '<option value="' . esc_attr($page->ID) . '" ' . selected(in_array($page->ID, $selected_pages), true, false) . '>' . esc_html($page->post_title) . '</option>';
    }
    echo '</select>';
}

function custom_popup_message_field_callback() {
    $message = get_option('custom_popup_message', 'Welcome to our website!');
    echo '<input type="text" name="custom_popup_message" value="' . esc_attr($message) . '">';
}

function custom_popup_description_field_callback() {
    $description = get_option('custom_popup_description', 'Enjoy your stay and check out our amazing offers.');
    echo '<textarea name="custom_popup_description">' . esc_textarea($description) . '</textarea>';
}

function custom_popup_placeholder_field_callback() {
    $placeholder = get_option('custom_popup_placeholder', 'Enter your input');
    echo '<input type="text" name="custom_popup_placeholder" value="' . esc_attr($placeholder) . '">';
}

function custom_popup_button_text_field_callback() {
    $button_text = get_option('custom_popup_button_text', 'Submit');
    echo '<input type="text" name="custom_popup_button_text" value="' . esc_attr($button_text) . '">';
}

function custom_popup_close_text_field_callback() {
    $close_text = get_option('custom_popup_close_text', 'Close');
    echo '<input type="text" name="custom_popup_close_text" value="' . esc_attr($close_text) . '">';
}

// Custom Popup Form Submission Handler
function custom_popup_submit_handler() {
    // Verify the nonce
    if (!isset($_POST['custom_popup_nonce']) || !wp_verify_nonce($_POST['custom_popup_nonce'], 'custom_popup_nonce')) {
        wp_die('Invalid nonce.');
    }

    // Process the form data
    // Here you can add your custom logic to handle the submitted form data
    // For demonstration purposes, we'll just send an email with the submitted input

    $input = isset($_POST['custom_popup_input']) ? sanitize_text_field($_POST['custom_popup_input']) : '';

    // Prepare the email
    $to = get_option('admin_email');
    $subject = 'Custom Popup Form Submission';
    $message = 'The form was submitted with the following input: ' . $input;
    $headers = array('Content-Type: text/html; charset=UTF-8');

    // Send the email
    $sent = wp_mail($to, $subject, $message, $headers);

    // Return a response
    if ($sent) {
        $response = array('success' => true);
    } else {
        $response = array('success' => false);
    }

    wp_send_json($response);
}
add_action('admin_post_nopriv_custom_popup_submit', 'custom_popup_submit_handler');
add_action('admin_post_custom_popup_submit', 'custom_popup_submit_handler');

// Helper function to check if the custom pop-up should be displayed
function should_display_custom_popup() {
    $enabled = get_option('custom_popup_enabled', '1');
    $selected_pages = get_option('custom_popup_selected_pages', array());

    if ($enabled && is_page($selected_pages)) {
        return true;
    }

    return false;
}
