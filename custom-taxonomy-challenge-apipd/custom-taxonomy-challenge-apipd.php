<?php
/**
 *
 * Plugin Name: Custom Taxonomy APIPD Challenge Video
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin allows you to display the APIPD Challenge videos.
 * Version: 0.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */

global $CHALLENGE_APIPD_VIDEO_TEXTDOMAIN;

$CHALLENGE_APIPD_VIDEO_TEXTDOMAIN = 'challenge-apipd-video-taxonomy';


/*
 * Make plugin available for translation.
 * Translations can be filed in the /languages directory.
 *
 */

function challenge_apipd_video_taxonomy_load_textdomain(){
    global $CHALLENGE_APIPD_VIDEO_TEXTDOMAIN;
    $locale = apply_filters( 'plugin_locale', get_locale(), $CHALLENGE_APIPD_VIDEO_TEXTDOMAIN );

    # Load i18n
    $path = basename( dirname( __FILE__ ) ) . '/languages/';
    $loaded = load_plugin_textdomain( $CHALLENGE_APIPD_VIDEO_TEXTDOMAIN, false, $path );
}

add_action( 'init', 'challenge_apipd_video_taxonomy_load_textdomain', 0 );


/*
 * Add plugin styles.
 * CSS can be found in the /css directory.
 *
 */

function challenge_apipd_video_add_stylesheet() {
    wp_register_style( 'challenge-apipd-video-styles', plugins_url( 'css/styles.css', __FILE__ ) );
    wp_enqueue_style(  'challenge-apipd-video-styles' );
}

add_action( 'wp_enqueue_scripts', 'challenge_apipd_video_add_stylesheet' );


/**
 * Add release item taxonomy
 *
 **/

function add_challenge_apipd_video_taxonomy(){
  global $CHALLENGE_APIPD_VIDEO_TEXTDOMAIN;

  $labels = array (
    'name'                       => _x( 'APIPD Challenge Videos', 'taxonomy general name', $CHALLENGE_APIPD_VIDEO_TEXTDOMAIN ),
    'singular_name'              => _x( 'APIPD Challenge Video', 'taxonomy singular name', $CHALLENGE_APIPD_VIDEO_TEXTDOMAIN ),
    'search_items'               => __( 'Search APIPD Challenge Videos', $CHALLENGE_APIPD_VIDEO_TEXTDOMAIN ),
    'popular_items'              => __( 'Popular APIPD Challenge Videos', $CHALLENGE_APIPD_VIDEO_TEXTDOMAIN ),
    'all_items'                  => __( 'All APIPD Challenge Videos', $CHALLENGE_APIPD_VIDEO_TEXTDOMAIN ),
    'parent_item'                => null,
    'parent_item_colon'          => null,
    'view_item'                  => __( 'See APIPD Challenge Video', $CHALLENGE_APIPD_VIDEO_TEXTDOMAIN ),
    'edit_item'                  => __( 'Edit APIPD Challenge Video', $CHALLENGE_APIPD_VIDEO_TEXTDOMAIN ),
    'update_item'                => __( 'Update APIPD Challenge Video', $CHALLENGE_APIPD_VIDEO_TEXTDOMAIN ),
    'add_new_item'               => __( 'Add New APIPD Challenge Video', $CHALLENGE_APIPD_VIDEO_TEXTDOMAIN ),
    'new_item_name'              => __( 'New APIPD Challenge Video Name', $CHALLENGE_APIPD_VIDEO_TEXTDOMAIN ),
    'separate_items_with_commas' => __( 'Separate APIPD challenge videos with commas', $CHALLENGE_APIPD_VIDEO_TEXTDOMAIN ),
    'add_or_remove_items'        => __( 'Add or remove APIPD challenge videos', $CHALLENGE_APIPD_VIDEO_TEXTDOMAIN ),
    'choose_from_most_used'      => __( 'Choose from the most used APIPD challenge videos', $CHALLENGE_APIPD_VIDEO_TEXTDOMAIN ),
    'not_found'                  => __( 'No APIPD challenge videos found.', $CHALLENGE_APIPD_VIDEO_TEXTDOMAIN ),
    'back_to_items'              => __( '← Back to APIPD challenge videos', $CHALLENGE_APIPD_VIDEO_TEXTDOMAIN ),
    'menu_name'                  => __( 'APIPD Challenge Videos', $CHALLENGE_APIPD_VIDEO_TEXTDOMAIN ),
  );

  $args = array (
    'hierarchical'          => false,
    'labels'                => $labels,
    'public'                => false,
    'show_ui'               => true,
    'update_count_callback' => '_update_post_term_count',
    'query_var'             => true,
    'rewrite'               => array( 'slug' => 'challenge-apipd-video', 'with_front' => 'false', 'hierarchical' => false ),
  );

  register_taxonomy( 'challenge_apipd_video', 'page', $args );
}

