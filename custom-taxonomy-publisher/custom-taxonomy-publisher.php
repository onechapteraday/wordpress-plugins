<?php
/**
 *
 * Plugin Name: Custom Taxonomy Publisher
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin allows you to add a publisher for your custom post type books.
 * Version: 0.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */

global $BOOK_TEXTDOMAIN;

$BOOK_TEXTDOMAIN = 'book-taxonomy';


/**
 * Add publisher taxonomy
 *
 **/

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
    'hierarchical'          => true,
    'labels'                => $labels,
    'show_ui'               => true,
    'show_admin_column'     => true,
    'update_count_callback' => '_update_post_term_count',
    'query_var'             => true,
    'rewrite'               => array( 'slug' => 'book/publisher', 'with_front' => 'true', 'hierarchical' => true ),
  );

  register_taxonomy ('publisher', 'book', $args);
}

add_action ('init', 'add_publisher_taxonomy', 1);


/**
 * Add custom field link
 *
 **/

function add_new_publisher_meta_field() {
  global $BOOK_TEXTDOMAIN;

  # This will add the custom meta fields to the 'Add new term' page
  ?>
  <div class="form-field">
    <label for="term_meta[publisher_link]"><?php _e( 'Website link', $BOOK_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[publisher_link]" id="term_meta[publisher_link]" value="">
    <p class="description"><?php _e( 'Enter the website link of the publisher.', $BOOK_TEXTDOMAIN ); ?></p>
  </div>
  <?php
}

add_action( 'publisher_add_form_fields', 'add_new_publisher_meta_field', 10, 2 );


/**
 * Editing custom fields in publisher taxonomy
 *
 * @param object $term
 *
 */

function edit_publisher_meta_field ($term) {
  global $BOOK_TEXTDOMAIN;

  # Put the term ID into a variable
  $t_id = $term->term_id;

  # Retrieve the existing values for this meta field
  # This will return an array
  $term_meta = get_option( "taxonomy_$t_id" );

  ?>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[publisher_link]"><?php _e( 'Website link', $BOOK_TEXTDOMAIN ); ?></label></th>
    <td>
    	<input type="text" name="term_meta[publisher_link]" id="term_meta[publisher_link]" value="<?php echo esc_attr( $term_meta['publisher_link'] ) ? esc_attr( $term_meta['publisher_link'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the website link of the publisher', $BOOK_TEXTDOMAIN); ?></p>
    </td>
  </tr>
  <?php
}

add_action( 'publisher_edit_form_fields', 'edit_publisher_meta_field', 10, 2 );


/**
 * Saving custom fields in publisher taxonomy
 *
 * @param int $term_id
 *
 */

function save_publisher_taxonomy_custom_meta ($term_id) {
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

add_action( 'edited_publisher', 'save_publisher_taxonomy_custom_meta', 10, 2 );
add_action( 'create_publisher', 'save_publisher_taxonomy_custom_meta', 10, 2 );


/**
 * Flush rewrites when the plugin is activated
 *
 */

function publisher_taxonomy_flush_rewrites() {
  flush_rewrite_rules();
}

# Prevent 404 errors on publishers' archive
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
register_activation_hook( __FILE__, 'publisher_taxonomy_flush_rewrites' );
add_action( 'init', 'publisher_taxonomy_flush_rewrites', 0 );

?>
