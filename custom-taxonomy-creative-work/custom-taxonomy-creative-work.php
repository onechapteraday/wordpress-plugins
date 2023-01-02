<?php
/**
 *
 * Plugin Name: Custom Taxonomy Creative Work
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin allows you to create creative works.
 * Version: 0.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */

global $CREATIVE_WORK_TEXTDOMAIN;

$CREATIVE_WORK_TEXTDOMAIN = 'creative-work-taxonomy';


/*
 * Make plugin available for translation.
 * Translations can be filed in the /languages directory.
 *
 */

function creative_work_taxonomy_load_textdomain(){
    global $CREATIVE_WORK_TEXTDOMAIN;
    $locale = apply_filters( 'plugin_locale', get_locale(), $CREATIVE_WORK_TEXTDOMAIN );

    # Load i18n
    $path = basename( dirname( __FILE__ ) ) . '/languages/';
    $loaded = load_plugin_textdomain( $CREATIVE_WORK_TEXTDOMAIN, false, $path );
}

add_action( 'init', 'creative_work_taxonomy_load_textdomain', 0 );


/**
 * Add creative work taxonomy
 *
 **/

function add_creative_work_taxonomy(){
  global $CREATIVE_WORK_TEXTDOMAIN;

  $labels = array (
    'name'                       => _x( 'Creative Works', 'taxonomy general name', $CREATIVE_WORK_TEXTDOMAIN ),
    'singular_name'              => _x( 'Creative Work', 'taxonomy singular name', $CREATIVE_WORK_TEXTDOMAIN ),
    'search_items'               => __( 'Search Creative Works', $CREATIVE_WORK_TEXTDOMAIN ),
    'popular_items'              => __( 'Popular Creative Works', $CREATIVE_WORK_TEXTDOMAIN ),
    'all_items'                  => __( 'All Creative Works', $CREATIVE_WORK_TEXTDOMAIN ),
    'parent_item'                => null,
    'parent_item_colon'          => null,
    'view_item'                  => __( 'See Creative Work', $CREATIVE_WORK_TEXTDOMAIN ),
    'edit_item'                  => __( 'Edit Creative Work', $CREATIVE_WORK_TEXTDOMAIN ),
    'update_item'                => __( 'Update Creative Work', $CREATIVE_WORK_TEXTDOMAIN ),
    'add_new_item'               => __( 'Add New Creative Work', $CREATIVE_WORK_TEXTDOMAIN ),
    'new_item_name'              => __( 'New Creative Work Name', $CREATIVE_WORK_TEXTDOMAIN ),
    'separate_items_with_commas' => __( 'Separate creative works with commas', $CREATIVE_WORK_TEXTDOMAIN ),
    'add_or_remove_items'        => __( 'Add or remove creative works', $CREATIVE_WORK_TEXTDOMAIN ),
    'choose_from_most_used'      => __( 'Choose from the most used creative works', $CREATIVE_WORK_TEXTDOMAIN ),
    'not_found'                  => __( 'No creative works found.', $CREATIVE_WORK_TEXTDOMAIN ),
    'back_to_items'              => __( '← Back to creative works', $CREATIVE_WORK_TEXTDOMAIN ),
    'menu_name'                  => __( 'Creative Works', $CREATIVE_WORK_TEXTDOMAIN ),
  );

  $args = array (
    'hierarchical'          => false,
    'labels'                => $labels,
    'public'                => false,
    'show_ui'               => true,
    'update_count_callback' => '_update_post_term_count',
    'query_var'             => true,
    'rewrite'               => array( 'slug' => 'creative-work', 'with_front' => 'false', 'hierarchical' => false ),
  );

  register_taxonomy ('creative_work', array('book', 'album'), $args);
}

add_action ('init', 'add_creative_work_taxonomy', 1);


/**
 * Add custom field link
 *
 **/

