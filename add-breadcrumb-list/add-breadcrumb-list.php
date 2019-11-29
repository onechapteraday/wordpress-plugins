<?php
/**
 *
 * Plugin Name: Add Breadcrumb List
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin creates a breadcrumb list for pages
 * Version: 0.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */


# Create breadcrumb for single posts

function create_breadcrumb_single( $atts, $content=null ) {
    global $wpdb;

    # Retrieve post_id and post_category
    $post_id = $atts['id'];
    $cats = get_the_category( $post_id );

    # Check if single post has a dictionary (cf. post-dictionary plugin)
    $charset_collate = $wpdb->get_charset_collate();
    $table_name      = $wpdb->prefix . 'postdictionary_data';
    $has_dictionary  = 0;

    $sql = "SELECT EXISTS( SELECT *
            FROM information_schema.tables
            WHERE table_name = 'wp_postdictionary_data'
            LIMIT 1) as `has_table`;";

    $result = $wpdb->get_results( $sql );
    $has_table = $result[0]->has_table;

    if( $has_table ){
        $sql = "SELECT EXISTS( SELECT *
                FROM $table_name
                WHERE post_id = $post_id
                ) as `has_dictionary`;";

        $result = $wpdb->get_results( $sql );
        $has_dictionary = $result[0]->has_dictionary;
    }

    # Display breadcrumb
    if( isset( $cats, $cats[0] ) ){
        $cat_id = $cats[0];
        ?>
        <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
          <li>
            <a href="<?php echo get_bloginfo( 'url' ); ?>">
                <span>Accueil</span>
            </a>
          </li>
          <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <a itemprop="item" href="<?php echo get_category_link( $cat_id ); ?>">
              <span itemprop="name"><?php echo $cat_id->cat_name; ?></span>
            </a>
            <meta itemprop="position" content="1" />
          </li>
          <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <span itemscope itemtype="http://schema.org/Thing" itemprop="item">
              <span itemprop="name">
	      <?php
	      if( is_singular( 'book' ) ){
		  echo 'Livres';
	      }
	      else if( is_singular( 'album' ) ){
                  if( has_tag( 'single' ) ){
		      echo 'Singles';
                  }
                  else {
		      echo 'Albums';
                  }
	      }
	      else if( is_singular( 'interview' ) ){
		  echo 'Interviews';
	      }
	      else if( is_singular( 'concert' ) ){
		  echo 'Concerts';
	      }
	      else if( $has_dictionary == 1 ){
		  echo 'Dictionnaires';
	      }
	      else{
		  echo 'Articles';
	      }
	      ?>
	      </span>
            </span>
            <meta itemprop="position" content="2" />
          </li>
          <li>
              <span><?php the_title(); ?></span>
          </li>
        </ol>
        <?php
    }
}

add_shortcode( 'wp_breadcrumb_single', 'create_breadcrumb_single' );


# Create breadcrumb for images

function create_breadcrumb_image( $atts, $content=null ) {
    global $wpdb;

    # Retrieve post_id and post_category
    $post_id = $atts['id'];

    # Display breadcrumb
    ?>
    <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
      <li>
        <a href="<?php echo get_bloginfo( 'url' ); ?>">
            <span>Accueil</span>
        </a>
      </li>
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <a href="<?php echo get_bloginfo( 'url' ); ?>/archives" itemprop="item">
          <span itemprop="name">Archives</span>
        </a>
        <meta itemprop="position" content="1" />
      </li>
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <span itemscope itemtype="http://schema.org/Thing" itemprop="item">
          <span itemprop="name">Images</span>
        </span>
        <meta itemprop="position" content="2" />
      </li>
      <li>
          <span><?php the_title(); ?></span>
      </li>
    </ol>
    <?php
}

add_shortcode( 'wp_breadcrumb_image', 'create_breadcrumb_image' );


# Create breadcrumb for category

