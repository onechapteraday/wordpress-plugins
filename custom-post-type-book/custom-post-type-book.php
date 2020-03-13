<?php
/**
 *
 * Plugin Name: Custom Post Type Book
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin creates a new post type called books.
 * Version: 0.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */

global $BOOK_TEXTDOMAIN;

$BOOK_TEXTDOMAIN = 'book-post-type';


/*
 * Make plugin available for translation.
 * Translations can be filed in the /languages directory.
 *
 */

function book_taxonomy_load_textdomain(){
  global $BOOK_TEXTDOMAIN;
  $locale = apply_filters( 'plugin_locale', get_locale(), $BOOK_TEXTDOMAIN );

  # Load i18n
  $path = basename( dirname( __FILE__ ) ) . '/languages/';
  $loaded = load_plugin_textdomain( $BOOK_TEXTDOMAIN, false, $path );
}

add_action( 'init', 'book_taxonomy_load_textdomain', 0 );


/**
 * Add new custom post type
 *
 */

function create_post_type_book(){
  global $BOOK_TEXTDOMAIN;

  register_post_type( 'book',
    array(
      'labels' => array(
          'name'                     => __( 'Books', $BOOK_TEXTDOMAIN ),
          'singular_name'            => __( 'Book', $BOOK_TEXTDOMAIN ),
          'add_new_item'             => __( 'Add New Book', $BOOK_TEXTDOMAIN ),
          'edit_item'                => __( 'Edit Book', $BOOK_TEXTDOMAIN ),
          'new_item'                 => __( 'New Book', $BOOK_TEXTDOMAIN ),
          'view_item'                => __( 'View Book', $BOOK_TEXTDOMAIN ),
          'view_items'               => __( 'View Books', $BOOK_TEXTDOMAIN ),
          'search_items'             => __( 'Search Books', $BOOK_TEXTDOMAIN ),
          'not_found'                => __( 'No books found', $BOOK_TEXTDOMAIN ),
          'not_found_in_trash'       => __( 'No books found in Trash', $BOOK_TEXTDOMAIN ),
          'all_items'                => __( 'All Books', $BOOK_TEXTDOMAIN ),
          'archives'                 => __( 'Book Archives', $BOOK_TEXTDOMAIN ),
          'attributes'               => __( 'Book Attributes', $BOOK_TEXTDOMAIN ),
          'insert_into_item'         => __( 'Insert into book', $BOOK_TEXTDOMAIN ),
          'uploaded_to_this_item'    => __( 'Uploaded to this book', $BOOK_TEXTDOMAIN ),
          'item_published'           => __( 'Book published.', $BOOK_TEXTDOMAIN ),
          'item_published_privately' => __( 'Book published privately.', $BOOK_TEXTDOMAIN ),
          'item_reverted_to_draft'   => __( 'Book reverted to draft.', $BOOK_TEXTDOMAIN ),
          'item_scheduled'           => __( 'Book scheduled.', $BOOK_TEXTDOMAIN ),
          'item_updated'             => __( 'Book updated.', $BOOK_TEXTDOMAIN ),
      ),
      'public'        => true,
      'has_archive'   => true,
      'menu_icon'     => 'dashicons-book',
      'menu_position' => 5,
      'taxonomies'    => array(
          'category',
          'person',
          'publisher',
          'collection',
          'location',
          'post_tag',
      ),
      'supports'      => array(
          'title',
          'editor',
          'excerpt',
          'custom-fields',
          'comments',
          'thumbnail',
          'publicize',
      ),
    )
  );
}

add_action( 'init', 'create_post_type_book' );


/*
 * Add custom post type book to dashboard widget activity
 *
 */

function add_custom_post_type_book_to_dashboard_activity( $query_args ){
	if( is_array( $query_args[ 'post_type' ] ) ){
	    # Set yout post type
	    $query_args[ 'post_type' ][] = 'book';
	} else {
	    $temp = array( $query_args[ 'post_type' ], 'book' );
	    $query_args[ 'post_type' ] = $temp;
	}

	return $query_args;
}

add_filter( 'dashboard_recent_posts_query_args', 'add_custom_post_type_book_to_dashboard_activity' );


/*
 * Add custom post type on dashboard 'At a glance'
 *
 */

function custom_post_type_book_at_a_glance(){
    $args = array(
        'name'     => 'book',
        '_builtin' => false,
    );

    $object = get_post_types( $args, 'objects' );

    foreach( $object as $post_type ){
        $num_posts = wp_count_posts( $post_type->name );
        $num = number_format_i18n( $num_posts->publish );
        $text = _n( strtolower( $post_type->labels->singular_name ), strtolower( $post_type->labels->name ), $num_posts->publish );

        if( current_user_can( 'edit_posts' ) ){
            $num = '<li class="post-count custom-post-type-book"><a href="edit.php?post_type=' . $post_type->name . '">' . $num . ' ' . $text . '</a></li>';
        }

        echo $num;
    }
}

add_action( 'dashboard_glance_items', 'custom_post_type_book_at_a_glance' );


/*
 * Add book metadata functions
 *
 */

function get_book_cover( $post_id ){
  return get_post_meta( $post_id, 'cover', true );
}

function get_book_excerpt( $post_id ){
  return get_post_meta( $post_id, 'excerpt', true );
}

function get_book_title_read( $post_id ){
  return get_post_meta( $post_id, 'title_read', true );
}

function get_book_title_original( $post_id ){
  return get_post_meta( $post_id, 'title_original', true );
}

function get_book_title_sort( $post_id ){
  return get_post_meta( $post_id, 'title_sort', true );
}

function get_book_pages_number( $post_id ){
  return get_post_meta( $post_id, 'pages_number', true );
}

