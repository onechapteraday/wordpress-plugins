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
        'publicize',
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


/*
 * Add custom post type book to dashboard widget activity
 *
 */

function add_custom_post_type_book_to_dashboard_activity( $query_args ) {
	if ( is_array( $query_args[ 'post_type' ] ) ) {
		//Set yout post type
		$query_args[ 'post_type' ][] = 'book';
	} else {
		$temp = array( $query_args[ 'post_type' ], 'book' );
		$query_args[ 'post_type' ] = $temp;
	}
	return $query_args;
}

add_filter( 'dashboard_recent_posts_query_args', 'add_custom_post_type_book_to_dashboard_activity' );


/*
 * Add book metadata functions
 *
 */

function get_book_cover ( $post_id ) {
  return get_post_meta($post_id, 'cover', true);
}

function get_book_excerpt ( $post_id ) {
  return get_post_meta($post_id, 'excerpt', true);
}

function get_book_title_french ( $post_id ) {
  return get_post_meta($post_id, 'title_french', true);
}

function get_book_title_original ( $post_id ) {
  return get_post_meta($post_id, 'title_original', true);
}

function get_book_pages_number ( $post_id ) {
  return get_post_meta($post_id, 'pages_number', true);
}

function get_book_isbn ( $post_id ) {
  return get_post_meta($post_id, 'isbn13', true);
}

function get_book_price ( $post_id ) {
  return get_post_meta($post_id, 'price', true);
}

function get_book_date_read ( $post_id ) {
  return get_post_meta($post_id, 'date_release', true);
}

function get_book_date_release ( $post_id ) {
  return get_post_meta($post_id, 'date_release', true);
}

function get_book_date_first_publication ( $post_id ) {
  return get_post_meta($post_id, 'date_first_publication', true);
}

function get_book_rating ( $post_id ) {
  return get_post_meta($post_id, 'rating', true);
}

function get_book_amazon ( $post_id ) {
  $arr = array(
    'link' => get_post_meta( $post_id, 'amazon', true ),
    'img' => plugin_dir_url( __FILE__ ) . 'images/logo_amazon.png'
  );

  return $arr;
}

function get_book_fnac ( $post_id ) {
  $arr = array(
    'link' => get_post_meta( $post_id, 'fnac', true ),
    'img' => plugin_dir_url( __FILE__ ) . 'images/logo_fnac.png'
  );

  return $arr;
}

function get_book_author ( $post_id ) {
  $person = get_post_meta($post_id, 'author', true);
  return get_term_by( 'slug', $person, 'person' );
}

function get_book_translator ( $post_id ) {
  $person = get_post_meta($post_id, 'translator', true);
  return get_term_by( 'slug', $person, 'person' );
}

function get_book_illustrator ( $post_id ) {
  $person = get_post_meta($post_id, 'illustrator', true);
  return get_term_by( 'slug', $person, 'person' );
}

function get_book_publisher ( $post_id ) {
  $publisher = get_the_terms($post_id, 'publisher');
  return $publisher;
}

function get_book_collection ( $post_id ) {
  $collection = get_post_meta($post_id, 'collection', true);
  return get_term_by( 'slug', $collection, 'collection' );
}

?>
