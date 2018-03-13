<?php
/**
 *
 * Plugin Name: Add Breadcrumb List
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin creates a breadcrumb list to use in single posts
 * Version: 0.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */

function create_breadcrumb_single( $atts, $content=null ) {
    $post_id = $atts['id'];
    $cats = get_the_category( $post_id );

    if( isset( $cats, $cats[0] ) ){
        $cat_id = $cats[0];
        ?>
        <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
          <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="<?php echo get_bloginfo( 'url' ); ?>">
                <span itemprop="name"><?php echo get_bloginfo( 'name' ); ?></span>
            </a>
            <meta itemprop="position" content="1" />
          </li>
          ›
          <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="<?php echo get_category_link( $cat_id ); ?>">
              <span itemprop="name"><?php echo $cat_id->cat_name; ?></span>
            </a>
            <meta itemprop="position" content="2" />
          </li>
          ›
          <li>
              <span itemprop="name"><?php the_title(); ?></span>
          </li>
        </ol>
        <?php
    }
}

add_shortcode( 'wp_breadcrumb_single', 'create_breadcrumb_single' );

?>
