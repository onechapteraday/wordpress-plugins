<?php
/**
 *
 * Plugin Name: Remove Feed links
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin removes feed links
 * Version: 1.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */


/**
 * Remove feed links
 *
 */

remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'feed_links_extra', 3 );

?>
