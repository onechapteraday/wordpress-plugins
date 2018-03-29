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

$INTERVIEW_TEXTDOMAIN = 'interview-taxonomy';


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
        'name' => __( 'Interviews', $INTERVIEW_TEXTDOMAIN ),
        'singular_name' => __( 'Interview', $INTERVIEW_TEXTDOMAIN )
      ),
      'public' => true,
      'has_archive' => true,
      'menu_icon' => 'dashicons-microphone',
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
