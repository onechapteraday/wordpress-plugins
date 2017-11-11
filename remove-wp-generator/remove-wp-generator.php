<?php
/**
 *
 * Plugin Name: Remove WP version
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin removes WordPress version into header
 * Version: 0.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */


/**
 * Remove the WordPress version
 *
 */

remove_action('wp_head', 'wp_generator');

?>