function create_breadcrumb_category( $atts, $content=null ) {
    $cat_id = $atts['id'];

    if( isset( $cat_id ) ){
        ?>
        <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
          <li>
            <a href="<?php echo get_bloginfo( 'url' ); ?>">
                <span>Accueil</span>
            </a>
          </li>
          <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <a itemprop="item" href="<?php echo get_bloginfo( 'url' ); ?>/archives">
              <span itemprop="name">Archives</span>
            </a>
            <meta itemprop="position" content="1" />
          </li>
          <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="<?php echo get_category_link( $cat_id ); ?>">
              <span itemprop="name"><?php echo get_category( $cat_id )->name; ?></span>
            </a>
            <meta itemprop="position" content="2" />
          </li>
        </ol>
        <?php
    }
}

add_shortcode( 'wp_breadcrumb_category', 'create_breadcrumb_category' );


# Create breadcrumb for tag

function create_breadcrumb_tag( $atts, $content=null ) {
    $tag_id = $atts['id'];

    if( isset( $tag_id ) ){
        $tag_name = get_tag( $tag_id )->name;
        ?>
        <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
          <li>
            <a href="<?php echo get_bloginfo( 'url' ); ?>">
                <span>Accueil</span>
            </a>
          </li>
          <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <a href="<?php echo get_bloginfo( 'url' ); ?>/tags" itemprop="item">
              <span itemprop="name">Étiquettes</span>
            </a>
            <meta itemprop="position" content="1" />
          </li>
          <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="<?php echo get_tag_link( $tag_id ); ?>">
              <span itemprop="name"><?php echo mb_strtoupper( mb_substr( $tag_name, 0, 1 )) . mb_substr( $tag_name, 1 ); ?></span>
            </a>
            <meta itemprop="position" content="2" />
          </li>
        </ol>
        <?php
    }
}

add_shortcode( 'wp_breadcrumb_tag', 'create_breadcrumb_tag' );


# Create breadcrumb for publishers

function create_breadcrumb_publisher( $atts, $content=null ) {
    $publisher_id = $atts['id'];

    $publisher      = get_term( $publisher_id, 'publisher' );
    $publisher_link = get_term_link( $publisher, 'publisher' );

    ?>
    <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
      <li>
        <a href="<?php echo get_bloginfo( 'url' ); ?>">
            <span>Accueil</span>
        </a>
      </li>
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <a href="<?php echo get_bloginfo( 'url' ); ?>/publishers" itemprop="item">
          <span itemprop="name">Maisons d'édition</span>
        </a>
        <meta itemprop="position" content="1" />
      </li>
      <?php
	if( $publisher->parent > 0 ){
	  $parent_id = $publisher->parent;

          $parent      = get_term( $parent_id, 'publisher' );
          $parent_link = get_term_link( $parent, 'publisher' );
          ?>
          <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="<?php echo $parent_link; ?>">
              <span itemprop="name"><?php echo $parent->name; ?></span>
            </a>
            <meta itemprop="position" content="2" />
          </li>
          <?php
	}
      ?>
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="<?php echo $publisher_link; ?>">
          <span itemprop="name"><?php echo $publisher->name; ?></span>
        </a>
        <meta itemprop="position" content="<?php if( $publisher->parent > 0 ){ echo '3'; }else{ echo '2'; } ?>" />
      </li>
    </ol>
    <?php
}

add_shortcode( 'wp_breadcrumb_publisher', 'create_breadcrumb_publisher' );


# Create breadcrumb for prizes

function create_breadcrumb_prize( $atts, $content=null ) {
    $prize_id = $atts['id'];

    $prize      = get_term( $prize_id, 'prize' );
    $prize_link = get_term_link( $prize, 'prize' );

    ?>
    <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
      <li>
        <a href="<?php echo get_bloginfo( 'url' ); ?>">
            <span>Accueil</span>
        </a>
      </li>
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <a href="<?php echo get_bloginfo( 'url' ); ?>/literary-prizes" itemprop="item">
          <span itemprop="name">Prix littéraires</span>
        </a>
        <meta itemprop="position" content="1" />
      </li>
      <?php
	if( $prize->parent > 0 ){
	  $parent_id = $prize->parent;

          $parent      = get_term( $parent_id, 'prize' );
          $parent_link = get_term_link( $parent, 'prize' );
          ?>
          <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="<?php echo $parent_link; ?>">
              <span itemprop="name"><?php echo $parent->name; ?></span>
            </a>
            <meta itemprop="position" content="2" />
          </li>
          <?php
	}
      ?>
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="<?php echo $prize_link; ?>">
          <span itemprop="name"><?php echo $prize->name; ?></span>
        </a>
        <meta itemprop="position" content="<?php if( $prize->parent > 0 ){ echo '3'; }else{ echo '2'; } ?>" />
      </li>
    </ol>
    <?php
}

