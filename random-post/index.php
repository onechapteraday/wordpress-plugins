<?php
/**
 *
 * Plugin Name: Random Post
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin provides an url where a random post is displayed
 * Version: 0.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */


/**
 * Provides an URI /random-post where a random post is displayed
 *
 */

function get_random_post() {
  # Should work on dev platforms too
  $base_url = parse_url(site_url(), PHP_URL_PATH);
  $url = $base_url . '/random-post';

  if ($url == $_SERVER['REQUEST_URI'] || $url . '/' == $_SERVER['REQUEST_URI']) {
    # WP query to get a random post url
    foreach (get_posts(array('numberposts' => 1, 'orderby' => 'rand')) as $post) {
      wp_redirect (get_permalink($post->ID), 302);
      exit; # It should never get here but just in case
    }
  }
}

add_action ('template_redirect', 'get_random_post');

?>
