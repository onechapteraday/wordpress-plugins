<?php
/**
 *
 * Plugin Name: Remove Really Simple Discovery link
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin removes the Really Simple Discovery link
 * Version: 0.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */


/**
 * Remove the Really Simple Discovery link
 *
 */

remove_action('wp_head', 'rsd_link');

?>
