<?php
/**
 *
 * Plugin Name: Custom Taxonomy Person
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin allows you to describe precisely the person you are talking about.
 * Version: 0.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */


require_once 'config/parameters.php';
require_once 'admin/dashboard_widgets.php';

require_once 'functions/getters.php';
require_once 'functions/twitter.php';
require_once 'functions/instagram.php';
require_once 'functions/soundcloud.php';

global $SOUNDCLOUD_CLIENT_ID;
global $DASHBOARD_WIDGET_ENABLED;
global $TWITTER_API_KEY;
global $TWITTER_API_SECRET;
global $PERSON_TEXTDOMAIN;

$PERSON_TEXTDOMAIN = 'person-taxonomy';


/*
 * Make plugin available for translation.
 * Translations can be filed in the /languages directory.
 *
 */

function person_taxonomy_load_textdomain() {
  global $PERSON_TEXTDOMAIN;
  $locale = apply_filters( 'plugin_locale', get_locale(), $PERSON_TEXTDOMAIN );

  # Load i18n
  $path = basename( dirname( __FILE__ ) ) . '/languages/';
  $loaded = load_plugin_textdomain( $PERSON_TEXTDOMAIN, false, $path );
}

add_action( 'init', 'person_taxonomy_load_textdomain', 0 );


/**
 * Adding person taxonomy
 *
 */

function add_person_taxonomy() {
  global $PERSON_TEXTDOMAIN;

  $labels = array (
    'name'                       => _x( 'Persons', 'taxonomy general name', $PERSON_TEXTDOMAIN ),
    'singular_name'              => _x( 'Person', 'taxonomy singular name', $PERSON_TEXTDOMAIN ),
    'search_items'               => __( 'Search Persons', $PERSON_TEXTDOMAIN ),
    'popular_items'              => __( 'Popular Persons', $PERSON_TEXTDOMAIN ),
    'all_items'                  => __( 'All Persons', $PERSON_TEXTDOMAIN ),
    'parent_item'                => null,
    'parent_item_colon'          => null,
    'view_item'                  => __( 'See Person', $PERSON_TEXTDOMAIN ),
    'edit_item'                  => __( 'Edit Person', $PERSON_TEXTDOMAIN ),
    'update_item'                => __( 'Update Person', $PERSON_TEXTDOMAIN ),
    'add_new_item'               => __( 'Add New Person', $PERSON_TEXTDOMAIN ),
    'new_item_name'              => __( 'New Person Name', $PERSON_TEXTDOMAIN ),
    'separate_items_with_commas' => __( 'Separate persons with commas', $PERSON_TEXTDOMAIN ),
    'add_or_remove_items'        => __( 'Add or remove persons', $PERSON_TEXTDOMAIN ),
    'choose_from_most_used'      => __( 'Choose from the most used persons', $PERSON_TEXTDOMAIN ),
    'not_found'                  => __( 'No persons found.', $PERSON_TEXTDOMAIN ),
    'menu_name'                  => __( 'Persons', $PERSON_TEXTDOMAIN ),
  );

  $args = array (
    'hierarchical'          => false,
    'labels'                => $labels,
    'show_ui'               => true,
    'show_admin_column'     => true,
    'update_count_callback' => '_update_post_term_count',
    'query_var'             => true,
    'rewrite'               => array( 'slug' => 'person', 'with_front' => 'true' ),
  );

  register_taxonomy ('person', array('post', 'attachment'), $args);
}

add_action ('init', 'add_person_taxonomy', 1);


/**
 * Adding custom fields in person taxonomy
 *
 */