function get_book_asin( $post_id ){
  return get_post_meta( $post_id, 'asin', true );
}

function get_book_isbn10( $post_id ){
  return get_post_meta( $post_id, 'isbn10', true );
}

function get_book_isbn13( $post_id ){
  return get_post_meta( $post_id, 'isbn13', true );
}

function get_book_price( $post_id ){
  return get_post_meta( $post_id, 'price', true );
}

function get_book_date_read( $post_id ){
  return get_post_meta( $post_id, 'date_read', true );
}

function get_book_date_release( $post_id ){
  return get_post_meta( $post_id, 'date_release', true );
}

function get_book_date_first_publication( $post_id ){
  return get_post_meta( $post_id, 'date_first_publication', true );
}

function get_book_rating( $post_id ){
  return get_post_meta( $post_id, 'rating', true );
}

function get_book_amazon( $post_id ){
  $amazon_link = get_post_meta( $post_id, 'amazon', true );

  if( $amazon_link ){
    $arr = array(
      'link'    => $amazon_link,
      'img'     => plugin_dir_url( __FILE__ ) . 'images/logo_amazon.png',
      'img_buy' => plugin_dir_url( __FILE__ ) . 'images/logo_amazon_buy.png'
    );

    return $arr;
  }

  return false;
}

function get_book_author( $post_id ){
  if( taxonomy_exists( 'person' ) ){
    $authors = array();
    $persons = get_post_meta( $post_id, 'author', true );

    if( $persons ){
      $persons = explode( ',', $persons );

      foreach( $persons as $person ){
        $person_obj = get_term_by( 'slug', $person, 'person' );

        if( $person_obj ){
          array_push( $authors, $person_obj );
        }
      }

      return $authors;
    }
  }
}

function get_book_author_preface( $post_id ){
  if( taxonomy_exists( 'person' ) ){
    $preface_authors = array();
    $persons = get_post_meta( $post_id, 'author_preface', true );

    if( $persons ){
      $persons = explode( ',', $persons );

      foreach( $persons as $person ){
        $person_obj = get_term_by( 'slug', $person, 'person' );

        if( $person_obj ){
          array_push( $preface_authors, $person_obj );
        }
      }

      return $preface_authors;
    }
  }
}

function get_book_author_postface( $post_id ){
  if( taxonomy_exists( 'person' ) ){
    $postface_authors = array();
    $persons = get_post_meta( $post_id, 'author_postface', true );

    if( $persons ){
      $persons = explode( ',', $persons );

      foreach( $persons as $person ){
        $person_obj = get_term_by( 'slug', $person, 'person' );

        if( $person_obj ){
          array_push( $postface_authors, $person_obj );
        }
      }

      return $postface_authors;
    }
  }
}

function get_book_translator( $post_id ){
  if( taxonomy_exists( 'person' ) ){
    $translators = array();
    $persons = get_post_meta( $post_id, 'translator', true );

    if( $persons ){
      $persons = explode( ',', $persons );

      foreach( $persons as $person ){
        $person_obj = get_term_by( 'slug', $person, 'person' );

        if( $person_obj ){
          array_push( $translators, $person_obj );
        }
      }

      return $translators;
    }
  }
}

function get_book_scenarist( $post_id ){
  if( taxonomy_exists( 'person' ) ){
    $scenarists = array();
    $persons = get_post_meta( $post_id, 'scenarist', true );

    if( $persons ){
      $persons = explode( ',', $persons );

      foreach( $persons as $person ){
        $person_obj = get_term_by( 'slug', $person, 'person' );

        if( $person_obj ){
          array_push( $scenarists, $person_obj );
        }
      }

      return $scenarists;
    }
  }
}

function get_book_illustrator( $post_id ){
  if( taxonomy_exists( 'person' ) ){
    $illustrators = array();
    $persons = get_post_meta( $post_id, 'illustrator', true );

    if( $persons ){
      $persons = explode( ',', $persons );

      foreach( $persons as $person ){
        $person_obj = get_term_by( 'slug', $person, 'person' );

        if( $person_obj ){
          array_push( $illustrators, $person_obj );
        }
      }

      return $illustrators;
    }
  }
}

function get_book_colourist( $post_id ){
  if( taxonomy_exists( 'person' ) ){
    $colourists = array();
    $persons = get_post_meta( $post_id, 'colourist', true );

    if( $persons ){
      $persons = explode( ',', $persons );

      foreach( $persons as $person ){
        $person_obj = get_term_by( 'slug', $person, 'person' );

        if( $person_obj ){
          array_push( $colourists, $person_obj );
        }
      }

      return $colourists;
    }
  }
}

function get_book_publisher( $post_id ){
  if( taxonomy_exists( 'publisher' ) ){
    $publishers     = get_the_terms( $post_id, 'publisher' );
    $arr_publishers = array();

    if( $publishers ){
        foreach( $publishers as $publisher ){
            if( isset ( $publisher->parent ) ){
                # If 'publisher' has parent = collection
                if( $publisher->parent ){
                    array_push( $arr_publishers, get_term_by( 'id', $publisher->parent, 'publisher' ) );
                }
                else {
                    array_push( $arr_publishers, $publisher );
                }
            }
        }
    }

    return $arr_publishers;
  }
}

function get_book_collection( $post_id ){
  if( taxonomy_exists( 'publisher' ) ){
    $publishers      = get_the_terms( $post_id, 'publisher' );
    $arr_collections = array();

    if( $publishers ){
        foreach( $publishers as $publisher ){
            if( isset( $publisher->parent ) ){
                if( $publisher->parent ){
                    array_push( $arr_collections, $publisher );
                }
            }
        }
    }

    return $arr_collections;
  }
}

?>
