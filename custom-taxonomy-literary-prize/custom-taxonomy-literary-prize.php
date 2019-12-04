<?php
/**
 *
 * Plugin Name: Custom Taxonomy Literary Prize
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin allows you to add a prize for your custom post type books.
 * Version: 0.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */

global $PRIZE_TEXTDOMAIN;

$PRIZE_TEXTDOMAIN = 'prize-taxonomy';


/*
 * Make plugin available for translation.
 * Translations can be filed in the /languages directory.
 *
 */

function literary_prize_taxonomy_load_textdomain() {
  global $PRIZE_TEXTDOMAIN;
  $locale = apply_filters( 'plugin_locale', get_locale(), $PRIZE_TEXTDOMAIN );

  # Load i18n
  $path = basename( dirname( __FILE__ ) ) . '/languages/';
  $loaded = load_plugin_textdomain( $PRIZE_TEXTDOMAIN, false, $path );
}

add_action( 'init', 'literary_prize_taxonomy_load_textdomain', 0 );


/**
 * Add prize taxonomy
 *
 **/

function add_prize_taxonomy() {
  global $PRIZE_TEXTDOMAIN;

  $labels = array (
    'name'                       => _x( 'Literary Prizes', 'taxonomy general name', $PRIZE_TEXTDOMAIN ),
    'singular_name'              => _x( 'Literary Prize', 'taxonomy singular name', $PRIZE_TEXTDOMAIN ),
    'search_items'               => __( 'Search Literary Prizes', $PRIZE_TEXTDOMAIN ),
    'popular_items'              => __( 'Popular Literary Prizes', $PRIZE_TEXTDOMAIN ),
    'all_items'                  => __( 'All Literary Prizes', $PRIZE_TEXTDOMAIN ),
    'parent_item'                => null,
    'parent_item_colon'          => null,
    'view_item'                  => __( 'See Literary Prize', $PRIZE_TEXTDOMAIN ),
    'edit_item'                  => __( 'Edit Literary Prize', $PRIZE_TEXTDOMAIN ),
    'update_item'                => __( 'Update Literary Prize', $PRIZE_TEXTDOMAIN ),
    'add_new_item'               => __( 'Add New Literary Prize', $PRIZE_TEXTDOMAIN ),
    'new_item_name'              => __( 'New Literary Prize Name', $PRIZE_TEXTDOMAIN ),
    'separate_items_with_commas' => __( 'Separate prizes with commas', $PRIZE_TEXTDOMAIN ),
    'add_or_remove_items'        => __( 'Add or remove prizes', $PRIZE_TEXTDOMAIN ),
    'choose_from_most_used'      => __( 'Choose from the most used prizes', $PRIZE_TEXTDOMAIN ),
    'not_found'                  => __( 'No prizes found.', $PRIZE_TEXTDOMAIN ),
    'back_to_items'              => __( '← Back to prizes', $PRIZE_TEXTDOMAIN ),
    'menu_name'                  => __( 'Literary Prizes', $PRIZE_TEXTDOMAIN ),
  );

  $args = array (
    'hierarchical'          => true,
    'labels'                => $labels,
    'show_ui'               => true,
    'show_admin_column'     => true,
    'update_count_callback' => '_update_post_term_count',
    'query_var'             => true,
    'rewrite'               => array( 'slug' => 'book/literary-prize', 'with_front' => 'true', 'hierarchical' => true ),
  );

  register_taxonomy ('prize', 'book', $args);
}

add_action ('init', 'add_prize_taxonomy', 1);


/**
 * Add custom field link
 *
 **/

