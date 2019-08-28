<?php
/**
 *
 * Plugin Name: Custom Post Type Album
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin creates a new post type called albums.
 * Version: 0.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */

global $ALBUM_TEXTDOMAIN;

$ALBUM_TEXTDOMAIN = 'album-post-type';


/*
 * Make plugin available for translation.
 * Translations can be filed in the /languages directory.
 *
 */

function album_taxonomy_load_textdomain() {
  global $ALBUM_TEXTDOMAIN;
  $locale = apply_filters( 'plugin_locale', get_locale(), $ALBUM_TEXTDOMAIN );

  # Load i18n
  $path = basename( dirname( __FILE__ ) ) . '/languages/';
  $loaded = load_plugin_textdomain( $ALBUM_TEXTDOMAIN, false, $path );
}

add_action( 'init', 'album_taxonomy_load_textdomain', 0 );


/**
 * Add new custom post type
 *
 */

function create_post_type_album() {
  global $ALBUM_TEXTDOMAIN;

  register_post_type( 'album',
    array(
      'labels' => array(
          'name'                     => __( 'Albums', $ALBUM_TEXTDOMAIN ),
          'singular_name'            => __( 'Album', $ALBUM_TEXTDOMAIN ),
          'add_new_item'             => __( 'Add New Album', $ALBUM_TEXTDOMAIN ),
          'edit_item'                => __( 'Edit Album', $ALBUM_TEXTDOMAIN ),
          'new_item'                 => __( 'New Album', $ALBUM_TEXTDOMAIN ),
          'view_item'                => __( 'View Album', $ALBUM_TEXTDOMAIN ),
          'view_items'               => __( 'View Albums', $ALBUM_TEXTDOMAIN ),
          'search_items'              => __( 'Search Albums', $ALBUM_TEXTDOMAIN ),
          'not_found'                => __( 'No albums found', $ALBUM_TEXTDOMAIN ),
          'not_found_in_trash'       => __( 'No albums found in Trash', $ALBUM_TEXTDOMAIN ),
          'all_items'                => __( 'All Albums', $ALBUM_TEXTDOMAIN ),
          'archives'                 => __( 'Album Archives', $ALBUM_TEXTDOMAIN ),
          'attributes'               => __( 'Album Attributes', $ALBUM_TEXTDOMAIN ),
          'insert_into_item'         => __( 'Insert into album', $ALBUM_TEXTDOMAIN ),
          'uploaded_to_this_item'    => __( 'Uploaded to this album', $ALBUM_TEXTDOMAIN ),
          'item_published'           => __( 'Album published.', $ALBUM_TEXTDOMAIN ),
          'item_published_privately' => __( 'Album published privately.', $ALBUM_TEXTDOMAIN ),
          'item_reverted_to_draft'   => __( 'Album reverted to draft.', $ALBUM_TEXTDOMAIN ),
          'item_scheduled'           => __( 'Album scheduled.', $ALBUM_TEXTDOMAIN ),
          'item_updated'             => __( 'Album updated.', $ALBUM_TEXTDOMAIN ),
      ),
      'public' => true,
      'has_archive' => true,
      'menu_icon' => 'dashicons-album',
      'menu_position' => 5,
      'taxonomies' => array(
          'category',
          'person',
          'location',
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

add_action( 'init', 'create_post_type_album' );


/*
 * Add custom post type album to dashboard widget activity
 *
 */

function add_custom_post_type_album_to_dashboard_activity( $query_args ) {
	if ( is_array( $query_args[ 'post_type' ] ) ) {
		//Set yout post type
		$query_args[ 'post_type' ][] = 'album';
	} else {
		$temp = array( $query_args[ 'post_type' ], 'album' );
		$query_args[ 'post_type' ] = $temp;
	}
	return $query_args;
}

add_filter( 'dashboard_recent_posts_query_args', 'add_custom_post_type_album_to_dashboard_activity' );


/*
 * Add custom post type on dashboard 'At a glance'
 *
 */

function custom_post_type_album_at_a_glance() {
    $args = array(
        'name'     => 'album',
        '_builtin' => false,
    );

    $object = get_post_types( $args, 'objects' );

    foreach ( $object as $post_type ) {
        $num_posts = wp_count_posts( $post_type->name );
        $num = number_format_i18n( $num_posts->publish );
        $text = _n( strtolower( $post_type->labels->singular_name ), strtolower( $post_type->labels->name ), $num_posts->publish );

        if ( current_user_can( 'edit_posts' ) ) {
            $num = '<li class="post-count custom-post-type-album"><a href="edit.php?post_type=' . $post_type->name . '">' . $num . ' ' . $text . '</a></li>';
        }

        echo $num;
    }
}

add_action( 'dashboard_glance_items', 'custom_post_type_album_at_a_glance' );


/*
 * Add album metadata functions
 *
 */

function get_album_title_original ( $post_id ) {
  return get_post_meta($post_id, 'title_original', true);
}

function get_album_price ( $post_id ) {
  return get_post_meta($post_id, 'price', true);
}

function get_album_date_release ( $post_id ) {
  return get_post_meta($post_id, 'date_release', true);
}

function get_album_rating ( $post_id ) {
  return get_post_meta($post_id, 'rating', true);
}

function get_album_amazon ( $post_id ) {
  $arr = array(
    'link' => get_post_meta( $post_id, 'amazon', true ),
    'img' => plugin_dir_url( __FILE__ ) . 'images/logo_amazon.png',
    'img_buy' => plugin_dir_url( __FILE__ ) . 'images/logo_amazon_buy.png'
  );

  return $arr;
}

function get_album_author( $post_id ){
  if( taxonomy_exists( 'person' ) ){
    $authors = array();
    $persons = get_post_meta( $post_id, 'author', true );

    if( $persons ){
      $persons = explode( ',', $persons );

      foreach( $persons as $person ){
        array_push( $authors, get_term_by( 'slug', $person, 'person' ) );
      }

      return $authors;
    }
  }
}

function get_album_author_featured( $post_id ){
  if( taxonomy_exists( 'person' ) ){
    $authors_featured = array();
    $persons = get_post_meta( $post_id, 'author_featured', true );

    if( $persons ){
      $persons = explode( ',', $persons );

      foreach( $persons as $person ){
        array_push( $authors_featured, get_term_by( 'slug', $person, 'person' ) );
      }

      return $authors_featured;
    }
  }
}

?>
