<?php
/**
 *
 * Plugin Name: Post Dictionary
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin gives you the possibility to add a glossary to your posts.
 * Version: 0.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */


/**
 * Add dictionary content at the end of posts
 *
 */
function post_dictionary_add_content($content) {
    if (is_single()) {
        $content .= '<p>The new dictionary will be here!</p>';
    }

    return $content;
}

add_action('the_content', 'post_dictionary_add_content');


?>
