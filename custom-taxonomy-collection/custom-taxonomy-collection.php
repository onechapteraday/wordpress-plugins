<?php
/**
 *
 * Plugin Name: Custom Taxonomy Collection
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin allows you to add a collection for your custom post type books.
 * Version: 0.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */

global $BOOK_TEXTDOMAIN;

$BOOK_TEXTDOMAIN = 'book-taxonomy';


/**
 * Add collection taxonomy
 **/

function add_collection_taxonomy() {
  global $BOOK_TEXTDOMAIN;

  $labels = array (
    'name'                       => _x( 'Collections', 'taxonomy general name', $BOOK_TEXTDOMAIN ),
    'singular_name'              => _x( 'Collection', 'taxonomy singular name', $BOOK_TEXTDOMAIN ),
    'search_items'               => __( 'Search Collections', $BOOK_TEXTDOMAIN ),
    'popular_items'              => __( 'Popular Collections', $BOOK_TEXTDOMAIN ),
    'all_items'                  => __( 'All Collections', $BOOK_TEXTDOMAIN ),
    'parent_item'                => null,
    'parent_item_colon'          => null,
    'view_item'                  => __( 'See Collection', $BOOK_TEXTDOMAIN ),
    'edit_item'                  => __( 'Edit Collection', $BOOK_TEXTDOMAIN ),
    'update_item'                => __( 'Update Collection', $BOOK_TEXTDOMAIN ),
    'add_new_item'               => __( 'Add New Collection', $BOOK_TEXTDOMAIN ),
    'new_item_name'              => __( 'New Collection Name', $BOOK_TEXTDOMAIN ),
    'separate_items_with_commas' => __( 'Separate collections with commas', $BOOK_TEXTDOMAIN ),
    'add_or_remove_items'        => __( 'Add or remove collections', $BOOK_TEXTDOMAIN ),
    'choose_from_most_used'      => __( 'Choose from the most used collections', $BOOK_TEXTDOMAIN ),
    'not_found'                  => __( 'No collections found.', $BOOK_TEXTDOMAIN ),
    'menu_name'                  => __( 'Collections', $BOOK_TEXTDOMAIN ),
  );

  $args = array (
    'hierarchical'          => false,
    'labels'                => $labels,
    'show_ui'               => true,
    'show_admin_column'     => true,
    'update_count_callback' => '_update_post_term_count',
    'query_var'             => true,
    'rewrite'               => array( 'slug' => 'book/collection', 'with_front' => 'true' ),
  );

  register_taxonomy ('collection', 'book', $args);
}

add_action ('init', 'add_collection_taxonomy', 1);


?>
