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
 * Create widget to retrieve popular publishers in specific category
 *
 */

class popular_publishers_in_category_widget extends WP_Widget {
    function __construct() {
        parent::__construct(
            # Base ID of your widget
            'popular_publishers_in_category_widget',

            # Widget name will appear in UI
            __('Popular Publishers in Category Widget', 'popular_publishers_in_category_widget_domain'),

            # Widget description
            array( 'description' => __( 'This widget will show all the publishers in the specific category you choose', 'popular_publishers_in_category_widget_domain' ), )
        );
    }

    # Creating widget front-end
    public function widget( $args, $instance ) {
        global $LOCATION_TEXTDOMAIN;
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

        $tag_args = array(
                    'format'   => 'array',
                    'number'   => 75,
                    'taxonomy' => 'publisher',
                    'orderby'  => 'count',
                    'order'    => 'DESC',
                    'include'  => $array_of_terms_in_category,
                    'echo'     => false,
                );

        echo '<div class="tagcloud">';

        $publishers_array = get_terms ( 'publisher', $tag_args );

        if( sizeof( $publishers_array ) ){
            function widget_sort_publisher_by_name( $a, $b ){
                $translit = array('Á'=>'A','À'=>'A','Â'=>'A','Ä'=>'A','Ã'=>'A','Å'=>'A','Ç'=>'C','É'=>'E','È'=>'E','Ê'=>'E','Ë'=>'E','Í'=>'I','Ï'=>'I','Î'=>'I','Ì'=>'I','Ñ'=>'N','Ó'=>'O','Ò'=>'O','Ô'=>'O','Ö'=>'O','Õ'=>'O','Ú'=>'U','Ù'=>'U','Û'=>'U','Ü'=>'U','Ý'=>'Y','á'=>'a','à'=>'a','â'=>'a','ä'=>'a','ã'=>'a','å'=>'a','ç'=>'c','é'=>'e','è'=>'e','ê'=>'e','ë'=>'e','í'=>'i','ì'=>'i','î'=>'i','ï'=>'i','ñ'=>'n','ó'=>'o','ò'=>'o','ô'=>'o','ö'=>'o','õ'=>'o','ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u','ý'=>'y','ÿ'=>'y');
                $at = strtr( $a->name, $translit );
                $bt = strtr( $b->name, $translit );

                return strcoll( $at, $bt );
            }

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
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        } else {
            $title = __( 'Publishers', 'popular_publishers_in_category_widget_domain' );
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
function popular_publisher_wpb_load_widget() {
    register_widget( 'popular_publishers_in_category_widget' );
}

add_action( 'widgets_init', 'popular_publisher_wpb_load_widget' );


/**
 * Create widget to retrieve popular collections in specific category
 *
 */

class popular_collections_in_category_widget extends WP_Widget {
    function __construct() {
        parent::__construct(
            # Base ID of your widget
            'popular_collections_in_category_widget',

            # Widget name will appear in UI
            __('Popular Collections in Category Widget', 'popular_collections_in_category_widget_domain'),

            # Widget description
            array( 'description' => __( 'This widget will show all the collections in the specific category you choose', 'popular_collections_in_category_widget_domain' ), )
        );
    }

    # Creating widget front-end
    public function widget( $args, $instance ) {
        global $LOCATION_TEXTDOMAIN;
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
                    'number'   => 75,
                    'taxonomy' => 'publisher',
                    'orderby'  => 'count',
                    'order'    => 'DESC',
                    'include'  => $array_of_terms_in_category,
                    'echo'     => false,
                );

        echo '<div class="tagcloud">';

        $publishers_array = get_terms ( 'publisher', $tag_args );

        if( sizeof( $publishers_array ) ){
            function widget_sort_collection_by_name( $a, $b ){
                $translit = array('Á'=>'A','À'=>'A','Â'=>'A','Ä'=>'A','Ã'=>'A','Å'=>'A','Ç'=>'C','É'=>'E','È'=>'E','Ê'=>'E','Ë'=>'E','Í'=>'I','Ï'=>'I','Î'=>'I','Ì'=>'I','Ñ'=>'N','Ó'=>'O','Ò'=>'O','Ô'=>'O','Ö'=>'O','Õ'=>'O','Ú'=>'U','Ù'=>'U','Û'=>'U','Ü'=>'U','Ý'=>'Y','á'=>'a','à'=>'a','â'=>'a','ä'=>'a','ã'=>'a','å'=>'a','ç'=>'c','é'=>'e','è'=>'e','ê'=>'e','ë'=>'e','í'=>'i','ì'=>'i','î'=>'i','ï'=>'i','ñ'=>'n','ó'=>'o','ò'=>'o','ô'=>'o','ö'=>'o','õ'=>'o','ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u','ý'=>'y','ÿ'=>'y');
                $at = strtr( $a->name, $translit );
                $bt = strtr( $b->name, $translit );

                return strcoll( $at, $bt );
            }

            usort( $publishers_array, 'widget_sort_collection_by_name' );

            echo '<ul class="wp-tag-cloud">';

	    foreach ( $publishers_array as $coll ) {
                $unid = false;

                # Define which collection has an unidentifiable name
                $coll_patterns = array(
                    '/litterature/',
                    '/roman/',
                    '/domaine/',
                );

                # Check if $coll->name has an unidentifiable name
                foreach( $coll_patterns as $pattern ){
                    if( preg_match( $pattern, strtolower( $coll->slug ) ) ){
                        $unid = true;
                        break;
                    }
                }

                if( $unid ){
                    $parent = get_term( $coll->parent, 'publisher' );
                }

                echo '<li><a href="' . get_term_link( $coll->term_id ) . '" class="tag-cloud-link tag-link-' . $coll->term_id . '">';
                echo $coll->name;

                if( $unid ){
                    echo ' ('. $parent->name . ')';
                }

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
            $title = __( 'Collections', 'popular_collections_in_category_widget_domain' );
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

function get_publisher_option ($option) {
  $publisher = get_queried_object();
  $id = $publisher->term_id;
  $term_meta = get_option( 'taxonomy_' . $id );
  return $term_meta[$option];
}

?>
