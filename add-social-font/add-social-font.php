<?php
/**
 *
 * Plugin Name: Add Social Font
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin allows you to use Facebook, Instagram, Twitter, Youtube, Soundcloud and website icons.
 * Version: 0.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */

function hook_social_font_css () {
  ?>
  <link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__);?>css/social-font.css" />
  <?php
}

add_action('wp_head', 'hook_social_font_css');

?>
