<?php

/*
Plugin Name: Incluyeme Applications
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: Shortcode [incluyeme_applications]
Version: 1.0.6
Author: Jesus Nuñez
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/
defined('ABSPATH') or exit;
require_once plugin_dir_path(__FILE__) . 'include/active_incluyeme_applications.php';
require_once plugin_dir_path(__FILE__) . 'include/incluyeme_applications_assets.php';
add_action('admin_init', 'incluyeme_applications_extension');
add_action('wp', 'incluyeme_applications_assets');
active_incluyeme_applications();
function incluyeme_applications_extension()
{
    if (is_admin() && current_user_can('activate_plugins') && !is_plugin_active('wpjobboard/index.php')) {
        add_action('admin_notices', 'incluyeme_applications_notice');
        deactivate_plugins(plugin_basename(__FILE__));
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }
    }
}

function incluyeme_applications_notice()
{
    ?>
	<div class="error"><p> <?php echo __('Sorry, but Incluyeme plugin requires the WPJob Board plugin to be installed and
	                      active.', 'incluyeme'); ?> </p></div>
    <?php
}

require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://github.com/Incluyeme-com/incluyeme-applications',
    __FILE__,
    'incluyeme-login-applications'
);
