<?php
/**
 *
 * Plugin Name: Custom Post Type Book
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin creates a new post type called books.
 * Version: 0.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */

global $BOOK_TEXTDOMAIN;

$BOOK_TEXTDOMAIN = 'book-taxonomy';


/*
 * Make plugin available for translation.
 * Translations can be filed in the /languages directory.
 *
 */

function book_taxonomy_load_textdomain() {
  global $BOOK_TEXTDOMAIN;
  $locale = apply_filters( 'plugin_locale', get_locale(), $BOOK_TEXTDOMAIN );

  # Load i18n
  $path = basename( dirname( __FILE__ ) ) . '/languages/';
  $loaded = load_plugin_textdomain( $BOOK_TEXTDOMAIN, false, $path );
}

add_action( 'init', 'book_taxonomy_load_textdomain', 0 );


/**
 * Add new custom post type
 *
 */

function create_post_type_book() {
  global $BOOK_TEXTDOMAIN;

  register_post_type( 'book',
    array(
      'labels' => array(
        'name' => __( 'Books', $BOOK_TEXTDOMAIN ),
        'singular_name' => __( 'Book', $BOOK_TEXTDOMAIN )
      ),
      'public' => true,
      'has_archive' => true,
      'menu_icon' => 'dashicons-book',
      'menu_position' => 5,
      'taxonomies' => array(
        'category',
        'person',
        'post_tag',
      ),
      'supports' => array(
        'title',
        'editor',
        'excerpt',
        'custom-fields',
        'comments',
        'thumbnail',
      ),
    )
  );
}

add_action( 'init', 'create_post_type_book' );


/*
 * Add book post in archives
 *
 **/

function include_book_filter( $query ) {
    if ( ($query->is_home() && $query->is_main_query()) || $query->is_feed() ) {
        $query->set( 'post_type', array( 'post', 'book' ) );
    }

    return $query;
}

add_action( 'pre_get_posts', 'include_book_filter' );


/*
 * Add custom taxonomies only for books
 *
 */

function add_genre_taxonomy() {
  global $BOOK_TEXTDOMAIN;

  $labels = array (
    'name'                       => _x( 'Genres', 'taxonomy general name', $BOOK_TEXTDOMAIN ),
    'singular_name'              => _x( 'Genre', 'taxonomy singular name', $BOOK_TEXTDOMAIN ),
    'search_items'               => __( 'Search Genres', $BOOK_TEXTDOMAIN ),
    'popular_items'              => __( 'Popular Genres', $BOOK_TEXTDOMAIN ),
    'all_items'                  => __( 'All Genres', $BOOK_TEXTDOMAIN ),
    'parent_item'                => null,
    'parent_item_colon'          => null,
    'view_item'                  => __( 'See Genre', $BOOK_TEXTDOMAIN ),
    'edit_item'                  => __( 'Edit Genre', $BOOK_TEXTDOMAIN ),
    'update_item'                => __( 'Update Genre', $BOOK_TEXTDOMAIN ),
    'add_new_item'               => __( 'Add New Genre', $BOOK_TEXTDOMAIN ),
    'new_item_name'              => __( 'New Genre Name', $BOOK_TEXTDOMAIN ),
    'separate_items_with_commas' => __( 'Separate genres with commas', $BOOK_TEXTDOMAIN ),
    'add_or_remove_items'        => __( 'Add or remove genres', $BOOK_TEXTDOMAIN ),
    'choose_from_most_used'      => __( 'Choose from the most used genres', $BOOK_TEXTDOMAIN ),
    'not_found'                  => __( 'No genres found.', $BOOK_TEXTDOMAIN ),
    'menu_name'                  => __( 'Genres', $BOOK_TEXTDOMAIN ),
  );

  $args = array (
    'hierarchical'          => false,
    'labels'                => $labels,
    'show_ui'               => true,
    'show_admin_column'     => true,
    'update_count_callback' => '_update_post_term_count',
    'query_var'             => true,
    'rewrite'               => array( 'slug' => 'book/genre', 'with_front' => 'true' ),
  );

  register_taxonomy ('genre', 'book', $args);
}

add_action ('init', 'add_genre_taxonomy', 1);


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
