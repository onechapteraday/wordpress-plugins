<?php
/**
 *
 * Plugin Name: Remove Useless Stuff
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin removes emojis and jQuery migrate
 * Version: 0.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */


# Remove emojis introduced in WordPress 4.2

remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );


/**
 * Remove jQuery migrate provided by Jetpack
 *
 * @param object $scripts
 *
 */

function remove_jquery_migrate($scripts) {
  if (!is_admin()) {
    $scripts->remove('jquery');
    $scripts->add('jquery', false, array('jquery-core'), '1.10.2');
  }
}

add_filter('wp_default_scripts', 'remove_jquery_migrate');

?>
