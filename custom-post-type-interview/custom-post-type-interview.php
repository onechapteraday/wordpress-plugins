<?php
/**
 *
 * Plugin Name: Custom Post Type Interview
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin creates a new post type called interviews.
 * Version: 0.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */

global $INTERVIEW_TEXTDOMAIN;

$INTERVIEW_TEXTDOMAIN = 'interview-post-type';


/*
 * Make plugin available for translation.
 * Translations can be filed in the /languages directory.
 *
 */

function interview_taxonomy_load_textdomain() {
  global $INTERVIEW_TEXTDOMAIN;
  $locale = apply_filters( 'plugin_locale', get_locale(), $INTERVIEW_TEXTDOMAIN );

  # Load i18n
  $path = basename( dirname( __FILE__ ) ) . '/languages/';
  $loaded = load_plugin_textdomain( $INTERVIEW_TEXTDOMAIN, false, $path );
}

add_action( 'init', 'interview_taxonomy_load_textdomain', 0 );


/**
 * Add new custom post type
 *
 */

function create_post_type_interview() {
  global $INTERVIEW_TEXTDOMAIN;

  register_post_type( 'interview',
    array(
      'labels' => array(
          'name'                     => __( 'Interviews', $INTERVIEW_TEXTDOMAIN ),
          'singular_name'            => __( 'Interview', $INTERVIEW_TEXTDOMAIN ),
          'add_new_item'             => __( 'Add New Interview', $INTERVIEW_TEXTDOMAIN ),
          'edit_item'                => __( 'Edit Interview', $INTERVIEW_TEXTDOMAIN ),
          'new_item'                 => __( 'New Interview', $INTERVIEW_TEXTDOMAIN ),
          'view_item'                => __( 'View Interview', $INTERVIEW_TEXTDOMAIN ),
          'view_items'               => __( 'View Interviews', $INTERVIEW_TEXTDOMAIN ),
          'search_items'             => __( 'Search Interviews', $INTERVIEW_TEXTDOMAIN ),
          'not_found'                => __( 'No interviews found', $INTERVIEW_TEXTDOMAIN ),
          'not_found_in_trash'       => __( 'No interviews found in Trash', $INTERVIEW_TEXTDOMAIN ),
          'all_items'                => __( 'All Interviews', $INTERVIEW_TEXTDOMAIN ),
          'archives'                 => __( 'Interview Archives', $INTERVIEW_TEXTDOMAIN ),
          'attributes'               => __( 'Interview Attributes', $INTERVIEW_TEXTDOMAIN ),
          'insert_into_item'         => __( 'Insert into interview', $INTERVIEW_TEXTDOMAIN ),
          'uploaded_to_this_item'    => __( 'Uploaded to this interview', $INTERVIEW_TEXTDOMAIN ),
          'item_published'           => __( 'Interview published.', $INTERVIEW_TEXTDOMAIN ),
          'item_published_privately' => __( 'Interview published privately.', $INTERVIEW_TEXTDOMAIN ),
          'item_reverted_to_draft'   => __( 'Interview reverted to draft.', $INTERVIEW_TEXTDOMAIN ),
          'item_scheduled'           => __( 'Interview scheduled.', $INTERVIEW_TEXTDOMAIN ),
          'item_updated'             => __( 'Interview updated.', $INTERVIEW_TEXTDOMAIN ),
      ),
      'public' => true,
      'has_archive' => true,
      'menu_icon' => 'dashicons-microphone',
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

add_action( 'init', 'create_post_type_interview' );


/*
 * Add custom post type interview to dashboard widget activity
 *
 */

function add_custom_post_type_interview_to_dashboard_activity( $query_args ) {
	if ( is_array( $query_args[ 'post_type' ] ) ) {
		//Set yout post type
		$query_args[ 'post_type' ][] = 'interview';
	} else {
		$temp = array( $query_args[ 'post_type' ], 'interview' );
		$query_args[ 'post_type' ] = $temp;
	}
	return $query_args;
}

add_filter( 'dashboard_recent_posts_query_args', 'add_custom_post_type_interview_to_dashboard_activity' );


/*
 * Add custom post type on dashboard 'At a glance'
 *
 */

function custom_post_type_interview_at_a_glance() {
    $args = array(
        'name'     => 'interview',
        '_builtin' => false,
    );

    $object = get_post_types( $args, 'objects' );

    foreach ( $object as $post_type ) {
        $num_posts = wp_count_posts( $post_type->name );
        $num = number_format_i18n( $num_posts->publish );
        $text = _n( strtolower( $post_type->labels->singular_name ), strtolower( $post_type->labels->name ), $num_posts->publish );

        if ( current_user_can( 'edit_posts' ) ) {
            $num = '<li class="post-count custom-post-type-interview"><a href="edit.php?post_type=' . $post_type->name . '">' . $num . ' ' . $text . '</a></li>';
        }

        echo $num;
    }
}

add_action( 'dashboard_glance_items', 'custom_post_type_interview_at_a_glance' );


?>
