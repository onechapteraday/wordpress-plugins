<?php
/**
 *
 * Plugin Name: Add SEO description
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin help you to improve your descriptions for SEO
 * Version: 0.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */

global $SEO_TEXTDOMAIN;

$SEO_TEXTDOMAIN = 'seo-custom-description';


/*
 * Make plugin available for translation.
 * Translations can be filed in the /languages directory.
 *
 */

function seo_custom_description_load_textdomain() {
  global $SEO_TEXTDOMAIN;
  $locale = apply_filters( 'plugin_locale', get_locale(), $SEO_TEXTDOMAIN );

  # Load i18n
  $path = basename( dirname( __FILE__ ) ) . '/languages/';
  $loaded = load_plugin_textdomain( $SEO_TEXTDOMAIN, false, $path );
}

add_action( 'init', 'seo_custom_description_load_textdomain', 0 );


/*
 * Add SEO description meta box
 */

function seo_description_add_custom_box(){
    global $SEO_TEXTDOMAIN;

    $screens = array( 'post' );

    if( post_type_exists( 'book' ) ){
        array_push( $screens, 'book' );
    }

    if( post_type_exists( 'album' ) ){
        array_push( $screens, 'album' );
    }

    if( post_type_exists( 'interview' ) ){
        array_push( $screens, 'interview' );
    }

    if( post_type_exists( 'concert' ) ){
        array_push( $screens, 'concert' );
    }

    foreach( $screens as $screen ){
        add_meta_box(
            # Unique ID
            'seo_description_box_id',

            # Box description
            __( 'SEO Description', $SEO_TEXTDOMAIN ),

            # Content callback, must be of type callable
            'seo_description_custom_box_html',

            # Post type
            $screen
        );
    }
}

add_action( 'add_meta_boxes', 'seo_description_add_custom_box' );

function seo_description_custom_box_html( $post ){
    global $SEO_TEXTDOMAIN;

    ?>
    <label for="seo_description_field" style="margin: 5px 0 8px; display: inline-block;"><em><?php echo __( 'Enter here description for SEO', $SEO_TEXTDOMAIN ); ?></em></label><br />
    <textarea name="seo_description_field" id="seo_description_field" rows="5" cols="50" maxlength="155" style="max-width: 100%;"><?php echo esc_attr( get_post_meta( $post->ID, '_seo_description_meta_key', true ) ); ?></textarea>
    <?php
}

function seo_description_save_postdata( $post_id ){
    if( array_key_exists( 'seo_description_field', $_POST ) ){
        update_post_meta(
            $post_id,
            '_seo_description_meta_key',
            $_POST['seo_description_field']
        );
    }
}

add_action( 'save_post', 'seo_description_save_postdata' );


/**
 * Write meta description tag for SEO
 */

function seo_description_custom_description( $description ){
    # Only for single post or page
    if( ( is_single() || is_page() ) && !is_page( 'front-page' ) ){

        # And only if new SEO description is defined
        if( get_the_ID() ){
            $id = get_the_ID();
            $meta_description = get_post_meta( $id, '_seo_description_meta_key', true );

            if( $meta_description != '' ){
                ?><meta name="description" content="<?php echo $meta_description; ?>" />
                <?php
            }
        }
    }
}

add_action('wp_head',  'seo_description_custom_description' );

?>
