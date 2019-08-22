<?php
/**
 *
 * Plugin Name: Custom Post Type Concert
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin creates a new post type called concerts.
 * Version: 0.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */

global $CONCERT_TEXTDOMAIN;

$CONCERT_TEXTDOMAIN = 'concert-post-type';


/*
 * Make plugin available for translation.
 * Translations can be filed in the /languages directory.
 *
 */

function concert_taxonomy_load_textdomain() {
  global $CONCERT_TEXTDOMAIN;
  $locale = apply_filters( 'plugin_locale', get_locale(), $CONCERT_TEXTDOMAIN );

  # Load i18n
  $path = basename( dirname( __FILE__ ) ) . '/languages/';
  $loaded = load_plugin_textdomain( $CONCERT_TEXTDOMAIN, false, $path );
}

add_action( 'init', 'concert_taxonomy_load_textdomain', 0 );


/**
 * Add new custom post type
 *
 */

function create_post_type_concert() {
  global $CONCERT_TEXTDOMAIN;

  register_post_type( 'concert',
    array(
      'labels' => array(
          'name'                     => __( 'Concerts', $CONCERT_TEXTDOMAIN ),
          'singular_name'            => __( 'Concert', $CONCERT_TEXTDOMAIN ),
          'add_new_item'             => __( 'Add New Concert', $CONCERT_TEXTDOMAIN ),
          'edit_item'                => __( 'Edit Concert', $CONCERT_TEXTDOMAIN ),
          'new_item'                 => __( 'New Concert', $CONCERT_TEXTDOMAIN ),
          'view_item'                => __( 'View Concert', $CONCERT_TEXTDOMAIN ),
          'view_items'               => __( 'View Concerts', $CONCERT_TEXTDOMAIN ),
          'search_items'              => __( 'Search Concerts', $CONCERT_TEXTDOMAIN ),
          'not_found'                => __( 'No concerts found', $CONCERT_TEXTDOMAIN ),
          'not_found_in_trash'       => __( 'No concerts found in Trash', $CONCERT_TEXTDOMAIN ),
          'all_items'                => __( 'All Concerts', $CONCERT_TEXTDOMAIN ),
          'archives'                 => __( 'Concert Archives', $CONCERT_TEXTDOMAIN ),
          'attributes'               => __( 'Concert Attributes', $CONCERT_TEXTDOMAIN ),
          'insert_into_item'         => __( 'Insert into concert', $CONCERT_TEXTDOMAIN ),
          'uploaded_to_this_item'    => __( 'Uploaded to this concert', $CONCERT_TEXTDOMAIN ),
          'item_published'           => __( 'Concert published.', $CONCERT_TEXTDOMAIN ),
          'item_published_privately' => __( 'Concert published privately.', $CONCERT_TEXTDOMAIN ),
          'item_reverted_to_draft'   => __( 'Concert reverted to draft.', $CONCERT_TEXTDOMAIN ),
          'item_scheduled'           => __( 'Concert scheduled.', $CONCERT_TEXTDOMAIN ),
          'item_updated'             => __( 'Concert updated.', $CONCERT_TEXTDOMAIN ),
      ),
      'public' => true,
      'has_archive' => true,
      'menu_icon' => 'dashicons-tickets-alt',
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

add_action( 'init', 'create_post_type_concert' );


/*
 * Add custom post type concert to dashboard widget activity
 *
 */

function add_custom_post_type_concert_to_dashboard_activity( $query_args ) {
	if ( is_array( $query_args[ 'post_type' ] ) ) {
		//Set yout post type
		$query_args[ 'post_type' ][] = 'concert';
	} else {
		$temp = array( $query_args[ 'post_type' ], 'concert' );
		$query_args[ 'post_type' ] = $temp;
	}
	return $query_args;
}

add_filter( 'dashboard_recent_posts_query_args', 'add_custom_post_type_concert_to_dashboard_activity' );


/*
 * Add custom post type on dashboard 'At a glance'
 *
 */

function custom_post_type_concert_at_a_glance() {
    $args = array(
        'name'     => 'concert',
        '_builtin' => false,
    );

    $object = get_post_types( $args, 'objects' );

    foreach ( $object as $post_type ) {
        $num_posts = wp_count_posts( $post_type->name );
        $num = number_format_i18n( $num_posts->publish );
        $text = _n( strtolower( $post_type->labels->singular_name ), strtolower( $post_type->labels->name ), $num_posts->publish );

        if ( current_user_can( 'edit_posts' ) ) {
            $num = '<li class="post-count custom-post-type-concert"><a href="edit.php?post_type=' . $post_type->name . '">' . $num . ' ' . $text . '</a></li>';
        }

        echo $num;
    }
}

add_action( 'dashboard_glance_items', 'custom_post_type_concert_at_a_glance' );


?>
