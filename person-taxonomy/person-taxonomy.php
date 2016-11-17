<?php
/**
 *
 * Plugin Name: Person Custom Taxonomy
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin allows you to describe more precisely the person you are talking about.
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


/**
 * Adding person taxonomy
 *
 */

function add_person_taxonomy() {
  $labels = array (
    'name'                       => _x( 'Persons', 'taxonomy general name' ),
    'singular_name'              => _x( 'Person', 'taxonomy singular name' ),
    'search_items'               => __( 'Search Persons' ),
    'popular_items'              => __( 'Popular Persons' ),
    'all_items'                  => __( 'All Persons' ),
    'parent_item'                => null,
    'parent_item_colon'          => null,
    'edit_item'                  => __( 'Edit Person' ),
    'update_item'                => __( 'Update Person' ),
    'add_new_item'               => __( 'Add New Person' ),
    'new_item_name'              => __( 'New Person Name' ),
    'separate_items_with_commas' => __( 'Separate persons with commas' ),
    'add_or_remove_items'        => __( 'Add or remove persons' ),
    'choose_from_most_used'      => __( 'Choose from the most used persons' ),
    'not_found'                  => __( 'No persons found.' ),
    'menu_name'                  => __( 'Persons' ),
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

add_action ('init', 'add_person_taxonomy', 0);


/**
 * Adding custom fields in person taxonomy
 *
 */

function add_new_meta_field() {
  # This will add the custom meta fields to the 'Add new term' page

  ?>
  <div class="form-field">
    <label for="term_meta[realname]"><?php _e( 'Real name' ); ?></label>
    <input type="text" name="term_meta[realname]" id="term_meta[realname]" value="">
    <p class="description"><?php _e( 'Enter the real name of the person.' ); ?></p>
  </div>

  <div class="form-field">
    <label for="term_meta[birthdate]"><?php _e( 'Birthdate' ); ?></label>
    <input type="text" name="term_meta[birthdate]" id="term_meta[birthdate]" value="">
    <p class="description"><?php _e( 'Enter a date with the format YYYY-mm-dd.' ); ?></p>
  </div>

  <div class="form-field">
    <label for="term_meta[deathdate]"><?php _e( 'Deathdate' ); ?></label>
    <input type="text" name="term_meta[deathdate]" id="term_meta[deathdate]" value="">
    <p class="description"><?php _e( 'Enter a date with the format YYYY-mm-dd.' ); ?></p>
  </div>

  <div class="form-field">
    <label for="term_meta[website]"><?php _e( 'Website' ); ?></label>
    <input type="text" name="term_meta[website]" id="term_meta[website]" value="">
    <p class="description"><?php _e( 'Enter the website of the person, if exists.' ); ?></p>
  </div>

  <div class="form-field">
    <label for="term_meta[twitter]"><?php _e( 'Twitter' ); ?></label>
    <input type="text" name="term_meta[twitter]" id="term_meta[twitter]" value="">
    <p class="description"><?php _e( 'Enter the Twitter account name of the person, only the part after the base url.' ); ?></p>
  </div>

  <div class="form-field">
    <label for="term_meta[facebook]"><?php _e( 'Facebook' ); ?></label>
    <input type="text" name="term_meta[facebook]" id="term_meta[facebook]" value="">
    <p class="description"><?php _e( 'Enter the Facebook account name of the person, only the part after the base url.' ); ?></p>
  </div>

  <div class="form-field">
    <label for="term_meta[instagram]"><?php _e( 'Instagram' ); ?></label>
    <input type="text" name="term_meta[instagram]" id="term_meta[instagram]" value="">
    <p class="description"><?php _e( 'Enter the Instagram account name of the person, only the part after the base url.' ); ?></p>
  </div>

  <div class="form-field">
    <label for="term_meta[youtube]"><?php _e( 'Youtube' ); ?></label>
    <input type="text" name="term_meta[youtube]" id="term_meta[youtube]" value="">
    <p class="description"><?php _e( 'Enter the Youtube account name of the person, only the part after the base url.' ); ?></p>
  </div>

  <div class="form-field">
    <label for="term_meta[soundcloud]"><?php _e( 'Soundcloud' ); ?></label>
    <input type="text" name="term_meta[soundcloud]" id="term_meta[soundcloud]" value="">
    <p class="description"><?php _e( 'Enter the Soundcloud account name of the person, only the part after the base url.' ); ?></p>
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
  # Put the term ID into a variable
  $t_id = $term->term_id;

  # Retrieve the existing values for this meta field
  # This will return an array
  $term_meta = get_option( "taxonomy_$t_id" );

  ?>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[realname]"><?php _e( 'Real name' ); ?></label></th>
    <td>
    	<input type="text" name="term_meta[realname]" id="term_meta[realname]" value="<?php echo esc_attr( $term_meta['realname'] ) ? esc_attr( $term_meta['realname'] ) : ''; ?>">
    	<p class="description"><?php _e( 'Enter the real name of the person' ); ?></p>
    </td>
  </tr>

  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[birthdate]"><?php _e( 'Birthdate' ); ?></label></th>
    <td>
    	<input type="text" name="term_meta[birthdate]" id="term_meta[birthdate]" value="<?php echo esc_attr( $term_meta['birthdate'] ) ? esc_attr( $term_meta['birthdate'] ) : ''; ?>">
    	<p class="description"><?php _e( 'Enter a date with the format YYYY-mm-dd' ); ?></p>
    </td>
  </tr>

  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[deathdate]"><?php _e( 'Deathdate' ); ?></label></th>
    <td>
    	<input type="text" name="term_meta[deathdate]" id="term_meta[deathdate]" value="<?php echo esc_attr( $term_meta['deathdate'] ) ? esc_attr( $term_meta['deathdate'] ) : ''; ?>">
    	<p class="description"><?php _e( 'Enter a date with the format YYYY-mm-dd' ); ?></p>
    </td>
  </tr>

  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[website]"><?php _e( 'Website' ); ?></label></th>
    <td>
    	<input type="text" name="term_meta[website]" id="term_meta[website]" value="<?php echo esc_attr( $term_meta['website'] ) ? esc_attr( $term_meta['website'] ) : ''; ?>">
    	<p class="description"><?php _e( 'Enter the website of the person, if exists.' ); ?></p>
    </td>
  </tr>

  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[twitter]"><?php _e( 'Twitter' ); ?></label></th>
    <td>
    	<input type="text" name="term_meta[twitter]" id="term_meta[twitter]" value="<?php echo esc_attr( $term_meta['twitter'] ) ? esc_attr( $term_meta['twitter'] ) : ''; ?>">
    	<p class="description"><?php _e( 'Enter the Twitter account name of the person, only the part after the base url.' ); ?></p>
    </td>
  </tr>

  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[facebook]"><?php _e( 'Facebook' ); ?></label></th>
    <td>
    	<input type="text" name="term_meta[facebook]" id="term_meta[facebook]" value="<?php echo esc_attr( $term_meta['facebook'] ) ? esc_attr( $term_meta['facebook'] ) : ''; ?>">
    	<p class="description"><?php _e( 'Enter the Facebook account name of the person, only the part after the base url.' ); ?></p>
    </td>
  </tr>

  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[instagram]"><?php _e( 'Instagram' ); ?></label></th>
    <td>
    	<input type="text" name="term_meta[instagram]" id="term_meta[instagram]" value="<?php echo esc_attr( $term_meta['instagram'] ) ? esc_attr( $term_meta['instagram'] ) : ''; ?>">
    	<p class="description"><?php _e( 'Enter the Instagram account name of the person, only the part after the base url.' ); ?></p>
    </td>
  </tr>

  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[youtube]"><?php _e( 'Youtube' ); ?></label></th>
    <td>
    	<input type="text" name="term_meta[youtube]" id="term_meta[youtube]" value="<?php echo esc_attr( $term_meta['youtube'] ) ? esc_attr( $term_meta['youtube'] ) : ''; ?>">
    	<p class="description"><?php _e( 'Enter the Youtube account name of the person, only the part after the base url.' ); ?></p>
    </td>
  </tr>

  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[soundcloud]"><?php _e( 'Soundcloud' ); ?></label></th>
    <td>
    	<input type="text" name="term_meta[soundcloud]" id="term_meta[soundcloud]" value="<?php echo esc_attr( $term_meta['soundcloud'] ) ? esc_attr( $term_meta['soundcloud'] ) : ''; ?>">
    	<p class="description"><?php _e( 'Enter the Facebook account name of the person, only the part after the base url.' ); ?></p>
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

add_action( 'init', 'person_taxonomy_flush_rewrites' );

?>
