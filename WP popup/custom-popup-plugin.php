<?php
/*
Plugin Name: Custom Pop-up Plugin
Plugin URI: https://github.com/mrl4n
Description: This plugin adds a custom pop-up to your WordPress site.
Version: 1.0
Author: Your Name
Author URI: https://github.com/mrl4n
License: GPLv2 or later
Text Domain: custom-popup-plugin
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
    ?>
    <div id="custom-popup" class="custom-popup">
        <div class="custom-popup-content">
            <h3>Welcome to our website!</h3>
            <p>Enjoy your stay and check out our amazing offers.</p>
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
// Sanitize and validate user input
function custom_popup_sanitize_input($input) {
    // Sanitize text fields
    $sanitized_input = sanitize_text_field($input);
    
    // Validate and sanitize numeric fields
    $numeric_input = intval($input);
    
    // Return the sanitized and validated input
    return $sanitized_input;
}

// Process user input
function custom_popup_process_input() {
    // Check if the form is submitted
    if (isset($_POST['custom_popup_input'])) {
        // Get the user input
        $user_input = $_POST['custom_popup_input'];
        
        // Sanitize and validate the user input
        $sanitized_input = custom_popup_sanitize_input($user_input);
        
        // Do further processing with the sanitized input
        // ...
    }
}

// Add action hook for processing input
add_action('init', 'custom_popup_process_input');
add_action('wp_footer', 'custom_popup_initialize');
