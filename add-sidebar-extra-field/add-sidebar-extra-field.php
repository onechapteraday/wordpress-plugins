<?php
/**
 *
 * Plugin Name: Add Sidebar Extra Field
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin gives you an extra field in tags to precise sidebar.
 * Version: 0.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */

global $SIDEBAR_TEXTDOMAIN;

$SIDEBAR_TEXTDOMAIN = 'sidebar-extra-field';


/*
 * Make plugin available for translation.
 * Translations can be filed in the /languages directory.
 *
 */

function sidebar_extra_field_load_textdomain(){
  global $SIDEBAR_TEXTDOMAIN;
  $locale = apply_filters( 'plugin_locale', get_locale(), $SIDEBAR_TEXTDOMAIN );

  # Load i18n
  $path   = basename( dirname( __FILE__ ) ) . '/languages/';
  $loaded = load_plugin_textdomain( $SIDEBAR_TEXTDOMAIN, false, $path );
}

add_action( 'init', 'sidebar_extra_field_load_textdomain', 0 );


/**
 * Add extra field sidebar
 *
 */

function post_tag_add_sidebar_extra_field( $term_obj ){
  global $SIDEBAR_TEXTDOMAIN;

  ?>
  <div class="form-field">
    <label for="term_meta[sidebar]"><?php _e( 'Sidebar', $SIDEBAR_TEXTDOMAIN ); ?></label>
    <input type="text" id="term_meta[sidebar]" name="term_meta[sidebar]" value="" />
    <p class="description"><?php _e( 'Enter the name of the sidebar that you want to display.', $SIDEBAR_TEXTDOMAIN ); ?></p>
  </div>
  <?php
}

add_action( 'post_tag_add_form_fields', 'post_tag_add_sidebar_extra_field', 10, 2 );


/**
 * Edit extra field sidebar
 *
 * @param object $term
 *
 */

function post_tag_edit_sidebar_extra_field( $term ){
  global $SIDEBAR_TEXTDOMAIN;

  # Put the term ID into a variable
  $t_id = $term->term_id;

  # Retrieve the existing values for this meta field
  # This will return an array
  $term_meta = get_option( "taxonomy_$t_id" );

  ?>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[sidebar]"><?php _e( 'Sidebar', $SIDEBAR_TEXTDOMAIN ); ?></label></th>
    <td>
      <input type="text" name="term_meta[sidebar]" id="term_meta[sidebar]" value="<?php echo esc_attr( $term_meta['sidebar'] ) ? esc_attr( $term_meta['sidebar'] ) : ''; ?>" />
      <p class="description"><?php _e( 'Enter the name of the sidebar that you want to display.', $SIDEBAR_TEXTDOMAIN ); ?></p>
    </td>
  </tr>
  <?php
}

add_action( 'post_tag_edit_form_fields', 'post_tag_edit_sidebar_extra_field', 10, 2 );


/**
 * Save extra field sidebar
 *
 * @param int $term_id
 *
 */

function post_tag_save_sidebar_extra_field( $term_id ){
  if( isset( $_POST['term_meta'] ) ){
    $t_id       = $term_id;
    $term_meta  = get_option( "taxonomy_$t_id" );
    $meta_keys  = array_keys( $_POST['term_meta'] );

    foreach( $meta_keys as $key ){
      if( isset( $_POST['term_meta'][$key] ) ){
        $term_meta[$key] = $_POST['term_meta'][$key];
      }
    }

    # Save the option array
    update_option( "taxonomy_$t_id", $term_meta );
  }
}

add_action(   'edit_term', 'post_tag_save_sidebar_extra_field', 10, 2 );
add_action( 'create_term', 'post_tag_save_sidebar_extra_field', 10, 2 );

?>
