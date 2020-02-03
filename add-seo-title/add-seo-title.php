<?php
/**
 *
 * Plugin Name: Add SEO title
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin help you to improve your titles for SEO
 * Version: 0.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */

global $SEO_TITLE_TEXTDOMAIN;

$SEO_TITLE_TEXTDOMAIN = 'seo-custom-title';


/*
 * Make plugin available for translation.
 * Translations can be filed in the /languages directory.
 *
 */

function seo_custom_title_load_textdomain() {
  global $SEO_TITLE_TEXTDOMAIN;
  $locale = apply_filters( 'plugin_locale', get_locale(), $SEO_TITLE_TEXTDOMAIN );

  # Load i18n
  $path = basename( dirname( __FILE__ ) ) . '/languages/';
  $loaded = load_plugin_textdomain( $SEO_TITLE_TEXTDOMAIN, false, $path );
}

add_action( 'init', 'seo_custom_title_load_textdomain', 0 );


/*
 * Add SEO title meta box
 */

function seo_title_add_custom_box(){
    global $SEO_TITLE_TEXTDOMAIN;

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
            'seo_title_box_id',

            # Box title
            __( 'SEO Title', $SEO_TITLE_TEXTDOMAIN ),

            # Content callback, must be of type callable
            'seo_title_custom_box_html',

            # Post type
            $screen
        );
    }
}

add_action( 'add_meta_boxes', 'seo_title_add_custom_box' );

function seo_title_custom_box_html( $post ){
    global $SEO_TITLE_TEXTDOMAIN;

    ?>
    <label for="seo_title_field" style="margin: 5px 0 8px; display: inline-block;"><em><?php echo __( 'Enter here title for SEO', $SEO_TITLE_TEXTDOMAIN ); ?></em></label><br />
    <input type="text" name="seo_title_field" id="seo_title_field" value="<?php echo esc_attr( get_post_meta( $post->ID, '_seo_title_meta_key', true ) ); ?>" size="35" style="max-width: 100%;" />
    <?php
}

function seo_title_save_postdata( $post_id ){
    if( array_key_exists( 'seo_title_field', $_POST ) ){
        update_post_meta(
            $post_id,
            '_seo_title_meta_key',
            $_POST['seo_title_field']
        );
    }
}

add_action( 'save_post', 'seo_title_save_postdata' );


/**
 * Rewrite title tag for SEO
 */

function seo_title_custom_title( $title ){
    $title = get_bloginfo( 'name' ) . ' &sdot; ' . get_bloginfo( 'description' );

    # Single post and page
    if( ( is_single() || is_page() ) && !is_page( 'front-page' ) ){
        # If new SEO title defined
        if( get_the_ID() ){
            $id = get_the_ID();
            $meta_title = get_post_meta( $id, '_seo_title_meta_key', true );

            if( $meta_title != '' ){
                return strip_tags( $meta_title );
            }
        }

        return strip_tags( get_the_title() );
    }

    # Category
    if( is_category() ){
        $cat = get_queried_object();
        return 'Articles de la catégorie ' . $cat->name . ' - ' . get_bloginfo( 'name' );
    }

    # Tag
    if( is_tag() ){
        $tag = get_queried_object();
        return 'Articles concernant l\'étiquette ' . mb_strtoupper( mb_substr( $tag->name, 0, 1 )) . mb_substr( $tag->name, 1 ) . ' - ' . get_bloginfo( 'name' );
    }

    # Archive year
    if( is_year() ){
        return 'Articles datant de ' . get_the_date( _x( 'Y', 'yearly archives date format' ) ) . ' - ' . get_bloginfo( 'name' );
    }

    # Archive month
    if( is_month() ){
        $month = get_the_date( _x( 'F', 'monthly archives date format' ) );
        $vowels = array( 'a', 'e', 'i', 'o', 'u', 'y' );
        $part = 'de ';

        if( in_array( mb_substr( $month, 0, 1 ), $vowels ) ){
            $part = 'd\'';
        }

        return 'Articles datant ' . $part . get_the_date( _x( 'F Y', 'monthly archives date format' ) ) . ' - ' . get_bloginfo( 'name' );
    }

    # Archive day
    if( is_day() ){
        return 'Articles datant du ' . get_the_date( _x( 'j F Y', 'daily archives date format' ) ) . ' - ' . get_bloginfo( 'name' );
    }

    # Post type archives
    if( is_post_type_archive() ){
        return 'Articles - ' . get_bloginfo( 'name' );
    }

    # Author
    if( is_author() ){
        return 'Articles écrits par ' . ucfirst( get_the_author() ) . ' - ' . get_bloginfo( 'name' );
    }

    # Custom taxonomy person
    if( is_tax( 'person' ) ){
        $person = get_queried_object();

        return 'Articles concernant ' . $person->name . ' - ' . get_bloginfo( 'name' );
    }

    # Custom taxonomy location
    if( is_tax( 'location' ) ){
        $location = get_queried_object();

        return 'Articles concernant la localité ' . __( $location->name, 'location-taxonomy' ) . ' - ' . get_bloginfo( 'name' );
    }

    # Custom taxonomy publisher
    if( is_tax( 'publisher' ) ){
        $publisher = get_queried_object();
        $parent_id = $publisher->parent;

        # If parent_id then it's a collection
        if( $parent_id > 0 ){
            $parent = get_term( $parent_id, 'publisher' );
            return 'Livres de la collection « ' . $publisher->name . ' » de la maison d\'édition ' . $parent->name . ' - ' . get_bloginfo( 'name' );
        }

        return 'Livres de la maison d\'édition ' . $publisher->name . ' - ' . get_bloginfo( 'name' );
    }

    # Custom taxonomy literary prize
    if( is_tax( 'prize' ) ){
        $prize = get_queried_object();

        return 'Livres ayant reçu le ' . $prize->name . ' - ' . get_bloginfo( 'name' );
    }

    # Search
    if( is_search() ){
        $keyword = get_search_query();
        return 'Articles concernant le mot-clé "' . $keyword . '" - ' . get_bloginfo( 'name' );
    }

    # 404
    if( is_404() ){
        return 'Désolé, ce que vous recherchez ne se trouve malheureusement pas ici - ' . get_bloginfo( 'name' );
    }

    # Home
    if( is_home() || is_front_page() ){
        return $title;
    }

    return $title;
}

add_filter('pre_get_document_title', 'seo_title_custom_title');


/*
 * Update sharing title with SEO title and strip_tags
 */

function define_sharing_title() {
    if( get_the_ID() ){
        $id = get_the_ID();
        $meta_title = get_post_meta( $id, '_seo_title_meta_key', true );

        if( $meta_title != '' ){
            return strip_tags( $meta_title );
        }
    }

    return strip_tags( get_the_title() );
}

add_filter( 'sharing_title', 'define_sharing_title' );

?>
