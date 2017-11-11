<?php
/**
 *
 * Plugin Name: Remove Windows Live Writer link
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin removes the Windows Live Writer link
 * Version: 0.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */


/**
 * Remove Windows Live Writer link
 *
 */

remove_action('wp_head', 'wlwmanifest_link');

?>