function add_new_creative_work_meta_field(){
  global $CREATIVE_WORK_TEXTDOMAIN;

  # This will add the custom meta fields to the 'Add new term' page
  ?>
  <div class="form-field">
    <label for="term_meta[creative_work_title]"><?php _e( 'Title', $CREATIVE_WORK_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[creative_work_title]" id="term_meta[creative_work_title]" value="">
    <p class="description"><?php _e( 'Enter the work title.', $CREATIVE_WORK_TEXTDOMAIN ); ?></p>
  </div>
  <div class="form-field">
    <label for="term_meta[creative_work_title_sort]"><?php _e( 'Sort title', $CREATIVE_WORK_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[creative_work_title_sort]" id="term_meta[creative_work_title_sort]" value="">
    <p class="description"><?php _e( 'Enter the work sort title.', $CREATIVE_WORK_TEXTDOMAIN ); ?></p>
  </div>
  <div class="form-field">
    <label for="term_meta[creative_work_translation]"><?php _e( 'Translations', $CREATIVE_WORK_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[creative_work_translation]" id="term_meta[creative_work_translation]" value="">
    <p class="description"><?php _e( 'Enter the work translations if exist.', $CREATIVE_WORK_TEXTDOMAIN ); ?></p>
  </div>
  <div class="form-field">
    <label for="term_meta[creative_work_translation_of]"><?php _e( 'Translation of', $CREATIVE_WORK_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[creative_work_translation_of]" id="term_meta[creative_work_translation_of]" value="">
    <p class="description"><?php _e( 'Enter the work from which this work has been translated if exists.', $CREATIVE_WORK_TEXTDOMAIN ); ?></p>
  </div>
  <div class="form-field">
    <label for="term_meta[creative_work_author]"><?php _e( 'Authors', $CREATIVE_WORK_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[creative_work_author]" id="term_meta[creative_work_author]" value="">
    <p class="description"><?php _e( 'Enter the work authors.', $CREATIVE_WORK_TEXTDOMAIN ); ?></p>
  </div>
  <div class="form-field">
    <label for="term_meta[creative_work_translator]"><?php _e( 'Translators', $CREATIVE_WORK_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[creative_work_translator]" id="term_meta[creative_work_translator]" value="">
    <p class="description"><?php _e( 'Enter the work translators if exist.', $CREATIVE_WORK_TEXTDOMAIN ); ?></p>
  </div>
  <div class="form-field">
    <label for="term_meta[creative_work_publisher]"><?php _e( 'Publisher', $CREATIVE_WORK_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[creative_work_publisher]" id="term_meta[creative_work_publisher]" value="">
    <p class="description"><?php _e( 'Enter the work publisher.', $CREATIVE_WORK_TEXTDOMAIN ); ?></p>
  </div>
  <div class="form-field">
    <label for="term_meta[creative_work_isbn10]"><?php _e( 'ISBN10', $CREATIVE_WORK_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[creative_work_isbn10]" id="term_meta[creative_work_isbn10]" value="">
    <p class="description"><?php _e( 'Enter the work ISBN10 if exists.', $CREATIVE_WORK_TEXTDOMAIN ); ?></p>
  </div>
  <div class="form-field">
    <label for="term_meta[creative_work_isbn13]"><?php _e( 'ISBN13', $CREATIVE_WORK_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[creative_work_isbn13]" id="term_meta[creative_work_isbn13]" value="">
    <p class="description"><?php _e( 'Enter the work ISBN13.', $CREATIVE_WORK_TEXTDOMAIN ); ?></p>
  </div>
  <div class="form-field">
    <label for="term_meta[creative_work_asin]"><?php _e( 'ASIN', $CREATIVE_WORK_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[creative_work_asin]" id="term_meta[creative_work_asin]" value="">
    <p class="description"><?php _e( 'Enter the work ASIN.', $CREATIVE_WORK_TEXTDOMAIN ); ?></p>
  </div>
  <div class="form-field">
    <label for="term_meta[creative_work_release_date]"><?php _e( 'Release date', $CREATIVE_WORK_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[creative_work_release_date]" id="term_meta[creative_work_release_date]" value="">
    <p class="description"><?php _e( 'Enter the work release date (YYYY-mm-dd).', $CREATIVE_WORK_TEXTDOMAIN ); ?></p>
  </div>
  <div class="form-field">
    <label for="term_meta[creative_work_language]"><?php _e( 'Language', $CREATIVE_WORK_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[creative_work_language]" id="term_meta[creative_work_language]" value="">
    <p class="description"><?php _e( 'Enter the language in which the work is written.', $CREATIVE_WORK_TEXTDOMAIN ); ?></p>
  </div>
  <div class="form-field">
    <label for="term_meta[creative_work_pagenumber]"><?php _e( 'Number of pages', $CREATIVE_WORK_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[creative_work_pagenumber]" id="term_meta[creative_work_pagenumber]" value="">
    <p class="description"><?php _e( 'Enter the work number of pages.', $CREATIVE_WORK_TEXTDOMAIN ); ?></p>
  </div>
  <div class="form-field">
    <label for="term_meta[creative_work_cover]"><?php _e( 'Cover', $CREATIVE_WORK_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[creative_work_cover]" id="term_meta[creative_work_cover]" value="">
    <p class="description"><?php _e( 'Enter the work cover if exists.', $CREATIVE_WORK_TEXTDOMAIN ); ?></p>
  </div>
  <div class="form-field">
    <label for="term_meta[creative_work_location]"><?php _e( 'Location', $CREATIVE_WORK_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[creative_work_location]" id="term_meta[creative_work_location]" value="">
    <p class="description"><?php _e( 'Enter the locations depicted or described in the work if exist.', $CREATIVE_WORK_TEXTDOMAIN ); ?></p>
  </div>
  <div class="form-field">
    <label for="term_meta[creative_work_review]"><?php _e( 'Work review', $CREATIVE_WORK_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[creative_work_review]" id="term_meta[creative_work_review]" value="">
    <p class="description"><?php _e( 'Enter the work review if exists.', $CREATIVE_WORK_TEXTDOMAIN ); ?></p>
  </div>
  <?php
}

add_action( 'creative_work_add_form_fields', 'add_new_creative_work_meta_field', 10, 2 );


/**
 * Editing custom fields in creative work taxonomy
 *
 * @param object $term
 *
 */

function edit_creative_work_meta_field ($term) {
  global $CREATIVE_WORK_TEXTDOMAIN;

  # Put the term ID into a variable
  $t_id = $term->term_id;

  # Retrieve the existing values for this meta field
  # This will return an array
  $term_meta = get_option( "taxonomy_$t_id" );

  ?>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[creative_work_title]"><?php _e( 'Title', $CREATIVE_WORK_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[creative_work_title]" id="term_meta[creative_work_title]" value="<?php echo esc_attr( $term_meta['creative_work_title'] ) ? esc_attr( $term_meta['creative_work_title'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the work title.', $CREATIVE_WORK_TEXTDOMAIN ); ?></p>
    </td>
  </tr>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[creative_work_title_sort]"><?php _e( 'Sort title', $CREATIVE_WORK_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[creative_work_title_sort]" id="term_meta[creative_work_title_sort]" value="<?php echo esc_attr( $term_meta['creative_work_title_sort'] ) ? esc_attr( $term_meta['creative_work_title_sort'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the work sort title.', $CREATIVE_WORK_TEXTDOMAIN ); ?></p>
    </td>
  </tr>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[creative_work_translation]"><?php _e( 'Translations', $CREATIVE_WORK_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[creative_work_translation]" id="term_meta[creative_work_translation]" value="<?php echo esc_attr( $term_meta['creative_work_translation'] ) ? esc_attr( $term_meta['creative_work_translation'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the work translations if exist.', $CREATIVE_WORK_TEXTDOMAIN ); ?></p>
    </td>
  </tr>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[creative_work_translation_of]"><?php _e( 'Translation of', $CREATIVE_WORK_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[creative_work_translation_of]" id="term_meta[creative_work_translation_of]" value="<?php echo esc_attr( $term_meta['creative_work_translation_of'] ) ? esc_attr( $term_meta['creative_work_translation_of'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the work from which this work has been translated if exists.', $CREATIVE_WORK_TEXTDOMAIN ); ?></p>
    </td>
  </tr>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[creative_work_author]"><?php _e( 'Authors', $CREATIVE_WORK_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[creative_work_author]" id="term_meta[creative_work_author]" value="<?php echo esc_attr( $term_meta['creative_work_author'] ) ? esc_attr( $term_meta['creative_work_author'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the work authors.', $CREATIVE_WORK_TEXTDOMAIN ); ?></p>
    </td>
  </tr>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[creative_work_translator]"><?php _e( 'Translators', $CREATIVE_WORK_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[creative_work_translator]" id="term_meta[creative_work_translator]" value="<?php echo esc_attr( $term_meta['creative_work_translator'] ) ? esc_attr( $term_meta['creative_work_translator'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the work translators if exist.', $CREATIVE_WORK_TEXTDOMAIN ); ?></p>
    </td>
  </tr>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[creative_work_publisher]"><?php _e( 'Publisher', $CREATIVE_WORK_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[creative_work_publisher]" id="term_meta[creative_work_publisher]" value="<?php echo esc_attr( $term_meta['creative_work_publisher'] ) ? esc_attr( $term_meta['creative_work_publisher'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the work publisher.', $CREATIVE_WORK_TEXTDOMAIN ); ?></p>
    </td>
  </tr>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[creative_work_isbn10]"><?php _e( 'ISBN10', $CREATIVE_WORK_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[creative_work_isbn10]" id="term_meta[creative_work_isbn10]" value="<?php echo esc_attr( $term_meta['creative_work_isbn10'] ) ? esc_attr( $term_meta['creative_work_isbn10'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the work ISBN10 if exists.', $CREATIVE_WORK_TEXTDOMAIN ); ?></p>
    </td>
  </tr>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[creative_work_isbn13]"><?php _e( 'ISBN13', $CREATIVE_WORK_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[creative_work_isbn13]" id="term_meta[creative_work_isbn13]" value="<?php echo esc_attr( $term_meta['creative_work_isbn13'] ) ? esc_attr( $term_meta['creative_work_isbn13'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the work ISBN13.', $CREATIVE_WORK_TEXTDOMAIN ); ?></p>
    </td>
  </tr>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[creative_work_asin]"><?php _e( 'ASIN', $CREATIVE_WORK_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[creative_work_asin]" id="term_meta[creative_work_asin]" value="<?php echo esc_attr( $term_meta['creative_work_asin'] ) ? esc_attr( $term_meta['creative_work_asin'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the work ASIN.', $CREATIVE_WORK_TEXTDOMAIN ); ?></p>
    </td>
  </tr>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[creative_work_release_date]"><?php _e( 'Release date', $CREATIVE_WORK_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[creative_work_release_date]" id="term_meta[creative_work_release_date]" value="<?php echo esc_attr( $term_meta['creative_work_release_date'] ) ? esc_attr( $term_meta['creative_work_release_date'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the work release date (YYYY-mm-dd).', $CREATIVE_WORK_TEXTDOMAIN ); ?></p>
    </td>
  </tr>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[creative_work_language]"><?php _e( 'Language', $CREATIVE_WORK_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[creative_work_language]" id="term_meta[creative_work_language]" value="<?php echo esc_attr( $term_meta['creative_work_language'] ) ? esc_attr( $term_meta['creative_work_language'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the language in which the work is written.', $CREATIVE_WORK_TEXTDOMAIN ); ?></p>
    </td>
  </tr>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[creative_work_pagenumber]"><?php _e( 'Number of pages', $CREATIVE_WORK_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[creative_work_pagenumber]" id="term_meta[creative_work_pagenumber]" value="<?php echo esc_attr( $term_meta['creative_work_pagenumber'] ) ? esc_attr( $term_meta['creative_work_pagenumber'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the work number of pages.', $CREATIVE_WORK_TEXTDOMAIN ); ?></p>
    </td>
  </tr>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[creative_work_cover]"><?php _e( 'Cover', $CREATIVE_WORK_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[creative_work_cover]" id="term_meta[creative_work_cover]" value="<?php echo esc_attr( $term_meta['creative_work_cover'] ) ? esc_attr( $term_meta['creative_work_cover'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the work cover if exists.', $CREATIVE_WORK_TEXTDOMAIN ); ?></p>
    </td>
  </tr>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[creative_work_location]"><?php _e( 'Location', $CREATIVE_WORK_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[creative_work_location]" id="term_meta[creative_work_location]" value="<?php echo isset( $term_meta['creative_work_location'] ) ? esc_attr( $term_meta['creative_work_location'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the locations depicted or described in the work if exist.', $CREATIVE_WORK_TEXTDOMAIN ); ?></p>
    </td>
  </tr>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[creative_work_review]"><?php _e( 'Work review', $CREATIVE_WORK_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[creative_work_review]" id="term_meta[creative_work_review]" value="<?php echo esc_attr( $term_meta['creative_work_review'] ) ? esc_attr( $term_meta['creative_work_review'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the work review if exists.', $CREATIVE_WORK_TEXTDOMAIN ); ?></p>
    </td>
  </tr>
  <?php
}

add_action( 'creative_work_edit_form_fields', 'edit_creative_work_meta_field', 10, 2 );


/**
 * Saving custom fields in creative work taxonomy
 *
 * @param int $term_id
 *
 */

function save_creative_work_taxonomy_custom_meta( $term_id ){
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

add_action( 'edited_creative_work', 'save_creative_work_taxonomy_custom_meta', 10, 2 );
add_action( 'create_creative_work', 'save_creative_work_taxonomy_custom_meta', 10, 2 );


/**
 * Flush rewrites when the plugin is activated
 *
 */

function creative_work_taxonomy_flush_rewrites(){
  flush_rewrite_rules();
}

# Prevent 404 errors on creative works' archive
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
register_activation_hook( __FILE__, 'creative_work_taxonomy_flush_rewrites' );
add_action( 'init', 'creative_work_taxonomy_flush_rewrites', 0 );


/**
 * Getting term specific option
 *
 * @param object $option
 * @param object $work
 *
 * @return string
 *
 */

function get_creative_work_option( $option, $work ){
  $id        = $work->term_id;
  $term_meta = get_option( 'taxonomy_' . $id );

  return $term_meta[$option];
}


/**
 * Sort creative works by release date
 *
 * @param string $a, $b
 *
 * @return array
 *
 */

function sort_work_by_date( $a, $b ){
  $asort = get_creative_work_option( 'creative_work_release_date', $a );
  $bsort = get_creative_work_option( 'creative_work_release_date', $b );

  return strcasecmp( $asort, $bsort );
}


/**
 * Get all creative works for specific author
 *
 * @param string $person_slug
 *
 * @return array
 *
 */

function get_creative_works_from_author( $person_slug ){
  $person_works = array();

  # Find all creative works

  $tag_args = array(
      'format'     => 'array',
      'taxonomy'   => 'creative_work',
      'hide_empty' => false,
      'echo'       => false,
    );

  $works = get_terms( 'creative_work', $tag_args );
  usort( $works, 'sort_work_by_date' );

  foreach( $works as $work ){
    $author = get_creative_work_option( 'creative_work_author', $work );

    if( strpos( $author, $person_slug ) > -1 ){
      array_push( $person_works, $work );
    }
  }

  return $person_works;
}


?>