add_action( 'init', 'add_challenge_apipd_video_taxonomy', 1 );


/**
 * Flush rewrites when the plugin is activated
 *
 */

function challenge_apipd_video_taxonomy_flush_rewrites(){
  flush_rewrite_rules();
}


# Prevent 404 errors on APIPD challenge videos' archive

register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
register_activation_hook( __FILE__, 'challenge_apipd_video_taxonomy_flush_rewrites' );
add_action( 'init', 'challenge_apipd_video_taxonomy_flush_rewrites', 0 );


/**
 * Create challenge APIPD shortcode
 *
 * @param array  $atts
 * @param string $content
 *
 */

function custom_page_nav( $totalpages, $page, $end_size, $mid_size )
{
    $bignum = 999999999;

    if ( $totalpages <= 1 || $page > $totalpages ) return;

    return paginate_links( array(
        'base'          => str_replace( $bignum, '%#%', esc_url( get_pagenum_link( $bignum ) ) ),
        'format'        => '',
        'current'       => max( 1, $page ),
        'total'         => $totalpages,
        'prev_text'     => '«',
        'next_text'     => '»',
        'type'          => 'list',
        'show_all'      => false,
        'end_size'      => $end_size,
        'mid_size'      => $mid_size
    ) );
}

function display_all_challenge_videos( $atts, $content=null ){
    global $wpdb;
    global $RELEASE_ITEM_TEXTDOMAIN;

    # Add pagination

    $number       = 5; # Number of terms to display per page
    $page         = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
    $offset       = ( $page > 0 ) ?  $number * ( $page - 1 ) : 1;
    $totalterms   = wp_count_terms( 'challenge_apipd_video' );
    $totalpages   = ceil( $totalterms / $number );

    # Get all videos

    $items  = get_terms( array(
        'taxonomy'    =>  'challenge_apipd_video',
        'hide_empty'  =>  'false',
        'get'         =>  'all',
        'hide_empty'  =>  true,
        'number'      =>  $number,
        'offset'      =>  $offset,
    ) );

    # Display all data

    ob_start();

    ?>
    <div class="apipd-challenge-videos">
        <div id="fb-root"></div>
        <script async defer crossorigin="anonymous" src="https://connect.facebook.net/fr_FR/sdk.js#xfbml=1&version=v7.0&appId=837753662999999&autoLogAppEvents=1" nonce="X5p0e9Qj"></script>
        <?php
        if( $page == 1 ){
            ?>
            <h2 class="first">Un appel à la sensibiliation de tous à la drépanocytose</h2>
            <p>Jenny HIPPOCRATE FIXY, présidente de l’APIPD, explique ce qu’est le challenge #brisonslesilence.</p>
            <br />
            <div class="fb-video" data-href="https://www.facebook.com/video.php?v=269573344187401"  data-width="600" data-allowfullscreen="true"></div>
            <br />
            <?php
        }
        ?>
        <h2>Un soutien massif</h2>
        <p>#CHALLENGE | Aidez l’APIPD à sortir la drépanocytose du silence en participant au 📣 𝐂𝐇𝐀𝐋𝐋𝐄𝐍𝐆𝐄 𝐁𝐑𝐈𝐒𝐎𝐍𝐒 𝐋𝐄 𝐒𝐈𝐋𝐄𝐍𝐂𝐄 📣</p>
        <p>🎥 Envoyez vos vidéos (d’une durée maximale de 60 secondes) par mail à <a href="mailto:apipd@free.fr">apipd@free.fr</a></p>
        <p>N'oubliez pas le hashtag #brisonslesilence.</p>
        <p class="center">* * *</p>
        <p>Découvrez ci-après les vidéos du challenge.</p>
        <br />
        <?php

        foreach( $items as $item ){
            $video_id = $item->slug;
            ?>
            <div class="fb-video" data-href="https://www.facebook.com/video.php?v=<?php echo $video_id; ?>"  data-width="600" data-allowfullscreen="true"></div>
            <br />
            &nbsp;
            <br />
            <?php
        }

        # Show custom page navigation
        printf(
            '<nav class="pagination">%s</nav>',
            custom_page_nav( $totalpages, $page, 2, 1 )
        );

        ?>
    </div>
    <?php

    return ob_get_clean();
}

add_shortcode( 'wp_challenge_apipd_videos', 'display_all_challenge_videos' );


?>