add_shortcode( 'wp_breadcrumb_prize', 'create_breadcrumb_prize' );


# Create breadcrumb for persons

function create_breadcrumb_person( $atts, $content=null ) {
    $person_id = $atts['id'];

    $person      = get_term( $person_id, 'person' );
    $person_link = get_term_link( $person, 'person' );

    ?>
    <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
      <li>
        <a href="<?php echo get_bloginfo( 'url' ); ?>">
            <span>Accueil</span>
        </a>
      </li>
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <a href="<?php echo get_bloginfo( 'url' ); ?>/persons" itemprop="item">
          <span itemprop="name">Personnes</span>
        </a>
        <meta itemprop="position" content="1" />
      </li>
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="<?php echo $person_link; ?>">
          <span itemprop="name"><?php echo $person->name; ?></span>
        </a>
        <meta itemprop="position" content="2" />
      </li>
    </ol>
    <?php
}

add_shortcode( 'wp_breadcrumb_person', 'create_breadcrumb_person' );


# Create breadcrumb for locations

function create_breadcrumb_location( $atts, $content=null ) {
    $location_id = $atts['id'];

    $location      = get_term( $location_id, 'location' );
    $location_link = get_term_link( $location, 'location' );

    ?>
    <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
      <li>
        <a href="<?php echo get_bloginfo( 'url' ); ?>">
            <span>Accueil</span>
        </a>
      </li>
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <a href="<?php echo get_bloginfo( 'url' ); ?>/locations" itemprop="item">
          <span itemprop="name">Localisations</span>
        </a>
        <meta itemprop="position" content="1" />
      </li>
      <?php
	$i = 2;
	$parent_id = $location->parent;
        $locations_array = array();

	# While location still has a parent, search for it
	while( $parent_id > 0 ){
	  array_unshift( $locations_array, $parent_id );

          $parent      = get_term( $parent_id, 'location' );
	  $parent_id = $parent->parent;
	}

	# When all locations are retrieved, display them in the correct order
        foreach( $locations_array as $parent_id ){
          $parent      = get_term( $parent_id, 'location' );
          $parent_link = get_term_link( $parent, 'location' );
          ?>
          <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="<?php echo $parent_link; ?>">
              <span itemprop="name"><?php echo __( $parent->name, 'location-taxonomy' ); ?></span>
            </a>
            <meta itemprop="position" content="<?php echo $i; ?>" />
          </li>
          <?php
	  $i = $i + 1;
	}
      ?>
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="<?php echo $location_link; ?>">
          <span itemprop="name"><?php echo __( $location->name, 'location-taxonomy' ); ?></span>
        </a>
        <meta itemprop="position" content="<?php echo $i; ?>" />
      </li>
    </ol>
    <?php
}

add_shortcode( 'wp_breadcrumb_location', 'create_breadcrumb_location' );


# Create breadcrumb for authors

function create_breadcrumb_author( $atts, $content=null ) {
    $author_id = $atts['id'];

    ?>
    <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
      <li>
        <a href="<?php echo get_bloginfo( 'url' ); ?>">
            <span>Accueil</span>
        </a>
      </li>
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <span itemscope itemtype="http://schema.org/Thing" itemprop="item">
          <span itemprop="name">Auteurs</span>
        </span>
        <meta itemprop="position" content="1" />
      </li>
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="<?php echo get_the_author_meta( 'user_url' ); ?>">
          <span itemprop="name"><?php echo ucfirst( get_the_author_meta( 'display_name', $author_id ) ); ?></span>
        </a>
        <meta itemprop="position" content="2" />
      </li>
    </ol>
    <?php
}

add_shortcode( 'wp_breadcrumb_author', 'create_breadcrumb_author' );


# Create breadcrumb for search page