function add_new_meta_field() {
  global $PERSON_TEXTDOMAIN;

  # This will add the custom meta fields to the 'Add new term' page
  ?>
  <div class="form-field">
    <label for="term_meta[realname]"><?php _e( 'Real name', $PERSON_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[realname]" id="term_meta[realname]" value="">
    <p class="description"><?php _e( 'Enter the real name of the person.', $PERSON_TEXTDOMAIN ); ?></p>
  </div>

  <div class="form-field">
    <label for="term_meta[birthdate]"><?php _e( 'Birthdate', $PERSON_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[birthdate]" id="term_meta[birthdate]" value="">
    <p class="description"><?php _e( 'Enter a date with the format YYYY-mm-dd.', $PERSON_TEXTDOMAIN ); ?></p>
  </div>

  <div class="form-field">
    <label for="term_meta[deathdate]"><?php _e( 'Deathdate', $PERSON_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[deathdate]" id="term_meta[deathdate]" value="">
    <p class="description"><?php _e( 'Enter a date with the format YYYY-mm-dd.', $PERSON_TEXTDOMAIN ); ?></p>
  </div>

  <div class="form-field">
    <label for="term_meta[website]"><?php _e( 'Website', $PERSON_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[website]" id="term_meta[website]" value="">
    <p class="description"><?php _e( 'Enter the website of the person, if exists.', $PERSON_TEXTDOMAIN ); ?></p>
  </div>

  <div class="form-field">
    <label for="term_meta[twitter]"><?php _e( 'Twitter', $PERSON_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[twitter]" id="term_meta[twitter]" value="">
    <p class="description"><?php _e( 'Enter the Twitter account name of the person, only the part after the base url.', $PERSON_TEXTDOMAIN ); ?></p>
  </div>

  <div class="form-field">
    <label for="term_meta[facebook]"><?php _e( 'Facebook', $PERSON_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[facebook]" id="term_meta[facebook]" value="">
    <p class="description"><?php _e( 'Enter the Facebook account name of the person, only the part after the base url.', $PERSON_TEXTDOMAIN ); ?></p>
  </div>

  <div class="form-field">
    <label for="term_meta[instagram]"><?php _e( 'Instagram', $PERSON_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[instagram]" id="term_meta[instagram]" value="">
    <p class="description"><?php _e( 'Enter the Instagram account name of the person, only the part after the base url.', $PERSON_TEXTDOMAIN ); ?></p>
  </div>

  <div class="form-field">
    <label for="term_meta[youtube]"><?php _e( 'Youtube', $PERSON_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[youtube]" id="term_meta[youtube]" value="">
    <p class="description"><?php _e( 'Enter the Youtube account name of the person, only the part after the base url.', $PERSON_TEXTDOMAIN ); ?></p>
  </div>

  <div class="form-field">
    <label for="term_meta[soundcloud]"><?php _e( 'Soundcloud', $PERSON_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[soundcloud]" id="term_meta[soundcloud]" value="">
    <p class="description"><?php _e( 'Enter the Soundcloud account name of the person, only the part after the base url.', $PERSON_TEXTDOMAIN ); ?></p>
  </div>

  <div class="form-field">
    <label for="term_meta[sidebar]"><?php _e( 'Sidebar', $PERSON_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[sidebar]" id="term_meta[sidebar]" value="">
    <p class="description"><?php _e( 'Enter the name of the sidebar you want to display, leave empty if default.', $PERSON_TEXTDOMAIN ); ?></p>
  </div>
  <?php

}

add_action( 'person_add_form_fields', 'add_new_meta_field', 10, 2 );


/**
 * Editing custom fields in person taxonomy
 *
 * @param object $term
 *
 */

function edit_meta_field ($term) {
  global $PERSON_TEXTDOMAIN;

  # Put the term ID into a variable
  $t_id = $term->term_id;

  # Retrieve the existing values for this meta field
  # This will return an array
  $term_meta = get_option( "taxonomy_$t_id" );

  ?>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[realname]"><?php _e( 'Real name', $PERSON_TEXTDOMAIN ); ?></label></th>
    <td>
    	<input type="text" name="term_meta[realname]" id="term_meta[realname]" value="<?php echo esc_attr( $term_meta['realname'] ) ? esc_attr( $term_meta['realname'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the real name of the person', $PERSON_TEXTDOMAIN); ?></p>
    </td>
  </tr>

  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[birthdate]"><?php _e( 'Birthdate', $PERSON_TEXTDOMAIN ); ?></label></th>
    <td>
    	<input type="text" name="term_meta[birthdate]" id="term_meta[birthdate]" value="<?php echo esc_attr( $term_meta['birthdate'] ) ? esc_attr( $term_meta['birthdate'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter a date with the format YYYY-mm-dd', $PERSON_TEXTDOMAIN ); ?></p>
    </td>
  </tr>

  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[deathdate]"><?php _e( 'Deathdate', $PERSON_TEXTDOMAIN ); ?></label></th>
    <td>
    	<input type="text" name="term_meta[deathdate]" id="term_meta[deathdate]" value="<?php echo esc_attr( $term_meta['deathdate'] ) ? esc_attr( $term_meta['deathdate'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter a date with the format YYYY-mm-dd', $PERSON_TEXTDOMAIN ); ?></p>
    </td>
  </tr>

  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[website]"><?php _e( 'Website', $PERSON_TEXTDOMAIN ); ?></label></th>
    <td>
    	<input type="text" name="term_meta[website]" id="term_meta[website]" value="<?php echo esc_attr( $term_meta['website'] ) ? esc_attr( $term_meta['website'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the website of the person, if exists.', $PERSON_TEXTDOMAIN ); ?></p>
    </td>
  </tr>

  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[twitter]"><?php _e( 'Twitter', $PERSON_TEXTDOMAIN ); ?></label></th>
    <td>
    	<input type="text" name="term_meta[twitter]" id="term_meta[twitter]" value="<?php echo esc_attr( $term_meta['twitter'] ) ? esc_attr( $term_meta['twitter'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the Twitter account name of the person, only the part after the base url.', $PERSON_TEXTDOMAIN ); ?></p>
    </td>
  </tr>

  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[facebook]"><?php _e( 'Facebook', $PERSON_TEXTDOMAIN ); ?></label></th>
    <td>
    	<input type="text" name="term_meta[facebook]" id="term_meta[facebook]" value="<?php echo esc_attr( $term_meta['facebook'] ) ? esc_attr( $term_meta['facebook'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the Facebook account name of the person, only the part after the base url.', $PERSON_TEXTDOMAIN ); ?></p>
    </td>
  </tr>

  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[instagram]"><?php _e( 'Instagram', $PERSON_TEXTDOMAIN ); ?></label></th>
    <td>
    	<input type="text" name="term_meta[instagram]" id="term_meta[instagram]" value="<?php echo esc_attr( $term_meta['instagram'] ) ? esc_attr( $term_meta['instagram'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the Instagram account name of the person, only the part after the base url.', $PERSON_TEXTDOMAIN ); ?></p>
    </td>
  </tr>

  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[youtube]"><?php _e( 'Youtube', $PERSON_TEXTDOMAIN ); ?></label></th>
    <td>
    	<input type="text" name="term_meta[youtube]" id="term_meta[youtube]" value="<?php echo esc_attr( $term_meta['youtube'] ) ? esc_attr( $term_meta['youtube'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the Youtube account name of the person, only the part after the base url.', $PERSON_TEXTDOMAIN ); ?></p>
    </td>
  </tr>

  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[soundcloud]"><?php _e( 'Soundcloud', $PERSON_TEXTDOMAIN ); ?></label></th>
    <td>
    	<input type="text" name="term_meta[soundcloud]" id="term_meta[soundcloud]" value="<?php echo esc_attr( $term_meta['soundcloud'] ) ? esc_attr( $term_meta['soundcloud'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the Soundcloud account name of the person, only the part after the base url.', $PERSON_TEXTDOMAIN ); ?></p>
    </td>
  </tr>

  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[sidebar]"><?php _e( 'Sidebar', $PERSON_TEXTDOMAIN ); ?></label></th>
    <td>
      <input type="text" name="term_meta[sidebar]" id="term_meta[sidebar]" value="<?php echo esc_attr( $term_meta['sidebar'] ) ? esc_attr( $term_meta['sidebar'] ) : ''; ?>">
      <p class="description"><?php _e( 'Enter the name of the sidebar you want to display, leave empty if default.', $PERSON_TEXTDOMAIN ); ?></p>
    </td>
  </tr>
  <?php

}

add_action( 'person_edit_form_fields', 'edit_meta_field', 10, 2 );


/**
 * Saving custom fields in person taxonomy
 *
 * @param int $term_id
 *
 */

function save_taxonomy_custom_meta ($term_id) {
  if (isset($_POST['term_meta'])) {
    $t_id = $term_id;
    $term_meta = get_option("taxonomy_$t_id");
    $cat_keys = array_keys($_POST['term_meta']);

    foreach ($cat_keys as $key) {
      if (isset($_POST['term_meta'][$key])) {
      	$term_meta[$key] = $_POST['term_meta'][$key];
      }
    }

    # Save the option array
    update_option( "taxonomy_$t_id", $term_meta );
  }
}

add_action( 'edited_person', 'save_taxonomy_custom_meta', 10, 2 );
add_action( 'create_person', 'save_taxonomy_custom_meta', 10, 2 );


/**
 * Create widget to retrieve popular persons in specific category
 *
 */

class popular_persons_in_category_widget extends WP_Widget {
    function __construct() {
        parent::__construct(
            # Base ID of your widget
            'popular_persons_in_category_widget',

            # Widget name will appear in UI
            __('Popular Persons in Category Widget', 'popular_persons_in_category_widget_domain'),

            # Widget description
            array( 'description' => __( 'This widget will show all the persons in the specific category you choose', 'popular_persons_in_category_widget_domain' ), )
        );
    }

    # Creating widget front-end
    public function widget( $args, $instance ) {
        $title = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';

        # Before and after widget arguments are defined by themes
        echo $args['before_widget'];

        if ( ! empty( $title ) )
            echo $args['before_title'] . $title . $args['after_title'];

        # This is where you run the code and display the output

        # Find the category where is displayed the widget
        $categories = get_the_category();

	$catID = null;
	if ( isset ( $categories[0] ) ) {
            $catID = $categories[0]->cat_ID;
	}

        $post_types = array( 'post' );

        if( post_type_exists( 'book' ) ){
            array_push( $post_types, 'book' );
        }

        if( post_type_exists( 'album' ) ){
            array_push( $post_types, 'album' );
        }

        if( post_type_exists( 'interview' ) ){
            array_push( $post_types, 'interview' );
        }

        if ( $catID ) {
            $posts_with_category = get_posts( array(
                         'category'       => $catID,
                         'post_type'      => $post_types,
                         'number_posts'   => -1,
                         'posts_per_page' => -1,
                     ));
        }
        else {
            $posts_with_category = get_posts( array(
                         'post_type'      => $post_types,
                         'number_posts'   => -1,
                         'posts_per_page' => -1,
                     ));
        }

        $array_of_terms_in_category = array();

        foreach( $posts_with_category as $post ) {
            $terms = wp_get_post_terms( $post->ID, 'person' );

            foreach( $terms as $value ){
                if( !in_array( $value, $array_of_terms_in_category, true ) ){
                    array_push( $array_of_terms_in_category, $value->term_id );
                }
            }
        }

        $tag_args = array(
                    'format'   => 'array',
                    'number'   => 75,
                    'taxonomy' => 'person',
                    'orderby'  => 'count',
                    'order'    => 'DESC',
                    'include'  => $array_of_terms_in_category,
                    'echo'     => false,
                );

        echo '<div class="tagcloud">';

        $persons_array = get_terms ( 'person', $tag_args );

        if( sizeof( $persons_array ) ){
            function widget_sort_person_by_name( $a, $b ){
                $translit = array('Á'=>'A','À'=>'A','Â'=>'A','Ä'=>'A','Ã'=>'A','Å'=>'A','Ç'=>'C','É'=>'E','È'=>'E','Ê'=>'E','Ë'=>'E','Í'=>'I','Ï'=>'I','Î'=>'I','Ì'=>'I','Ñ'=>'N','Ó'=>'O','Ò'=>'O','Ô'=>'O','Ö'=>'O','Õ'=>'O','Ú'=>'U','Ù'=>'U','Û'=>'U','Ü'=>'U','Ý'=>'Y','á'=>'a','à'=>'a','â'=>'a','ä'=>'a','ã'=>'a','å'=>'a','ç'=>'c','é'=>'e','è'=>'e','ê'=>'e','ë'=>'e','í'=>'i','ì'=>'i','î'=>'i','ï'=>'i','ñ'=>'n','ó'=>'o','ò'=>'o','ô'=>'o','ö'=>'o','õ'=>'o','ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u','ý'=>'y','ÿ'=>'y');
                $at = strtolower( strtr( $a->name, $translit ) );
                $bt = strtolower( strtr( $b->name, $translit ) );

                return strcoll( $at, $bt );
            }

            usort( $persons_array, 'widget_sort_person_by_name' );

            echo '<ul class="wp-tag-cloud">';

	    foreach ( $persons_array as $myperson ) {
                echo '<li><a href="' . get_term_link( $myperson->term_id ) . '" class="tag-cloud-link tag-link-' . $myperson->term_id . '">';
                echo $myperson->name;
                echo '</a></li>';
	    }

            echo '</ul>';
	}

        echo '</div>';

        echo $args['after_widget'];
    }

    # Widget Backend
    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        } else {
            $title = __( 'New title', 'popular_persons_in_category_widget_domain' );
        }

        # Widget admin form
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <?php
    }

    # Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        return $instance;
    }
}

# Register and load the widget
function wpb_load_widget() {
    register_widget( 'popular_persons_in_category_widget' );
}

add_action( 'widgets_init', 'wpb_load_widget' );


/**
 * Flush rewrites when the plugin is activated
 *
 */

function person_taxonomy_flush_rewrites() {
  flush_rewrite_rules();
}

# Prevent 404 errors on persons' archive

register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
register_activation_hook( __FILE__, 'person_taxonomy_flush_rewrites' );

add_action( 'init', 'person_taxonomy_flush_rewrites', 0 );

?>
