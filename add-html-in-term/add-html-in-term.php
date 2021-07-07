<?php
/**
 *
 * Plugin Name: Add HTML in term
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin allows HTML tags in term
 * Version: 0.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */

/**
 * Allow HTML in term descriptions
 */

remove_filter( 'pre_term_description', 'wp_filter_kses' );
remove_filter( 'term_description', 'wp_filter_kses' );

?>
