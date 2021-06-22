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


global $PUBLISHER_TEXTDOMAIN;

$PUBLISHER_TEXTDOMAIN = 'publisher-taxonomy';


/*
 * Make plugin available for translation.
 * Translations can be filed in the /languages directory.
 *
 */

function publisher_taxonomy_load_textdomain() {
  global $PUBLISHER_TEXTDOMAIN;
  $locale = apply_filters( 'plugin_locale', get_locale(), $PUBLISHER_TEXTDOMAIN );

  # Load i18n
  $path = basename( dirname( __FILE__ ) ) . '/languages/';
  $loaded = load_plugin_textdomain( $PUBLISHER_TEXTDOMAIN, false, $path );
}

add_action( 'init', 'publisher_taxonomy_load_textdomain', 0 );


/**
 * Add publisher taxonomy
 *
 **/

function add_publisher_taxonomy() {
  global $PUBLISHER_TEXTDOMAIN;

  $labels = array (
    'name'                       => _x( 'Publishers', 'taxonomy general name', $PUBLISHER_TEXTDOMAIN ),
    'singular_name'              => _x( 'Publisher', 'taxonomy singular name', $PUBLISHER_TEXTDOMAIN ),
    'search_items'               => __( 'Search Publishers', $PUBLISHER_TEXTDOMAIN ),
    'popular_items'              => __( 'Popular Publishers', $PUBLISHER_TEXTDOMAIN ),
    'all_items'                  => __( 'All Publishers', $PUBLISHER_TEXTDOMAIN ),
    'parent_item'                => null,
    'parent_item_colon'          => null,
    'view_item'                  => __( 'See Publisher', $PUBLISHER_TEXTDOMAIN ),
    'edit_item'                  => __( 'Edit Publisher', $PUBLISHER_TEXTDOMAIN ),
    'update_item'                => __( 'Update Publisher', $PUBLISHER_TEXTDOMAIN ),
    'add_new_item'               => __( 'Add New Publisher', $PUBLISHER_TEXTDOMAIN ),
    'new_item_name'              => __( 'New Publisher Name', $PUBLISHER_TEXTDOMAIN ),
    'separate_items_with_commas' => __( 'Separate publishers with commas', $PUBLISHER_TEXTDOMAIN ),
    'add_or_remove_items'        => __( 'Add or remove publishers', $PUBLISHER_TEXTDOMAIN ),
    'choose_from_most_used'      => __( 'Choose from the most used publishers', $PUBLISHER_TEXTDOMAIN ),
    'not_found'                  => __( 'No publishers found.', $PUBLISHER_TEXTDOMAIN ),
    'back_to_items'              => __( '← Back to publishers', $PUBLISHER_TEXTDOMAIN ),
    'menu_name'                  => __( 'Publishers', $PUBLISHER_TEXTDOMAIN ),
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
  global $PUBLISHER_TEXTDOMAIN;

  # This will add the custom meta fields to the 'Add new term' page
  ?>
  <div class="form-field">
    <label for="term_meta[publisher_sortname]"><?php _e( 'Sort name', $PUBLISHER_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[publisher_sortname]" id="term_meta[publisher_sortname]" value="">
    <p class="description"><?php _e( 'Enter the name of the publisher as it should be sorted.', $PUBLISHER_TEXTDOMAIN ); ?></p>
  </div>

  <div class="form-field">
    <label for="term_meta[publisher_link]"><?php _e( 'Website link', $PUBLISHER_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[publisher_link]" id="term_meta[publisher_link]" value="">
    <p class="description"><?php _e( 'Enter the website link of the publisher.', $PUBLISHER_TEXTDOMAIN ); ?></p>
  </div>

  <div class="form-field">
    <label for="term_meta[publisher_twitter]"><?php _e( 'Twitter', $PUBLISHER_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[publisher_twitter]" id="term_meta[publisher_twitter]" value="">
    <p class="description"><?php _e( 'Enter the Twitter account name of the publisher, only the part after the base url.', $PUBLISHER_TEXTDOMAIN ); ?></p>
  </div>

  <div class="form-field">
    <label for="term_meta[publisher_facebook]"><?php _e( 'Facebook', $PUBLISHER_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[publisher_facebook]" id="term_meta[publisher_facebook]" value="">
    <p class="description"><?php _e( 'Enter the Facebook account name of the publisher, only the part after the base url.', $PUBLISHER_TEXTDOMAIN ); ?></p>
  </div>

  <div class="form-field">
    <label for="term_meta[publisher_instagram]"><?php _e( 'Instagram', $PUBLISHER_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[publisher_instagram]" id="term_meta[publisher_instagram]" value="">
    <p class="description"><?php _e( 'Enter the Instagram account name of the publisher, only the part after the base url.', $PUBLISHER_TEXTDOMAIN ); ?></p>
  </div>

  <div class="form-field">
    <label for="term_meta[publisher_youtube]"><?php _e( 'YouTube', $PUBLISHER_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[publisher_youtube]" id="term_meta[publisher_youtube]" value="">
    <p class="description"><?php _e( 'Enter the YouTube account name of the publisher, only the part after the base url.', $PUBLISHER_TEXTDOMAIN ); ?></p>
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
  global $PUBLISHER_TEXTDOMAIN;

  # Put the term ID into a variable
  $t_id = $term->term_id;

  # Retrieve the existing values for this meta field
  # This will return an array
  $term_meta = get_option( "taxonomy_$t_id" );

  ?>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[publisher_sortname]"><?php _e( 'Sort name', $PUBLISHER_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[publisher_sortname]" id="term_meta[publisher_sortname]" value="<?php echo isset( $term_meta['publisher_sortname'] ) ? esc_attr( $term_meta['publisher_sortname'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the name of the publisher as it should be sorted.', $PUBLISHER_TEXTDOMAIN); ?></p>
    </td>
  </tr>

  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[publisher_link]"><?php _e( 'Website link', $PUBLISHER_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[publisher_link]" id="term_meta[publisher_link]" value="<?php echo esc_attr( $term_meta['publisher_link'] ) ? esc_attr( $term_meta['publisher_link'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the website link of the publisher.', $PUBLISHER_TEXTDOMAIN); ?></p>
    </td>
  </tr>

  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[publisher_twitter]"><?php _e( 'Twitter', $PUBLISHER_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[publisher_twitter]" id="term_meta[publisher_twitter]" value="<?php echo isset( $term_meta['publisher_twitter'] ) ? esc_attr( $term_meta['publisher_twitter'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the Twitter account name of the publisher, only the part after the base url.', $PUBLISHER_TEXTDOMAIN ); ?></p>
    </td>
  </tr>

  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[publisher_facebook]"><?php _e( 'Facebook', $PUBLISHER_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[publisher_facebook]" id="term_meta[publisher_facebook]" value="<?php echo isset( $term_meta['publisher_facebook'] ) ? esc_attr( $term_meta['publisher_facebook'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the Facebook account name of the publisher, only the part after the base url.', $PUBLISHER_TEXTDOMAIN ); ?></p>
    </td>
  </tr>

  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[publisher_instagram]"><?php _e( 'Instagram', $PUBLISHER_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[publisher_instagram]" id="term_meta[publisher_instagram]" value="<?php echo isset( $term_meta['publisher_instagram'] ) ? esc_attr( $term_meta['publisher_instagram'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the Instagram account name of the publisher, only the part after the base url.', $PUBLISHER_TEXTDOMAIN ); ?></p>
    </td>
  </tr>

  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[publisher_youtube]"><?php _e( 'YouTube', $PUBLISHER_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[publisher_youtube]" id="term_meta[publisher_youtube]" value="<?php echo isset( $term_meta['publisher_youtube'] ) ? esc_attr( $term_meta['publisher_youtube'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the YouTube account name of the publisher, only the part after the base url.', $PUBLISHER_TEXTDOMAIN ); ?></p>
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
 * Create widget to retrieve popular publishers
 *
 */

function widget_sort_publisher_by_name( $a, $b ){
    $a_op = get_option( "taxonomy_$a->term_id" );
    $b_op = get_option( "taxonomy_$b->term_id" );

    $asort = isset( $a_op['publisher_sortname'] ) ? $a_op['publisher_sortname'] : $a->name;
    $bsort = isset( $b_op['publisher_sortname'] ) ? $b_op['publisher_sortname'] : $b->name;

    $translit = array('Á'=>'A','À'=>'A','Â'=>'A','Ä'=>'A','Ã'=>'A','Å'=>'A','Ç'=>'C','É'=>'E','È'=>'E','Ê'=>'E','Ë'=>'E','Í'=>'I','Ï'=>'I','Î'=>'I','Ì'=>'I','Ñ'=>'N','Ó'=>'O','Ò'=>'O','Ô'=>'O','Ö'=>'O','Õ'=>'O','Ú'=>'U','Ù'=>'U','Û'=>'U','Ü'=>'U','Ý'=>'Y','á'=>'a','à'=>'a','â'=>'a','ä'=>'a','ã'=>'a','å'=>'a','ç'=>'c','é'=>'e','è'=>'e','ê'=>'e','ë'=>'e','í'=>'i','ì'=>'i','î'=>'i','ï'=>'i','ñ'=>'n','ó'=>'o','ò'=>'o','ô'=>'o','ö'=>'o','õ'=>'o','ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u','ý'=>'y','ÿ'=>'y');

    $at = strtolower( strtr( $asort, $translit ) );
    $bt = strtolower( strtr( $bsort, $translit ) );

    return strcasecmp( $at, $bt );
}

class popular_publishers_in_category_widget extends WP_Widget {
    function __construct() {
        global $PUBLISHER_TEXTDOMAIN;

        parent::__construct(
            # Base ID of your widget
            'popular_publishers_in_category_widget',

            # Widget name will appear in UI
            __('Popular Publishers in Category Widget', $PUBLISHER_TEXTDOMAIN),

            # Widget description
            array( 'description' => __( 'This widget will show all the publishers in the specific category you choose.', $PUBLISHER_TEXTDOMAIN ), )
        );
    }

    # Creating widget front-end
    public function widget( $args, $instance ) {
        global $PUBLISHER_TEXTDOMAIN;

        $title = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
        $p_count = isset( $instance['p_count'] ) ? $instance['p_count'] : '';

        # Before and after widget arguments are defined by themes
        echo $args['before_widget'];

        if ( ! empty( $title ) )
            echo $args['before_title'] . $title . $args['after_title'];

        # This is where you run the code and display the output

        # Find the category where is displayed the widget
        $category = 'reads';
	$catID = get_cat_ID( $category );

        $post_types = array( 'post' );

        if( post_type_exists( 'book' ) ){
            array_push( $post_types, 'book' );
        }

        if( post_type_exists( 'album' ) ){
            array_push( $post_types, 'album' );
        }

        if( post_type_exists( 'concert' ) ){
            array_push( $post_types, 'concert' );
        }

        if( post_type_exists( 'interview' ) ){
            array_push( $post_types, 'interview' );
        }

        $posts_with_category = get_posts( array(
                     'category'       => $catID,
                     'post_type'      => $post_types,
                     'number_posts'   => -1,
                     'posts_per_page' => -1,
                 ));

        $array_of_terms_in_category = array();

        foreach( $posts_with_category as $post ) {
            $terms = wp_get_post_terms( $post->ID, 'publisher' );

            foreach( $terms as $value ){
		$parent = $value->parent;

                if( !in_array( $value, $array_of_terms_in_category, true ) ){
                    # Add publisher only if parent
                    if( $parent == 0 ){
                        array_push( $array_of_terms_in_category, $value->term_id );
                    }
                }

                # Add parent publisher if not in array
                if( $parent > 0 ){
                    if( !in_array( $parent, $array_of_terms_in_category, true ) ){
                        array_push( $array_of_terms_in_category, $parent );
                    }
                }
            }
        }

        # All publishers (w/ collections)
        # In order to publishers to be well ordered by count

        $tag_args = array(
                    'format'     => 'array',
                    'taxonomy'   => 'publisher',
                    'orderby'    => 'count',
                    'order'      => 'DESC',
                    'echo'       => false,
                );


        echo '<div class="tagcloud">';

        $all_publishers_array = get_terms ( 'publisher', $tag_args );

        if( sizeof( $all_publishers_array ) ){

            # Get all publishers w/ correct count (by adding collections' count)

            $publishers_test = array();

            foreach( $all_publishers_array as $pub ){
                $term_id = $pub->term_id;
                $parent  = $pub->parent;
                $count   = $pub->count;

                # If publisher (and not a collection)

                if( $parent == 0 ){

                    if( !isset( $publishers_test[ $term_id ] ) ){

                        # If not already in array, add key and count
                        $publishers_test[ $term_id ] = $count;

                    } else {

                        # If already in array, find key and add count
                        $publishers_test[ $term_id ] += $count;

                    }
                }

                # If collection

                else {
                    if( !isset( $publishers_test[ $parent ] ) ){

                        # If parent is not already in array, add key and count
                        $publishers_test[ $parent ] = $count;

                    } else {

                        # If parent is already in array, find key and add count
                        $publishers_test[ $parent ] += $count;

                    }
                }
            }

            # Reverse sort and keep index
            # Order by the publishers w/ the most collections

            arsort( $publishers_test );

            # Create array w/ only the needed term_ids

            $publishers_id = array();

            foreach( $publishers_test as $key => $value ){
                array_push( $publishers_id, $key );

                if( count( $publishers_id ) >= $p_count ){
                    break;
                }
            }

            # Get publishers cloud order by count

            $new_args = array(
                            'include'  => $publishers_id,
                        );

            $publishers_array = get_terms ( 'publisher', $new_args );

            # Alphabetize publishers

            usort( $publishers_array, 'widget_sort_publisher_by_name' );

            echo '<ul class="wp-tag-cloud">';

	    foreach ( $publishers_array as $mypublisher ) {
                echo '<li><a href="' . get_term_link( $mypublisher->term_id ) . '" class="tag-cloud-link tag-link-' . $mypublisher->term_id . '">';
                echo $mypublisher->name;
                echo '</a></li>';
	    }

            echo '</ul>';
	}

        echo '</div>';

        echo $args['after_widget'];
    }

    # Widget Backend
    public function form( $instance ) {
        global $PUBLISHER_TEXTDOMAIN;

        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
            $p_count = isset( $instance['p_count'] ) ? esc_attr( $instance['p_count'] ) : '';
        } else {
            $title = __( 'Publishers', $PUBLISHER_TEXTDOMAIN );
            $p_count = 75;
        }

        # Widget admin form
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>

	<p>
	    <label for="<?php echo $this->get_field_id( 'p_count' ); ?>"><?php _e( 'Number of publishers to show:', $PUBLISHER_TEXTDOMAIN ); ?></label>
	    <input type="text" name="<?php echo $this->get_field_name( 'p_count' ); ?>" value="<?php echo esc_attr( $p_count ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'p_count' ); ?>" />
	</p>
        <?php
    }

    # Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = array();

        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['p_count'] = $new_instance['p_count'];

        return $instance;
    }
}

# Register and load the widget
function popular_publisher_wpb_load_widget() {
    register_widget( 'popular_publishers_in_category_widget' );
}

add_action( 'widgets_init', 'popular_publisher_wpb_load_widget' );


/**
 * Create widget to retrieve popular collections
 *
 */

class popular_collections_in_category_widget extends WP_Widget {
    function __construct() {
        global $PUBLISHER_TEXTDOMAIN;

        parent::__construct(
            # Base ID of your widget
            'popular_collections_in_category_widget',

            # Widget name will appear in UI
            __('Popular Collections in Category Widget', $PUBLISHER_TEXTDOMAIN),

            # Widget description
            array( 'description' => __( 'This widget will show all the collections in the specific category you choose.', $PUBLISHER_TEXTDOMAIN ), )
        );
    }

    # Creating widget front-end
    public function widget( $args, $instance ) {
        global $PUBLISHER_TEXTDOMAIN;

        $title = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
        $c_count = isset( $instance['c_count'] ) ? $instance['c_count'] : '';

        # Before and after widget arguments are defined by themes
        echo $args['before_widget'];

        if ( ! empty( $title ) )
            echo $args['before_title'] . $title . $args['after_title'];

        # This is where you run the code and display the output

        # Find the category where is displayed the widget
        $category = 'reads';
	$catID = get_cat_ID( $category );

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

        $posts_with_category = get_posts( array(
                     'category'       => $catID,
                     'post_type'      => $post_types,
                     'number_posts'   => -1,
                     'posts_per_page' => -1,
                 ));

        $array_of_terms_in_category = array();

        foreach( $posts_with_category as $post ) {
            $terms = wp_get_post_terms( $post->ID, 'publisher' );

            foreach( $terms as $value ){
		$parent = $value->parent;

                if( !in_array( $value, $array_of_terms_in_category, true ) ){
                    # Add "publisher" only if collection
                    if( $parent > 0 ){
                        array_push( $array_of_terms_in_category, $value->term_id );
                    }
                }
            }
        }

        $tag_args = array(
                    'format'   => 'array',
                    'number'   => $c_count,
                    'taxonomy' => 'publisher',
                    'orderby'  => 'count',
                    'order'    => 'DESC',
                    'include'  => $array_of_terms_in_category,
                    'echo'     => false,
                );

        echo '<div class="tagcloud">';

        $collections_array = get_terms ( 'publisher', $tag_args );

        if( sizeof( $collections_array ) ){
            function widget_sort_collection_by_name( $a, $b ){
                $translit = array('Á'=>'A','À'=>'A','Â'=>'A','Ä'=>'A','Ã'=>'A','Å'=>'A','Ç'=>'C','É'=>'E','È'=>'E','Ê'=>'E','Ë'=>'E','Í'=>'I','Ï'=>'I','Î'=>'I','Ì'=>'I','Ñ'=>'N','Ó'=>'O','Ò'=>'O','Ô'=>'O','Ö'=>'O','Õ'=>'O','Ú'=>'U','Ù'=>'U','Û'=>'U','Ü'=>'U','Ý'=>'Y','á'=>'a','à'=>'a','â'=>'a','ä'=>'a','ã'=>'a','å'=>'a','ç'=>'c','é'=>'e','è'=>'e','ê'=>'e','ë'=>'e','í'=>'i','ì'=>'i','î'=>'i','ï'=>'i','ñ'=>'n','ó'=>'o','ò'=>'o','ô'=>'o','ö'=>'o','õ'=>'o','ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u','ý'=>'y','ÿ'=>'y');
                $at = strtolower( strtr( $a->name, $translit ) );
                $bt = strtolower( strtr( $b->name, $translit ) );

                if( $at == $bt ){
                    $a_parent_name = get_term( $a->parent, 'publisher' )->name;
                    $b_parent_name = get_term( $b->parent, 'publisher' )->name;

                    return strcoll( $a_parent_name, $b_parent_name );
                }

                return strcoll( $at, $bt );
            }

            usort( $collections_array, 'widget_sort_collection_by_name' );

            echo '<ul class="wp-tag-cloud">';

	    foreach ( $collections_array as $coll ) {
                $publisher = get_term( $coll->parent, 'publisher' );

                echo '<li><a href="' . get_term_link( $coll->term_id ) . '" class="tag-cloud-link tag-link-' . $coll->term_id . '" title="' . $publisher->name . '">';
                echo $coll->name;
                echo '</a></li>';
	    }

            echo '</ul>';
	}

        echo '</div>';

        echo $args['after_widget'];
    }

    # Widget Backend
    public function form( $instance ) {
        global $PUBLISHER_TEXTDOMAIN;

        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
            $c_count = isset( $instance['c_count'] ) ? esc_attr( $instance['c_count'] ) : '';
        } else {
            $title = __( 'Collections', $PUBLISHER_TEXTDOMAIN );
            $c_count = 75;
        }

        # Widget admin form
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>

	<p>
	    <label for="<?php echo $this->get_field_id( 'c_count' ); ?>"><?php _e( 'Number of collections to show:', $PUBLISHER_TEXTDOMAIN ); ?></label>
	    <input type="text" name="<?php echo $this->get_field_name( 'c_count' ); ?>" value="<?php echo esc_attr( $c_count ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'c_count' ); ?>" />
	</p>
        <?php
    }

    # Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = array();

        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['c_count'] = $new_instance['c_count'];

        return $instance;
    }
}

# Register and load the widget
function popular_collection_wpb_load_widget() {
    register_widget( 'popular_collections_in_category_widget' );
}

add_action( 'widgets_init', 'popular_collection_wpb_load_widget' );


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


/**
 * Getting term specific option
 *
 * @param object $option
 *
 * @return string
 *
 */

function get_publisher_option( $option ){
  $publisher = get_queried_object();
  $id = $publisher->term_id;
  $term_meta = get_option( 'taxonomy_' . $id );

  return isset( $term_meta[ $option ] ) ? $term_meta[ $option ] : false;
}

?>
