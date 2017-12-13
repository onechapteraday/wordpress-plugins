<?php
/**
 *
 * Plugin Name: Custom Taxonomy Publisher
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin allows you to add a publisher for your custom post type books.
 * Version: 0.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */

global $BOOK_TEXTDOMAIN;

$BOOK_TEXTDOMAIN = 'book-taxonomy';


/**
 * Add publisher taxonomy
 **/

function add_publisher_taxonomy() {
  global $BOOK_TEXTDOMAIN;

  $labels = array (
    'name'                       => _x( 'Publishers', 'taxonomy general name', $BOOK_TEXTDOMAIN ),
    'singular_name'              => _x( 'Publisher', 'taxonomy singular name', $BOOK_TEXTDOMAIN ),
    'search_items'               => __( 'Search Publishers', $BOOK_TEXTDOMAIN ),
    'popular_items'              => __( 'Popular Publishers', $BOOK_TEXTDOMAIN ),
    'all_items'                  => __( 'All Publishers', $BOOK_TEXTDOMAIN ),
    'parent_item'                => null,
    'parent_item_colon'          => null,
    'view_item'                  => __( 'See Publisher', $BOOK_TEXTDOMAIN ),
    'edit_item'                  => __( 'Edit Publisher', $BOOK_TEXTDOMAIN ),
    'update_item'                => __( 'Update Publisher', $BOOK_TEXTDOMAIN ),
    'add_new_item'               => __( 'Add New Publisher', $BOOK_TEXTDOMAIN ),
    'new_item_name'              => __( 'New Publisher Name', $BOOK_TEXTDOMAIN ),
    'separate_items_with_commas' => __( 'Separate publishers with commas', $BOOK_TEXTDOMAIN ),
    'add_or_remove_items'        => __( 'Add or remove publishers', $BOOK_TEXTDOMAIN ),
    'choose_from_most_used'      => __( 'Choose from the most used publishers', $BOOK_TEXTDOMAIN ),
    'not_found'                  => __( 'No publishers found.', $BOOK_TEXTDOMAIN ),
    'menu_name'                  => __( 'Publishers', $BOOK_TEXTDOMAIN ),
  );

  $args = array (
    'hierarchical'          => false,
    'labels'                => $labels,
    'show_ui'               => true,
    'show_admin_column'     => true,
    'update_count_callback' => '_update_post_term_count',
    'query_var'             => true,
    'rewrite'               => array( 'slug' => 'book/publisher', 'with_front' => 'true' ),
  );

  register_taxonomy ('publisher', 'book', $args);
}

add_action ('init', 'add_publisher_taxonomy', 1);


?>
