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


/**
 * Rewrite title tag for SEO
 */

function twentysixteen_child_custom_title( $title ){
    $title = 'One chapter a day, un chapitre... presque tous les jours';

    # Single post and page
    if( ( is_single() || is_page() ) && !is_page( 'Front page' ) ){
        return get_the_title();
    }

    # Category
    if( is_category() ){
        $cat = get_queried_object();
        return 'Articles de la catégorie ' . $cat->name . ' - ' . get_bloginfo( 'name' );
    }

    # Tag
    if( is_tag() ){
        $tag = get_queried_object();
        return 'Articles concernant l\'étiquette "' . ucfirst( $tag->name ) . '" - ' . get_bloginfo( 'name' );
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
            return 'Articles concernant la collection ' . $publisher->name . ' de la maison d\'édition ' . $parent->name . ' - ' . get_bloginfo( 'name' );
        }

        return 'Articles concernant la maison d\'édition ' . $publisher->name . ' - ' . get_bloginfo( 'name' );
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
    if( is_home() ){
        return $title;
    }

    return $title;
}

add_filter('pre_get_document_title', 'twentysixteen_child_custom_title');

?>
