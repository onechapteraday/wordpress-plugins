<?php
/**
 *
 * Plugin Name: Person Custom Taxonomy
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
