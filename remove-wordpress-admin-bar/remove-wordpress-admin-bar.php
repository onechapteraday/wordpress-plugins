<?php
/**
 *
 * Plugin Name: Remove WP Admin Bar
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin removes all features (files and inline styles) of the WP Admin Bar
 * Version: 0.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */


# Remove all features of the WP Admin Bar

add_filter('show_admin_bar', false);


?>