function add_new_prize_meta_field() {
  global $PRIZE_TEXTDOMAIN;

  # This will add the custom meta fields to the 'Add new term' page
  ?>
  <div class="form-field">
    <label for="term_meta[prize_link]"><?php _e( 'Website link', $PRIZE_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[prize_link]" id="term_meta[prize_link]" value="">
    <p class="description"><?php _e( 'Enter the website link of the prize.', $PRIZE_TEXTDOMAIN ); ?></p>
  </div>
  <?php
}

add_action( 'prize_add_form_fields', 'add_new_prize_meta_field', 10, 2 );


/**
 * Editing custom fields in prize taxonomy
 *
 * @param object $term
 *
 */

function edit_prize_meta_field ($term) {
  global $PRIZE_TEXTDOMAIN;

  # Put the term ID into a variable
  $t_id = $term->term_id;

  # Retrieve the existing values for this meta field
  # This will return an array
  $term_meta = get_option( "taxonomy_$t_id" );

  ?>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[prize_link]"><?php _e( 'Website link', $PRIZE_TEXTDOMAIN ); ?></label></th>
    <td>
    	<input type="text" name="term_meta[prize_link]" id="term_meta[prize_link]" value="<?php echo esc_attr( $term_meta['prize_link'] ) ? esc_attr( $term_meta['prize_link'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the website link of the prize.', $PRIZE_TEXTDOMAIN); ?></p>
    </td>
  </tr>
  <?php
}

add_action( 'prize_edit_form_fields', 'edit_prize_meta_field', 10, 2 );


/**
 * Saving custom fields in prize taxonomy
 *
 * @param int $term_id
 *
 */

function save_prize_taxonomy_custom_meta ($term_id) {
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

add_action( 'edited_prize', 'save_prize_taxonomy_custom_meta', 10, 2 );
add_action( 'create_prize', 'save_prize_taxonomy_custom_meta', 10, 2 );


/**
 * Create widget to retrieve popular prizes in specific category
 *
 */

class popular_prizes_in_category_widget extends WP_Widget {
    function __construct() {
        global $PRIZE_TEXTDOMAIN;

        parent::__construct(
            # Base ID of your widget
            'popular_prizes_in_category_widget',

            # Widget name will appear in UI
            __('Popular Literary Prizes in Category Widget', $PRIZE_TEXTDOMAIN),

            # Widget description
            array( 'description' => __( 'This widget will show all the prizes in the specific category you choose', $PRIZE_TEXTDOMAIN ), )
        );
    }

    # Creating widget front-end
    public function widget( $args, $instance ) {
        global $PRIZE_TEXTDOMAIN;

        $title = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
        $lp_count = isset( $instance['lp_count'] ) ? $instance['lp_count'] : '';

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

        if( post_type_exists( 'concert' ) ){
            array_push( $post_types, 'concert' );
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
            $terms = wp_get_post_terms( $post->ID, 'prize' );

            foreach( $terms as $value ){
		$parent = $value->parent;

                if( !in_array( $value, $array_of_terms_in_category, true ) ){
                    array_push( $array_of_terms_in_category, $value->term_id );
                }
                if( $parent > 0 ){
                    if( !in_array( $parent, $array_of_terms_in_category, true ) ){
                        array_push( $array_of_terms_in_category, $parent );
                    }
                }
            }
        }

        $tag_args = array(
                    'format'   => 'array',
                    'number'   => $lp_count,
                    'taxonomy' => 'prize',
                    'orderby'  => 'count',
                    'order'    => 'DESC',
                    'include'  => $array_of_terms_in_category,
                    'parent'   => 0,
                    'echo'     => false,
                );

        echo '<div class="tagcloud">';

        $prizes_array = get_terms ( 'prize', $tag_args );

        if( sizeof( $prizes_array ) ){
            function widget_sort_prize_by_name( $a, $b ){
                $translit = array('Á'=>'A','À'=>'A','Â'=>'A','Ä'=>'A','Ã'=>'A','Å'=>'A','Ç'=>'C','É'=>'E','È'=>'E','Ê'=>'E','Ë'=>'E','Í'=>'I','Ï'=>'I','Î'=>'I','Ì'=>'I','Ñ'=>'N','Ó'=>'O','Ò'=>'O','Ô'=>'O','Ö'=>'O','Õ'=>'O','Ú'=>'U','Ù'=>'U','Û'=>'U','Ü'=>'U','Ý'=>'Y','á'=>'a','à'=>'a','â'=>'a','ä'=>'a','ã'=>'a','å'=>'a','ç'=>'c','é'=>'e','è'=>'e','ê'=>'e','ë'=>'e','í'=>'i','ì'=>'i','î'=>'i','ï'=>'i','ñ'=>'n','ó'=>'o','ò'=>'o','ô'=>'o','ö'=>'o','õ'=>'o','ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u','ý'=>'y','ÿ'=>'y');
                $at = strtolower( strtr( $a->name, $translit ) );
                $bt = strtolower( strtr( $b->name, $translit ) );

                return strcoll( $at, $bt );
            }

            usort( $prizes_array, 'widget_sort_prize_by_name' );

            echo '<ul class="wp-tag-cloud">';

	    foreach ( $prizes_array as $myprize ) {
                echo '<li><a href="' . get_term_link( $myprize->term_id ) . '" class="tag-cloud-link tag-link-' . $myprize->term_id . '">';
                echo $myprize->name;
                echo '</a></li>';
	    }

            echo '</ul>';
	}

        echo '</div>';

        echo $args['after_widget'];
    }

    # Widget Backend
    public function form( $instance ) {
        global $PRIZE_TEXTDOMAIN;

        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
            $lp_count = isset( $instance['lp_count'] ) ? esc_attr( $instance['lp_count'] ) : '';
        } else {
            $title = __( 'Literary prizes', $PRIZE_TEXTDOMAIN );
            $lp_count = 75;
        }

        # Widget admin form
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>

	<p>
	    <label for="<?php echo $this->get_field_id( 'lp_count' ); ?>"><?php _e( 'Number of literary prizes to show:', $PRIZE_TEXTDOMAIN ); ?></label>
	    <input type="text" name="<?php echo $this->get_field_name( 'lp_count' ); ?>" value="<?php echo esc_attr( $lp_count ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'lp_count' ); ?>" />
	</p>
        <?php
    }

    # Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = array();

        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['lp_count'] = $new_instance['lp_count'];

        return $instance;
    }
}

# Register and load the widget
function popular_prize_wpb_load_widget() {
    register_widget( 'popular_prizes_in_category_widget' );
}

add_action( 'widgets_init', 'popular_prize_wpb_load_widget' );


/**
 * Flush rewrites when the plugin is activated
 *
 */

function prize_taxonomy_flush_rewrites() {
  flush_rewrite_rules();
}

# Prevent 404 errors on prizes' archive
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
register_activation_hook( __FILE__, 'prize_taxonomy_flush_rewrites' );
add_action( 'init', 'prize_taxonomy_flush_rewrites', 0 );


/**
 * Getting term specific option
 *
 * @param object $option
 *
 * @return string
 *
 */

function get_prize_option ($option) {
  $prize = get_queried_object();
  $id = $prize->term_id;
  $term_meta = get_option( 'taxonomy_' . $id );
  return $term_meta[$option];
}

?>
