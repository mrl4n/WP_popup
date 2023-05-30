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
add_action('wp_footer', 'custom_popup_initialize');
