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
    $post_id = $atts['id'];
    $cats = get_the_category( $post_id );

    if( isset( $cats, $cats[0] ) ){
        $cat_id = $cats[0];
        ?>
        <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
          <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="<?php echo get_bloginfo( 'url' ); ?>">
                <span itemprop="name">Accueil</span>
            </a>
            <meta itemprop="position" content="1" />
          </li>
          <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="<?php echo get_category_link( $cat_id ); ?>">
              <span itemprop="name"><?php echo $cat_id->cat_name; ?></span>
            </a>
            <meta itemprop="position" content="2" />
          </li>
          <li>
              <span itemprop="name"><?php the_title(); ?></span>
          </li>
        </ol>
        <?php
    }
}

add_shortcode( 'wp_breadcrumb_single', 'create_breadcrumb_single' );


# Create breadcrumb for category

function create_breadcrumb_category( $atts, $content=null ) {
    $cat_id = $atts['id'];

    if( isset( $cat_id ) ){
        ?>
        <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
          <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="<?php echo get_bloginfo( 'url' ); ?>">
                <span itemprop="name">Accueil</span>
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
        ?>
        <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
          <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="<?php echo get_bloginfo( 'url' ); ?>">
                <span itemprop="name">Accueil</span>
            </a>
            <meta itemprop="position" content="1" />
          </li>
          <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <span itemscope itemtype="http://schema.org/Thing" itemprop="item">
              <span itemprop="name">Étiquettes</span>
            </span>
            <meta itemprop="position" content="2" />
          </li>
          <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="<?php echo get_tag_link( $tag_id ); ?>">
              <span itemprop="name"><?php echo ucfirst( get_tag( $tag_id )->name ); ?></span>
            </a>
            <meta itemprop="position" content="3" />
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
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="<?php echo get_bloginfo( 'url' ); ?>">
            <span itemprop="name">Accueil</span>
        </a>
        <meta itemprop="position" content="1" />
      </li>
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <span itemscope itemtype="http://schema.org/Thing" itemprop="item">
          <span itemprop="name">Maisons d'édition</span>
        </span>
        <meta itemprop="position" content="2" />
      </li>
      <?php
	if( $publisher->parent > 0 ){
	  $parent_id = $publisher->parent;

          $parent      = get_term( $parent_id, 'publisher' );
          $parent_link = get_term_link( $parent, 'publisher' );
          ?>
          <li>
            <a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="<?php echo $parent_link; ?>">
              <span itemprop="name"><?php echo $parent->name; ?></span>
            </a>
            <meta itemprop="position" content="3" />
          </li>
          <?php
	}
      ?>
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="<?php echo $publisher_link; ?>">
          <span itemprop="name"><?php echo $publisher->name; ?></span>
        </a>
        <meta itemprop="position" content="<?php if( $publisher->parent > 0 ){ echo '4'; }else{ echo '3'; } ?>" />
      </li>
    </ol>
    <?php
}

add_shortcode( 'wp_breadcrumb_publisher', 'create_breadcrumb_publisher' );


# Create breadcrumb for persons

function create_breadcrumb_person( $atts, $content=null ) {
    $person_id = $atts['id'];

    $person      = get_term( $person_id, 'person' );
    $person_link = get_term_link( $person, 'person' );

    ?>
    <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="<?php echo get_bloginfo( 'url' ); ?>">
            <span itemprop="name">Accueil</span>
        </a>
        <meta itemprop="position" content="1" />
      </li>
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <span itemscope itemtype="http://schema.org/Thing" itemprop="item">
          <span itemprop="name">Personnes</span>
        </span>
        <meta itemprop="position" content="2" />
      </li>
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="<?php echo $person_link; ?>">
          <span itemprop="name"><?php echo $person->name; ?></span>
        </a>
        <meta itemprop="position" content="3" />
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
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="<?php echo get_bloginfo( 'url' ); ?>">
            <span itemprop="name">Accueil</span>
        </a>
        <meta itemprop="position" content="1" />
      </li>
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <span itemscope itemtype="http://schema.org/Thing" itemprop="item">
          <span itemprop="name">Localisations</span>
        </span>
        <meta itemprop="position" content="2" />
      </li>
      <?php
	$i = 3;
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
          <li>
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
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="<?php echo get_bloginfo( 'url' ); ?>">
            <span itemprop="name">Accueil</span>
        </a>
        <meta itemprop="position" content="1" />
      </li>
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <span itemscope itemtype="http://schema.org/Thing" itemprop="item">
          <span itemprop="name">Auteurs</span>
        </span>
        <meta itemprop="position" content="2" />
      </li>
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="<?php echo get_the_author_meta( 'user_url' ); ?>">
          <span itemprop="name"><?php echo ucfirst( get_the_author_meta( 'display_name', $author_id ) ); ?></span>
        </a>
        <meta itemprop="position" content="3" />
      </li>
    </ol>
    <?php
}

add_shortcode( 'wp_breadcrumb_author', 'create_breadcrumb_author' );


# Create breadcrumb for search page

function create_breadcrumb_search( $atts, $content=null ) {
    ?>
    <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="<?php echo get_bloginfo( 'url' ); ?>">
            <span itemprop="name">Accueil</span>
        </a>
        <meta itemprop="position" content="1" />
      </li>
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <span itemscope itemtype="http://schema.org/Thing" itemprop="item">
          <span itemprop="name">Recherche</span>
        </span>
        <meta itemprop="position" content="2" />
      </li>
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <span itemscope itemtype="http://schema.org/Thing" itemprop="item">
          <span itemprop="name"><?php echo ucfirst( get_search_query() ); ?></span>
        </span>
        <meta itemprop="position" content="3" />
      </li>
    </ol>
    <?php
}

add_shortcode( 'wp_breadcrumb_search', 'create_breadcrumb_search' );


# Create breadcrumb for 404

function create_breadcrumb_error_page( $atts, $content=null ) {
    ?>
    <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="<?php echo get_bloginfo( 'url' ); ?>">
            <span itemprop="name">Accueil</span>
        </a>
        <meta itemprop="position" content="1" />
      </li>
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <span itemscope itemtype="http://schema.org/Thing" itemprop="item">
          <span itemprop="name">Erreur 404</span>
        </span>
        <meta itemprop="position" content="2" />
      </li>
    </ol>
    <?php
}

add_shortcode( 'wp_breadcrumb_404', 'create_breadcrumb_error_page' );


# Create breadcrumb for archives

function create_breadcrumb_archive( $atts, $content=null ) {
    ?>
    <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="<?php echo get_bloginfo( 'url' ); ?>">
            <span itemprop="name">Accueil</span>
        </a>
        <meta itemprop="position" content="1" />
      </li>
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <span itemscope itemtype="http://schema.org/Thing" itemprop="item">
          <span itemprop="name">Archives</span>
        </span>
        <meta itemprop="position" content="2" />
      </li>
      <?php
      if( is_year() ){
          ?>
          <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <span itemscope itemtype="http://schema.org/Thing" itemprop="item">
              <span itemprop="name"><?php echo get_the_date( _x( 'Y', 'yearly archives date format' ) ); ?></span>
            </span>
            <meta itemprop="position" content="3" />
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
            <meta itemprop="position" content="3" />
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
            <meta itemprop="position" content="3" />
          </li>
	  <?php
      }
      ?>
    </ol>
    <?php
}

add_shortcode( 'wp_breadcrumb_archive', 'create_breadcrumb_archive' );

?>
