<?php
/**
 *
 * Plugin Name: Custom Taxonomy Release Item
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin allows you to add an item which will be released during a literary season.
 * Version: 0.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */

global $RELEASE_ITEM_TEXTDOMAIN;

$RELEASE_ITEM_TEXTDOMAIN = 'release-item-taxonomy';


/*
 * Make plugin available for translation.
 * Translations can be filed in the /languages directory.
 *
 */

function release_item_taxonomy_load_textdomain(){
    global $RELEASE_ITEM_TEXTDOMAIN;
    $locale = apply_filters( 'plugin_locale', get_locale(), $RELEASE_ITEM_TEXTDOMAIN );

    # Load i18n
    $path = basename( dirname( __FILE__ ) ) . '/languages/';
    $loaded = load_plugin_textdomain( $RELEASE_ITEM_TEXTDOMAIN, false, $path );
}

add_action( 'init', 'release_item_taxonomy_load_textdomain', 0 );


/*
 * Add plugin styles.
 * CSS can be found in the /css directory.
 *
 */

function release_item_add_stylesheet() {
    wp_register_style( 'release-item-styles', plugins_url( 'css/styles.css', __FILE__ ) );
    wp_enqueue_style(  'release-item-styles' );
}

add_action( 'wp_enqueue_scripts', 'release_item_add_stylesheet' );


/**
 * Add release item taxonomy
 *
 **/

function add_release_item_taxonomy(){
  global $RELEASE_ITEM_TEXTDOMAIN;

  $labels = array (
    'name'                       => _x( 'Release Items', 'taxonomy general name', $RELEASE_ITEM_TEXTDOMAIN ),
    'singular_name'              => _x( 'Release Item', 'taxonomy singular name', $RELEASE_ITEM_TEXTDOMAIN ),
    'search_items'               => __( 'Search Release Items', $RELEASE_ITEM_TEXTDOMAIN ),
    'popular_items'              => __( 'Popular Release Items', $RELEASE_ITEM_TEXTDOMAIN ),
    'all_items'                  => __( 'All Release Items', $RELEASE_ITEM_TEXTDOMAIN ),
    'parent_item'                => null,
    'parent_item_colon'          => null,
    'view_item'                  => __( 'See Release Item', $RELEASE_ITEM_TEXTDOMAIN ),
    'edit_item'                  => __( 'Edit Release Item', $RELEASE_ITEM_TEXTDOMAIN ),
    'update_item'                => __( 'Update Release Item', $RELEASE_ITEM_TEXTDOMAIN ),
    'add_new_item'               => __( 'Add New Release Item', $RELEASE_ITEM_TEXTDOMAIN ),
    'new_item_name'              => __( 'New Release Item Name', $RELEASE_ITEM_TEXTDOMAIN ),
    'separate_items_with_commas' => __( 'Separate release items with commas', $RELEASE_ITEM_TEXTDOMAIN ),
    'add_or_remove_items'        => __( 'Add or remove release items', $RELEASE_ITEM_TEXTDOMAIN ),
    'choose_from_most_used'      => __( 'Choose from the most used release items', $RELEASE_ITEM_TEXTDOMAIN ),
    'not_found'                  => __( 'No release items found.', $RELEASE_ITEM_TEXTDOMAIN ),
    'back_to_items'              => __( '← Back to release items', $RELEASE_ITEM_TEXTDOMAIN ),
    'menu_name'                  => __( 'Release Items', $RELEASE_ITEM_TEXTDOMAIN ),
  );

  $args = array (
    'hierarchical'          => false,
    'labels'                => $labels,
    'public'                => false,
    'show_ui'               => true,
    'update_count_callback' => '_update_post_term_count',
    'query_var'             => true,
    'rewrite'               => array( 'slug' => 'book/release-item', 'with_front' => 'false', 'hierarchical' => false ),
  );

  register_taxonomy ('release_item', 'book', $args);
}

add_action ('init', 'add_release_item_taxonomy', 1);


/**
 * Add custom field link
 *
 **/