function create_breadcrumb_search( $atts, $content=null ) {
    ?>
    <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
      <li>
        <a href="<?php echo get_bloginfo( 'url' ); ?>">
            <span>Accueil</span>
        </a>
      </li>
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <a href="<?php echo get_bloginfo( 'url' ); ?>/archives" itemprop="item">
          <span itemprop="name">Archives</span>
        </a>
        <meta itemprop="position" content="1" />
      </li>
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <span itemscope itemtype="http://schema.org/Thing" itemprop="item">
          <span itemprop="name">Résultats de recherche pour&nbsp;: <?php echo get_search_query(); ?></span>
        </span>
        <meta itemprop="position" content="2" />
      </li>
    </ol>
    <?php
}

add_shortcode( 'wp_breadcrumb_search', 'create_breadcrumb_search' );


# Create breadcrumb for 404

function create_breadcrumb_error_page( $atts, $content=null ) {
    ?>
    <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
      <li>
        <a href="<?php echo get_bloginfo( 'url' ); ?>">
            <span>Accueil</span>
        </a>
      </li>
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <span itemscope itemtype="http://schema.org/Thing" itemprop="item">
          <span itemprop="name">Erreur 404</span>
        </span>
        <meta itemprop="position" content="1" />
      </li>
    </ol>
    <?php
}

add_shortcode( 'wp_breadcrumb_404', 'create_breadcrumb_error_page' );


# Create breadcrumb for archives

function create_breadcrumb_archive( $atts, $content=null ) {
    ?>
    <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
      <li>
        <a href="<?php echo get_bloginfo( 'url' ); ?>">
            <span>Accueil</span>
        </a>
      </li>
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <a itemprop="item" href="<?php echo get_bloginfo( 'url' ); ?>/archives">
          <span itemprop="name">Archives</span>
        </a>
        <meta itemprop="position" content="1" />
      </li>
      <?php
      if( is_year() ){
          ?>
          <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <span itemscope itemtype="http://schema.org/Thing" itemprop="item">
              <span itemprop="name"><?php echo get_the_date( _x( 'Y', 'yearly archives date format' ) ); ?></span>
            </span>
            <meta itemprop="position" content="2" />
          </li>
	  <?php
      }
      ?>
      <?php
      if( is_month() ){
          ?>
          <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <span itemscope itemtype="http://schema.org/Thing" itemprop="item">
              <span itemprop="name"><?php echo ucfirst( get_the_date( _x( 'F Y', 'monthly archives date format' ) ) ); ?></span>
            </span>
            <meta itemprop="position" content="2" />
          </li>
	  <?php
      }
      ?>
      <?php
      if( is_day() ){
          ?>
          <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <span itemscope itemtype="http://schema.org/Thing" itemprop="item">
              <span itemprop="name"><?php echo get_the_date( _x( 'j F Y', 'daily archives date format' ) ); ?></span>
            </span>
            <meta itemprop="position" content="2" />
          </li>
	  <?php
      }
      ?>
    </ol>
    <?php
}

add_shortcode( 'wp_breadcrumb_archive', 'create_breadcrumb_archive' );


# Create breadcrumb for taxonomy pages

function create_breadcrumb_taxonomy( $atts, $content=null ) {
    $slug = $atts['page'];
    ?>
    <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
      <li>
        <a href="<?php echo get_bloginfo( 'url' ); ?>">
            <span>Accueil</span>
        </a>
      </li>
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="<?php esc_url( the_permalink() ); ?>">
            <?php
            if( $slug == 'tag' ){
                ?><span itemprop="name">Étiquettes</span><?php
            }

            if( $slug == 'person' ){
                ?><span itemprop="name">Personnes</span><?php
            }

            if( $slug == 'prize' ){
                ?><span itemprop="name">Prix littéraires</span><?php
            }

            if( $slug == 'location' ){
                ?><span itemprop="name">Localisations</span><?php
            }

            if( $slug == 'publisher' ){
                ?><span itemprop="name">Maisons d'édition</span><?php
            }
            ?>
        </a>
        <meta itemprop="position" content="1" />
      </li>
    </ol>
    <?php
}

add_shortcode( 'wp_breadcrumb_taxonomy', 'create_breadcrumb_taxonomy' );

?>
