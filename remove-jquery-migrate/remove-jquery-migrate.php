<?php
/**
 *
 * Plugin Name: Remove jQuery migrate
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin removes jQuery migrate implementation
 * Version: 1.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */


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