function add_new_release_item_meta_field(){
  global $RELEASE_ITEM_TEXTDOMAIN;

  # This will add the custom meta fields to the 'Add new term' page
  ?>
  <div class="form-field">
    <label for="term_meta[release_item_release_name]"><?php _e( 'Release name', $RELEASE_ITEM_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[release_item_release_name]" id="term_meta[release_item_release_name]" value="">
    <p class="description"><?php _e( 'Enter the name of the release.', $RELEASE_ITEM_TEXTDOMAIN ); ?></p>
  </div>
  <div class="form-field">
    <label for="term_meta[release_item_publisher]"><?php _e( 'Publisher', $RELEASE_ITEM_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[release_item_publisher]" id="term_meta[release_item_publisher]" value="">
    <p class="description"><?php _e( 'Enter the book publisher.', $RELEASE_ITEM_TEXTDOMAIN ); ?></p>
  </div>
  <div class="form-field">
    <label for="term_meta[release_item_author]"><?php _e( 'Authors', $RELEASE_ITEM_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[release_item_author]" id="term_meta[release_item_author]" value="">
    <p class="description"><?php _e( 'Enter the book authors.', $RELEASE_ITEM_TEXTDOMAIN ); ?></p>
  </div>
  <div class="form-field">
    <label for="term_meta[release_item_translator]"><?php _e( 'Translators', $RELEASE_ITEM_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[release_item_translator]" id="term_meta[release_item_translator]" value="">
    <p class="description"><?php _e( 'Enter the book translators.', $RELEASE_ITEM_TEXTDOMAIN ); ?></p>
  </div>
  <div class="form-field">
    <label for="term_meta[release_item_title]"><?php _e( 'Title', $RELEASE_ITEM_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[release_item_title]" id="term_meta[release_item_title]" value="">
    <p class="description"><?php _e( 'Enter the book title.', $RELEASE_ITEM_TEXTDOMAIN ); ?></p>
  </div>
  <div class="form-field">
    <label for="term_meta[release_item_title_original]"><?php _e( 'Original title', $RELEASE_ITEM_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[release_item_title_original]" id="term_meta[release_item_title_original]" value="">
    <p class="description"><?php _e( 'Enter the book original title if exists.', $RELEASE_ITEM_TEXTDOMAIN ); ?></p>
  </div>
  <div class="form-field">
    <label for="term_meta[release_item_title_sort]"><?php _e( 'Sort title', $RELEASE_ITEM_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[release_item_title_sort]" id="term_meta[release_item_title_sort]" value="">
    <p class="description"><?php _e( 'Enter the book sort title.', $RELEASE_ITEM_TEXTDOMAIN ); ?></p>
  </div>
  <div class="form-field">
    <label for="term_meta[release_item_isbn10]"><?php _e( 'ISBN10', $RELEASE_ITEM_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[release_item_isbn10]" id="term_meta[release_item_isbn10]" value="">
    <p class="description"><?php _e( 'Enter the book ISBN10 if exists.', $RELEASE_ITEM_TEXTDOMAIN ); ?></p>
  </div>
  <div class="form-field">
    <label for="term_meta[release_item_isbn13]"><?php _e( 'ISBN13', $RELEASE_ITEM_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[release_item_isbn13]" id="term_meta[release_item_isbn13]" value="">
    <p class="description"><?php _e( 'Enter the book ISBN13.', $RELEASE_ITEM_TEXTDOMAIN ); ?></p>
  </div>
  <div class="form-field">
    <label for="term_meta[release_item_asin]"><?php _e( 'ASIN', $RELEASE_ITEM_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[release_item_asin]" id="term_meta[release_item_asin]" value="">
    <p class="description"><?php _e( 'Enter the book ASIN.', $RELEASE_ITEM_TEXTDOMAIN ); ?></p>
  </div>
  <div class="form-field">
    <label for="term_meta[release_item_release_date]"><?php _e( 'Release date', $RELEASE_ITEM_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[release_item_release_date]" id="term_meta[release_item_isbn13]" value="">
    <p class="description"><?php _e( 'Enter the book release date (YYYY-mm-dd).', $RELEASE_ITEM_TEXTDOMAIN ); ?></p>
  </div>
  <div class="form-field">
    <label for="term_meta[release_item_pagenumber]"><?php _e( 'Number of pages', $RELEASE_ITEM_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[release_item_pagenumber]" id="term_meta[release_item_pagenumber]" value="">
    <p class="description"><?php _e( 'Enter the book number of pages.', $RELEASE_ITEM_TEXTDOMAIN ); ?></p>
  </div>
  <div class="form-field">
    <label for="term_meta[release_item_price]"><?php _e( 'Price', $RELEASE_ITEM_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[release_item_price]" id="term_meta[release_item_price]" value="">
    <p class="description"><?php _e( 'Enter the book price.', $RELEASE_ITEM_TEXTDOMAIN ); ?></p>
  </div>
  <div class="form-field">
    <label for="term_meta[release_item_thumbnail]"><?php _e( 'Thumbnail', $RELEASE_ITEM_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[release_item_thumbnail]" id="term_meta[release_item_thumbnail]" value="">
    <p class="description"><?php _e( 'Enter the book thumbnail if exists.', $RELEASE_ITEM_TEXTDOMAIN ); ?></p>
  </div>
  <div class="form-field">
    <label for="term_meta[release_item_excerpt]"><?php _e( 'Excerpt', $RELEASE_ITEM_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[release_item_excerpt]" id="term_meta[release_item_excerpt]" value="">
    <p class="description"><?php _e( 'Enter the book excerpt if exists.', $RELEASE_ITEM_TEXTDOMAIN ); ?></p>
  </div>
  <div class="form-field">
    <label for="term_meta[release_item_post_link]"><?php _e( 'Post linked', $RELEASE_ITEM_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[release_item_post_link]" id="term_meta[release_item_post_link]" value="">
    <p class="description"><?php _e( 'Enter the book post link if exists.', $RELEASE_ITEM_TEXTDOMAIN ); ?></p>
  </div>
  <?php
}

add_action( 'release_item_add_form_fields', 'add_new_release_item_meta_field', 10, 2 );


/**
 * Editing custom fields in release item taxonomy
 *
 * @param object $term
 *
 */

function edit_release_item_meta_field ($term) {
  global $RELEASE_ITEM_TEXTDOMAIN;

  # Put the term ID into a variable
  $t_id = $term->term_id;

  # Retrieve the existing values for this meta field
  # This will return an array
  $term_meta = get_option( "taxonomy_$t_id" );

  ?>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[release_item_release_name]"><?php _e( 'Release name', $RELEASE_ITEM_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[release_item_release_name]" id="term_meta[release_item_release_name]" value="<?php echo esc_attr( $term_meta['release_item_release_name'] ) ? esc_attr( $term_meta['release_item_release_name'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the name of the release.', $RELEASE_ITEM_TEXTDOMAIN ); ?></p>
    </td>
  </tr>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[release_item_publisher]"><?php _e( 'Publisher', $RELEASE_ITEM_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[release_item_publisher]" id="term_meta[release_item_publisher]" value="<?php echo esc_attr( $term_meta['release_item_publisher'] ) ? esc_attr( $term_meta['release_item_publisher'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the book publisher.', $RELEASE_ITEM_TEXTDOMAIN ); ?></p>
    </td>
  </tr>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[release_item_author]"><?php _e( 'Authors', $RELEASE_ITEM_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[release_item_author]" id="term_meta[release_item_author]" value="<?php echo esc_attr( $term_meta['release_item_author'] ) ? esc_attr( $term_meta['release_item_author'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the book authors.', $RELEASE_ITEM_TEXTDOMAIN ); ?></p>
    </td>
  </tr>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[release_item_translator]"><?php _e( 'Translators', $RELEASE_ITEM_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[release_item_translator]" id="term_meta[release_item_translator]" value="<?php echo esc_attr( $term_meta['release_item_translator'] ) ? esc_attr( $term_meta['release_item_translator'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the book translators.', $RELEASE_ITEM_TEXTDOMAIN ); ?></p>
    </td>
  </tr>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[release_item_title]"><?php _e( 'Title', $RELEASE_ITEM_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[release_item_title]" id="term_meta[release_item_title]" value="<?php echo esc_attr( $term_meta['release_item_title'] ) ? esc_attr( $term_meta['release_item_title'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the book title.', $RELEASE_ITEM_TEXTDOMAIN ); ?></p>
    </td>
  </tr>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[release_item_title_original]"><?php _e( 'Original title', $RELEASE_ITEM_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[release_item_title_original]" id="term_meta[release_item_title_original]" value="<?php echo esc_attr( $term_meta['release_item_title_original'] ) ? esc_attr( $term_meta['release_item_title_original'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the book original title.', $RELEASE_ITEM_TEXTDOMAIN ); ?></p>
    </td>
  </tr>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[release_item_title_sort]"><?php _e( 'Sort title', $RELEASE_ITEM_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[release_item_title_sort]" id="term_meta[release_item_title_sort]" value="<?php echo esc_attr( $term_meta['release_item_title_sort'] ) ? esc_attr( $term_meta['release_item_title_sort'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the book sort title.', $RELEASE_ITEM_TEXTDOMAIN ); ?></p>
    </td>
  </tr>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[release_item_isbn10]"><?php _e( 'ISBN10', $RELEASE_ITEM_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[release_item_isbn10]" id="term_meta[release_item_isbn10]" value="<?php echo esc_attr( $term_meta['release_item_isbn10'] ) ? esc_attr( $term_meta['release_item_isbn10'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the book ISBN10 if exists.', $RELEASE_ITEM_TEXTDOMAIN ); ?></p>
    </td>
  </tr>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[release_item_isbn13]"><?php _e( 'ISBN13', $RELEASE_ITEM_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[release_item_isbn13]" id="term_meta[release_item_isbn13]" value="<?php echo esc_attr( $term_meta['release_item_isbn13'] ) ? esc_attr( $term_meta['release_item_isbn13'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the book ISBN13.', $RELEASE_ITEM_TEXTDOMAIN ); ?></p>
    </td>
  </tr>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[release_item_asin]"><?php _e( 'ASIN', $RELEASE_ITEM_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[release_item_asin]" id="term_meta[release_item_asin]" value="<?php echo esc_attr( $term_meta['release_item_asin'] ) ? esc_attr( $term_meta['release_item_asin'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the book ASIN.', $RELEASE_ITEM_TEXTDOMAIN ); ?></p>
    </td>
  </tr>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[release_item_release_date]"><?php _e( 'Release date', $RELEASE_ITEM_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[release_item_release_date]" id="term_meta[release_item_release_date]" value="<?php echo esc_attr( $term_meta['release_item_release_date'] ) ? esc_attr( $term_meta['release_item_release_date'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the book release date (YYYY-mm-dd).', $RELEASE_ITEM_TEXTDOMAIN ); ?></p>
    </td>
  </tr>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[release_item_pagenumber]"><?php _e( 'Number of pages', $RELEASE_ITEM_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[release_item_pagenumber]" id="term_meta[release_item_pagenumber]" value="<?php echo esc_attr( $term_meta['release_item_pagenumber'] ) ? esc_attr( $term_meta['release_item_pagenumber'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the book number of pages.', $RELEASE_ITEM_TEXTDOMAIN ); ?></p>
    </td>
  </tr>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[release_item_price]"><?php _e( 'Price', $RELEASE_ITEM_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[release_item_price]" id="term_meta[release_item_price]" value="<?php echo esc_attr( $term_meta['release_item_price'] ) ? esc_attr( $term_meta['release_item_price'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the book price.', $RELEASE_ITEM_TEXTDOMAIN ); ?></p>
    </td>
  </tr>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[release_item_thumbnail]"><?php _e( 'Thumbnail', $RELEASE_ITEM_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[release_item_thumbnail]" id="term_meta[release_item_thumbnail]" value="<?php echo esc_attr( $term_meta['release_item_thumbnail'] ) ? esc_attr( $term_meta['release_item_thumbnail'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the book thumbnail if exists.', $RELEASE_ITEM_TEXTDOMAIN ); ?></p>
    </td>
  </tr>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[release_item_excerpt]"><?php _e( 'Excerpt', $RELEASE_ITEM_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[release_item_excerpt]" id="term_meta[release_item_excerpt]" value="<?php echo isset( $term_meta['release_item_excerpt'] ) ? esc_attr( $term_meta['release_item_excerpt'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the book excerpt if exists.', $RELEASE_ITEM_TEXTDOMAIN ); ?></p>
    </td>
  </tr>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[release_item_post_link]"><?php _e( 'Post linked', $RELEASE_ITEM_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[release_item_post_link]" id="term_meta[release_item_post_link]" value="<?php echo esc_attr( $term_meta['release_item_post_link'] ) ? esc_attr( $term_meta['release_item_post_link'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the book post linked if exists.', $RELEASE_ITEM_TEXTDOMAIN ); ?></p>
    </td>
  </tr>
  <?php
}

add_action( 'release_item_edit_form_fields', 'edit_release_item_meta_field', 10, 2 );


/**
 * Saving custom fields in release item taxonomy
 *
 * @param int $term_id
 *
 */

function save_release_item_taxonomy_custom_meta( $term_id ){
    if( isset( $_POST['term_meta'] ) ){
        $t_id      = $term_id;
        $term_meta = get_option( "taxonomy_$t_id" );
        $cat_keys  = array_keys( $_POST['term_meta'] );

        foreach( $cat_keys as $key ){
            if( isset( $_POST['term_meta'][$key] ) ){
                $term_meta[$key] = $_POST['term_meta'][$key];
            }
        }

        # Save the option array
        update_option( "taxonomy_$t_id", $term_meta );
    }
}

add_action( 'edited_release_item', 'save_release_item_taxonomy_custom_meta', 10, 2 );
add_action( 'create_release_item', 'save_release_item_taxonomy_custom_meta', 10, 2 );


/**
 * Flush rewrites when the plugin is activated
 *
 */

function release_item_taxonomy_flush_rewrites(){
  flush_rewrite_rules();
}

# Prevent 404 errors on release items' archive
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
register_activation_hook( __FILE__, 'release_item_taxonomy_flush_rewrites' );
add_action( 'init', 'release_item_taxonomy_flush_rewrites', 0 );


/**
 * Getting term specific option
 *
 * @param object $option
 *
 * @return string
 *
 */

function get_release_item_option( $option ){
  $item = get_queried_object();
  $id   = $item->term_id;
  $term_meta = get_option( 'taxonomy_' . $id );

  return $term_meta[$option];
}


/**
 * Create literary season shortcode
 *
 * @param array  $atts
 * @param string $content
 *
 */

function display_literary_season( $atts, $content=null ){
    global $wpdb;
    global $RELEASE_ITEM_TEXTDOMAIN;

    # Retrieve release name and publisher

    $release_name      = $atts['release_name'];
    $release_publisher = $atts['publisher'];

    # Check publisher and children

    $release_publisher_obj          = get_term_by( 'slug', $release_publisher, 'publisher' );
    $release_publisher_children     = get_term_children( $release_publisher_obj->term_id, 'publisher' );
    $release_publisher_obj_count    = 0;

    $release_publisher_slugs = array();
    array_push( $release_publisher_slugs, $release_publisher );

    foreach( $release_publisher_children as $child ){
        $term = get_term_by( 'id', $child, 'publisher' );
        array_push( $release_publisher_slugs, $term->slug );

        if( $term->count > 0 ){
            $release_publisher_obj_count = 1;
        }
    }

    # Get all release items

    $items  = get_terms( array(
                          'taxonomy'   => 'release_item',
                          'hide_empty' => 'false',
                          'get'        => 'all',
                      ) );

    $items_selected = array();

    foreach( $items as $item ){
        $item_options = get_option( 'taxonomy_' . $item->term_id );

        if( $item_options['release_item_release_name'] == $release_name &&
            in_array( $item_options['release_item_publisher'], $release_publisher_slugs ) ){
            array_push( $items_selected, $item );
        }
    }

    # Sort by date, author

    # Display all data

    ob_start();

    ?>
    <div class="literary-season">
    <?php

    foreach( $items_selected as $item ){
        $item_options = get_option( 'taxonomy_' . $item->term_id );

        # Organize data
        $publisher      = $item_options['release_item_publisher'];
        $authors        = $item_options['release_item_author'];
        $translators    = $item_options['release_item_translator'];
        $title          = $item_options['release_item_title'];
        $title_sort     = $item_options['release_item_title_sort'];
        $title_original = $item_options['release_item_title_original'];
        $isbn10         = $item_options['release_item_isbn10'];
        $isbn13         = $item_options['release_item_isbn13'];
        $asin           = $item_options['release_item_asin'];
        $release_date   = $item_options['release_item_release_date'];
        $pagenumber     = $item_options['release_item_pagenumber'];
        $price          = $item_options['release_item_price'];
        $post_link      = $item_options['release_item_post_link'];
        $thumbnail      = $item_options['release_item_thumbnail'];
        $book_excerpt   = ( isset( $item_options['release_item_excerpt'] ) ) ? $item_options['release_item_excerpt'] : '';
        $description    = $item->description;

        # Find authors

        $author_displayed = '';

        if( taxonomy_exists( 'person' ) ){
            $auth    = array();
            $authors = explode( ',', $authors );

            foreach( $authors as $person ){
              $person_obj = get_term_by( 'slug', $person, 'person' );

              if( $person_obj ){
                array_push( $auth, $person_obj );
              }
            }
        }

        # Display title and authors if exist
        if( !empty( $auth ) ){
            foreach( $auth as $author ){
                if( $author_displayed != '' ){
                    $author_displayed .= ' &sdot; ';
                }

                $author_displayed .= str_replace( ' ', '&nbsp;', str_replace( '-', '-&#8288;', $author->name ) );
            }
        }

        # Display translators

        if( taxonomy_exists( 'person' ) ){
            $transl      = array();
            $translators = explode( ',', $translators );

            foreach( $translators as $person ){
              $person_obj = get_term_by( 'slug', $person, 'person' );

              if( $person_obj ){
                array_push( $transl, $person_obj );
              }
            }
        }

        ?>
        <div class="book-release-item">

        <h3 class="book-release-item-title">
            <span class="book-infos">
            <?php
            echo '<cite class="book-release-item-title-read">' . $title . '</cite>';

            if( !empty( $author_displayed ) ){
                if( preg_match( '/^[aieéouAIEÉOU].*/', $author_displayed ) || preg_match( '/^Y[bcdfghjklmnpqrstvwxz].*/', $author_displayed ) ){
                    echo '<span class="book-release-item-pronoun"> d’</span>';
                } else {
                    echo '<span class="book-release-item-pronoun"> de </span>';
                }
            }

            echo '<span class="book-release-item-author">' . $author_displayed . '</span>';
            ?>
            </span>
        </h3>
        <div class="book-release-item-excerpts">
            <?php
            if( !empty( $book_excerpt )){
                ?>
                <span class="book-excerpt">[<a href="<?php echo $book_excerpt; ?>" target="_blank" rel="noopener nofollow">extrait</a>]</span>
                <?php
            }
            ?>
            <?php
            if( !empty( $post_link )){
                ?>
                <span class="book-review">[<a href="<?php echo $post_link; ?>">chronique</a>]</span>
                <?php
            }
            ?>
        </div>
        <?php

        # Display thumbnail

        ?>
        <div class="book-release-item-card">
            <figure>
            <?php
                if( !empty( $thumbnail ) ){
                    echo ( !empty( $post_link ) ? '<a href="'. $post_link .'">' : '' );
                    echo '<img src="' . $thumbnail . '" alt="' . $title . '" />';
                    echo ( !empty( $post_link ) ? '</a>' : '' );
                } else {
                    echo ( !empty( $post_link ) ? '<a href="'. $post_link .'">' : '' );
                    echo '<img src="https://onechapteraday.fr/wp-content/uploads/2020/06/onechapt_cover_artwork.jpg" alt="" title="La première de couverture de ' . $title . ' n\'est pas encore connue." width="250" height="375" />';
                    echo ( !empty( $post_link ) ? '</a>' : '' );
                }
            ?>
            </figure><!--

            --><div class="book-card">
                <table>
                <?php

                    # Display titles

                    if( !empty( $title ) ){
                       ?>
                       <tr>
                           <td><?php echo _e( 'Title', $RELEASE_ITEM_TEXTDOMAIN ); ?></td>
                           <td><?php echo $title; ?></td>
                       </tr>
                       <?php
                    }

                    if( !empty( $title_original ) && ( ( $title != $title_original ) || ( count( $transl ) > 0 ) ) ){
                       ?>
                       <tr>
                           <td><?php echo _e( 'Original title', $RELEASE_ITEM_TEXTDOMAIN ); ?></td>
                           <td><?php echo $title_original; ?></td>
                       </tr>
                       <?php
                    }

                    # Display authors

                    # If at least one author

                    if( count( $auth ) > 0 ){
                        ?>
                        <tr>
                            <td>
                            <?php
                            if( count( $auth ) > 1 ){
                                $female_only = true;
                                $transgenre_only = true;

                                foreach( $auth as $author ){
                                    $gender = get_option( 'taxonomy_' . $author->term_id )['gender'];

                                    if( $gender == 0 ){
                                        $female_only = false;
                                        break;
                                    }
                                }

                                if( $female_only ){
                                    echo _x( 'Authors', 'book metadata female authors', $RELEASE_ITEM_TEXTDOMAIN );
                                } else {
                                    echo _x( 'Authors', 'book metadata authors', $RELEASE_ITEM_TEXTDOMAIN );
                                }

                            } else {
                                $gender = get_option( 'taxonomy_' . $auth[0]->term_id )['gender'];

                                if( $gender == 1 ){
                                    echo _x( 'Author', 'book metadata female author', $RELEASE_ITEM_TEXTDOMAIN );
                                } elseif( $gender == 2 ) {
                                    echo _x( 'Author', 'book metadata transgenre author', $RELEASE_ITEM_TEXTDOMAIN );
                                } else {
                                    echo _x( 'Author', 'book metadata male author', $RELEASE_ITEM_TEXTDOMAIN );
                                }
                            }
                            ?>
                            </td>
                            <td>
                            <?php

                            $count = 0;

                            foreach( $auth as $author ){
                                if( $count > 0 ) echo ' &sdot; ';

                                echo '<a href="' . get_term_link( $author->term_id ) . '">' . $author->name . '</a>';
                                $count++;
                            }

                            ?>
                            </td>
                        </tr>
                        <?php
                    }

                    # If at least one translator

                    if( count( $transl ) > 0 ){
                        ?>
                        <tr>
                            <td>
                            <?php
                            if( count( $transl ) > 1 ){
                                $female_only = true;

                                foreach( $transl as $translator ){
                                    $gender = get_option( 'taxonomy_' . $translator->term_id )['gender'];

                                    if( $gender == 0 ){
                                        $female_only = false;
                                        break;
                                    }
                                }

                                if( $female_only ){
                                    echo _x( 'Translators', 'book metadata female translators', $RELEASE_ITEM_TEXTDOMAIN );
                                } else {
                                    echo _x( 'Translators', 'book metadata translators', $RELEASE_ITEM_TEXTDOMAIN );
                                }

                            } else {
                                $gender = get_option( 'taxonomy_' . $transl[0]->term_id )['gender'];

                                if( $gender == 1 ){
                                    echo _x( 'Translator', 'book metadata female translator', $RELEASE_ITEM_TEXTDOMAIN );
                                } else {
                                    echo _x( 'Translator', 'book metadata male translator', $RELEASE_ITEM_TEXTDOMAIN );
                                }
                            }
                            ?>
                            </td>
                            <td>
                            <?php

                            $count = 0;

                            foreach( $transl as $translator ){
                                if( $count > 0 ) echo ' &sdot; ';

                                echo '<a href="' . get_term_link( $translator->term_id ) . '">' . $translator->name . '</a>';
                                $count++;
                            }

                            ?>
                            </td>
                        </tr>
                        <?php
                    }

                    # Display pub, coll

                    if( !empty( $release_publisher_obj ) ){
                       $release_publisher_obj_link     = get_term_link( $release_publisher_obj->term_id, 'publisher' );

                       ?>
                       <tr>
                           <td><?php echo _e( 'Publisher', $RELEASE_ITEM_TEXTDOMAIN ); ?></td>
                           <td>
                               <?php
                               if( $release_publisher_obj->count > 0 || $release_publisher_obj_count > 0 ){
                                   ?>
                                   <a href="<?php echo $release_publisher_obj_link; ?>"><?php echo $release_publisher_obj->name; ?></a>
                                   <?php
                               } else {
                                   echo $release_publisher_obj->name;
                               }
                               ?>
                           </td>
                       </tr>
                       <?php
                    }

                    $real_pub = get_term_by( 'slug', $publisher, 'publisher' );
                    $real_pub_link = get_term_link( $real_pub->term_id, 'publisher' );

                    if( $real_pub->parent > 0 ){
                       ?>
                       <tr>
                           <td><?php echo _e( 'Collection', $RELEASE_ITEM_TEXTDOMAIN ); ?></td>
                           <td>
                               <?php
                               if( $real_pub->count > 0 ){
                                   ?>
                                   <a href="<?php echo $real_pub_link; ?>"><?php echo $real_pub->name; ?></a>
                                   <?php
                               } else {
                                   echo $real_pub->name;
                               }
                               ?>
                           </td>
                       </tr>
                       <?php
                    }

                    # Display book data

                    if( !empty( $isbn13 ) ){
                       ?>
                       <tr>
                           <td><?php echo _e( 'ISBN13', $RELEASE_ITEM_TEXTDOMAIN ); ?></td>
                           <td><?php echo $isbn13; ?></td>
                       </tr>
                       <?php
                    }

                    if( !empty( $isbn10 ) ){
                       ?>
                       <tr>
                           <td><?php echo _e( 'ISBN10', $RELEASE_ITEM_TEXTDOMAIN ); ?></td>
                           <td><?php echo $isbn10; ?></td>
                       </tr>
                       <?php
                    }

                    if( !empty( $release_date ) ){
                       ?>
                       <tr>
                           <td><?php echo _e( 'Release date', $RELEASE_ITEM_TEXTDOMAIN ); ?></td>
                           <td><?php echo date_i18n( 'j F Y', strtotime( $release_date ) ); ?></td>
                       </tr>
                       <?php
                    }

                    if( !empty( $price ) ){
                       ?>
                       <tr>
                           <td><?php echo _e( 'Estimated price', $RELEASE_ITEM_TEXTDOMAIN ); ?></td>
                           <td><?php echo $price . ' &euro;'; ?></td>
                       </tr>
                       <?php
                    }

                    if( !empty( $pagenumber ) ){
                       ?>
                       <tr>
                           <td><?php echo _e( 'Number of pages', $RELEASE_ITEM_TEXTDOMAIN ); ?></td>
                           <td><?php echo $pagenumber; ?></td>
                       </tr>
                       <?php
                    }

                    if( !empty( $isbn13 ) || !empty( $asin ) ){
                       ?>
                       <tr class="affiliate-links">
                           <td>
                               <?php
                               if( $asin && $isbn13 ){
                                   ?>
			           <b><?php echo _x( 'Affiliate links', 'book metadata affiliate links', $RELEASE_ITEM_TEXTDOMAIN ); ?></b>
                                   <?php
                               } else {
                                   ?>
			           <b><?php echo _x( 'Affiliate link', 'book metadata affiliate link', $RELEASE_ITEM_TEXTDOMAIN ); ?></b>
                                   <?php
                               }
                               ?>
                           </td>
                           <td>
                               <?php
                               if( !empty( $isbn13 ) ){
                                   ?>
			           <a href="https://www.leslibraires.fr/livre/<?php echo $isbn13; ?>?affiliate=onechapteraday" target="_blank" rel="nofollow" class="logo_partner logo_libraires_fr">
			                <img src="https://onechapteraday.fr/wp-content/plugins/custom-post-type-book/images/logo_leslibraires_tooltip.png" alt="leslibraires.fr" />
                                        <span><?php echo _x( 'Buy on Les Libraires', 'book metadata leslibraires.fr affiliate message', $RELEASE_ITEM_TEXTDOMAIN ); ?></span>
			           </a>
                                   <?php
                               }
                               ?>
                               <?php
                               if( !empty( $asin ) ){
                                   ?>
			           <a href="https://www.amazon.fr/dp/<?php echo $asin; ?>/?tag=onchada-21" target="_blank" rel="nofollow" class="logo_partner logo_amazon">
			                <img src="https://onechapteraday.fr/wp-content/plugins/custom-post-type-book/images/logo_amazon_buy.png" alt="Amazon" />
                                        <span><?php echo _x( 'Buy on Amazon', 'book metadata Amazon affiliate message', $RELEASE_ITEM_TEXTDOMAIN ); ?></span>
			           </a>
                                   <?php
                               }
                               ?>
                           </td>
                       </tr>
                       <?php
                    }

                ?>
                </table>
            </div>
        </div>
        <?php

        # Display description

        if( !empty( $description ) ){

            ?>
            <div class="publisher-description">
                <span><?php echo _e( 'Publisher description', $RELEASE_ITEM_TEXTDOMAIN ); ?>&nbsp;:</span>
                <div>
                    <?php
                    $description = str_replace("\n\r", "</p>\n<p>", '<p>'.$description.'</p>');
                    $description = str_replace("\r", "<br />", '<p>'.$description.'</p>');

                    echo $description;
                    ?>
                </div>
            </div>
            <?php
        }

        ?>
        </div> <!-- .book-release-item -->
        <?php

    }

    ?>
    </div> <!-- .literary-season -->
    <?php

    return ob_get_clean();
}

add_shortcode( 'wp_literary_season', 'display_literary_season' );

?>
