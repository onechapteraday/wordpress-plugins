<?php
/**
 *
 * Plugin Name: Remove Shortlink
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin removes the shortlink from header
 * Version: 0.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */


/**
 * Remove the shortlink from header
 *
 */

remove_action('wp_head', 'wp_shortlink_wp_head');

?>
