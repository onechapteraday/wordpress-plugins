<?php
/**
 *
 * Plugin Name: Custom Taxonomy Selection
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin allows you to add prize selections for your custom post type books.
 * Version: 0.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */

global $SELECTION_TEXTDOMAIN;

$SELECTION_TEXTDOMAIN = 'selection-taxonomy';


/*
 * Make plugin available for translation.
 * Translations can be filed in the /languages directory.
 *
 */

function literary_selection_taxonomy_load_textdomain() {
  global $SELECTION_TEXTDOMAIN;
  $locale = apply_filters( 'plugin_locale', get_locale(), $SELECTION_TEXTDOMAIN );

  # Load i18n
  $path = basename( dirname( __FILE__ ) ) . '/languages/';
  $loaded = load_plugin_textdomain( $SELECTION_TEXTDOMAIN, false, $path );
}

add_action( 'init', 'literary_selection_taxonomy_load_textdomain', 0 );


/**
 * Add selection taxonomy
 *
 **/

function add_selection_taxonomy() {
  global $SELECTION_TEXTDOMAIN;

  $labels = array (
    'name'                       => _x( 'Selections', 'taxonomy general name', $SELECTION_TEXTDOMAIN ),
    'singular_name'              => _x( 'Selection', 'taxonomy singular name', $SELECTION_TEXTDOMAIN ),
    'search_items'               => __( 'Search Selections', $SELECTION_TEXTDOMAIN ),
    'popular_items'              => __( 'Popular Selections', $SELECTION_TEXTDOMAIN ),
    'all_items'                  => __( 'All Selections', $SELECTION_TEXTDOMAIN ),
    'parent_item'                => null,
    'parent_item_colon'          => null,
    'view_item'                  => __( 'See Selection', $SELECTION_TEXTDOMAIN ),
    'edit_item'                  => __( 'Edit Selection', $SELECTION_TEXTDOMAIN ),
    'update_item'                => __( 'Update Selection', $SELECTION_TEXTDOMAIN ),
    'add_new_item'               => __( 'Add New Selection', $SELECTION_TEXTDOMAIN ),
    'new_item_name'              => __( 'New Selection Name', $SELECTION_TEXTDOMAIN ),
    'separate_items_with_commas' => __( 'Separate selections with commas', $SELECTION_TEXTDOMAIN ),
    'add_or_remove_items'        => __( 'Add or remove selections', $SELECTION_TEXTDOMAIN ),
    'choose_from_most_used'      => __( 'Choose from the most used selections', $SELECTION_TEXTDOMAIN ),
    'not_found'                  => __( 'No selections found.', $SELECTION_TEXTDOMAIN ),
    'back_to_items'              => __( '← Back to selections', $SELECTION_TEXTDOMAIN ),
    'menu_name'                  => __( 'Selections', $SELECTION_TEXTDOMAIN ),
  );

  $args = array (
    'hierarchical'          => true,
    'labels'                => $labels,
    'show_ui'               => true,
    'show_admin_column'     => true,
    'update_count_callback' => '_update_post_term_count',
    'query_var'             => true,
    'rewrite'               => array( 'slug' => 'selection', 'with_front' => 'true', 'hierarchical' => true ),
  );

  register_taxonomy ('selection', 'book', $args);
}

add_action ('init', 'add_selection_taxonomy', 1);


/**
 * Add custom field link
 *
 **/

function add_new_selection_meta_field() {
  global $SELECTION_TEXTDOMAIN;

  # This will add the custom meta fields to the 'Add new term' page
  ?>
  <div class="form-field">
    <label for="term_meta[selection_prize]"><?php _e( 'Prize related to selection', $SELECTION_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[selection_prize]" id="term_meta[selection_prize]" value="">
    <p class="description"><?php _e( 'Enter the prize to which the selection is about.', $SELECTION_TEXTDOMAIN ); ?></p>
  </div>

  <div class="form-field">
    <label for="term_meta[selection_order]"><?php _e( 'Selection ordinal number', $SELECTION_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[selection_order]" id="term_meta[selection_order]" value="">
    <p class="description"><?php _e( 'Enter the ordinal number of the selection.', $SELECTION_TEXTDOMAIN ); ?></p>
  </div>

  <div class="form-field">
    <label for="term_meta[selection_date]"><?php _e( 'Date of selection', $SELECTION_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[selection_date]" id="term_meta[selection_date]" value="">
    <p class="description"><?php _e( 'Enter the date of selection.', $SELECTION_TEXTDOMAIN ); ?></p>
  </div>
  <?php
}

add_action( 'selection_add_form_fields', 'add_new_selection_meta_field', 10, 2 );


/**
 * Editing custom fields in selection taxonomy
 *
 * @param object $term
 *
 */

function edit_selection_meta_field ($term) {
  global $SELECTION_TEXTDOMAIN;

  # Put the term ID into a variable
  $t_id = $term->term_id;

  # Retrieve the existing values for this meta field
  # This will return an array
  $term_meta = get_option( "taxonomy_$t_id" );

  ?>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[selection_prize]"><?php _e( 'Prize related to selection', $SELECTION_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[selection_prize]" id="term_meta[selection_prize]" value="<?php echo esc_attr( $term_meta['selection_prize'] ) ? esc_attr( $term_meta['selection_prize'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the prize to which the selection is about.', $SELECTION_TEXTDOMAIN); ?></p>
    </td>
  </tr>

  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[selection_order]"><?php _e( 'Selection ordinal number', $SELECTION_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[selection_order]" id="term_meta[selection_order]" value="<?php echo esc_attr( $term_meta['selection_order'] ) ? esc_attr( $term_meta['selection_order'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the ordinal number of the selection.', $SELECTION_TEXTDOMAIN); ?></p>
    </td>
  </tr>

  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[selection_date]"><?php _e( 'Date of selection', $SELECTION_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[selection_date]" id="term_meta[selection_date]" value="<?php echo isset( $term_meta['selection_date'] ) ? esc_attr( $term_meta['selection_date'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the date of selection.', $SELECTION_TEXTDOMAIN); ?></p>
    </td>
  </tr>
  <?php
}

add_action( 'selection_edit_form_fields', 'edit_selection_meta_field', 10, 2 );


/**
 * Saving custom fields in selection taxonomy
 *
 * @param int $term_id
 *
 */

function save_selection_taxonomy_custom_meta ($term_id) {
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

add_action( 'edited_selection', 'save_selection_taxonomy_custom_meta', 10, 2 );
add_action( 'create_selection', 'save_selection_taxonomy_custom_meta', 10, 2 );


/**
 * Flush rewrites when the plugin is activated
 *
 */

function selection_taxonomy_flush_rewrites() {
  flush_rewrite_rules();
}

# Prevent 404 errors on selections' archive
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
register_activation_hook( __FILE__, 'selection_taxonomy_flush_rewrites' );
add_action( 'init', 'selection_taxonomy_flush_rewrites', 0 );


/**
 * Getting term specific option
 *
 * @param object $option
 *
 * @return string
 *
 */

function get_selection_option( $option ){
  $selection = get_queried_object();
  $id        = $selection->term_id;
  $term_meta = get_option( 'taxonomy_' . $id );

  return isset( $term_meta[ $option ] ) ? $term_meta[ $option ] : false;
}

?>
