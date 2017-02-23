<?php
/**
 *
 * Plugin Name: Remove Emojis
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin removes emojis provided in WP 4.2
 * Version: 0.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */


# Remove emojis introduced in WordPress 4.2

remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );


?>
