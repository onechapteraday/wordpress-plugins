<?php
/**
 *
 * Plugin Name: Post Dictionary
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin gives you the possibility to add a glossary to your posts.
 * Version: 0.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */


/**
 * Create new table for dictionary data
 *
 */

function post_dictionary_install_table() {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'postdictionary_data';

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
             id int(11) NOT NULL AUTO_INCREMENT,
             post_id int(11) NOT NULL,
             entry text NOT NULL,
             information text,
             definition text,
             PRIMARY KEY  (id)
           ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    $result = dbDelta( $sql );
}

# Call the function when the plugin is activated

register_activation_hook( __FILE__, 'post_dictionary_install_table' );


/**
 * Create plugin menu
 *
 */
add_action('admin_menu', 'post_dictionary_setup_menu');

function post_dictionary_setup_menu() {
    add_menu_page( 'Gestion des dictionnaires', 'Dictionaries', 'manage_options', 'post-dictionaries', 'post_dictionary_home_page', 'dashicons-book', 21 );
}

function post_dictionary_home_page() {
    global $wpdb;

    # Retrieve all dictionaries
    $sql = "SELECT DISTINCT post_id, post_title, COUNT(d.id) as count
            FROM `wp_postdictionary_data` d, `wp_posts` p
            WHERE d.post_id = p.ID
	    GROUP BY (post_id);";

    $dictionaries = $wpdb->get_results( $sql );

    echo '<h1>Gestion des dictionnaires</h1>';

    echo '<h2>Créer un nouveau dictionnaire</h2>';

    echo '<h2>Liste des dictionnaires existants</h2>';

    if( $dictionaries ) {
        echo '<table class="wp-list-table widefat fixed striped posts">';
        echo '<thead><tr>';
        echo '<th class="column-primary">Article</th>';
        echo '<th>Entrées dans le dictionnaire</th>';
        echo '</tr></thead>';

	foreach( $dictionaries as $result ) {
	    echo '<tr>';
	    echo '<td>' . $result->post_title . '</td>';
	    echo '<td>' . $result->count . '</td>';
	    echo '</tr>';
	}

        echo '</table>';
    }
}


/**
 * Add dictionary content at the end of posts
 *
 */

function post_dictionary_add_content($content) {
    if (is_single()) {
        global $wpdb;

        $ID = get_the_ID();
        $table_name = $wpdb->prefix . 'postdictionary_data';

	$last = '';
	$sql = "SELECT entry, information, definition
	        FROM $table_name
		WHERE post_id = $ID
		ORDER BY entry ASC";
	
	$entries = $wpdb->get_results( $sql );

	if( $entries ) {
	    # Display all letters with anchor

	    $sql = "SELECT DISTINCT LEFT(entry, 1) capitale
	            FROM $table_name
		    WHERE post_id = $ID
		    ORDER BY entry ASC";

            $letters = $wpdb->get_results( $sql );

	    $content .= '<div class="post_dictionary_letters">';
	    $content .= '<h3>' . __('Par ordre alphabétique', 'post_dictionary') . '</h3>';

	    foreach( $letters as $letter ) {
	        $content .= '<a href="#letter_' . $letter->capitale . '" class="post_dictionary_capitale">' . $letter->capitale . '</a>';
	    }

	    $content .= '</div>';

	    # Display all terms in dictionary

	    $content .= '<div class="post_dictionary_data">';

	    foreach( $entries as $element ) {
                $entry = $element->entry;
                $info = $element->information;
                $def = $element->definition;

	        $current = $entry[0];

	        # Display letter

		if ($last != $current) {
		    $content .= '<div id="letter_' . $current . '" class="post_dictionary_letter">' . $current . '</div>';
		    $last = $current;
		}

		# Display term

                $content .= '<dl>';
                $content .= '<dt class="post_dictionary_term">' . $entry . '</dt>';

		if( $info != NULL ){
                    $content .= '<dd class="post_dictionary_info">' . $info . '</dd>';
		}

		if( $def != NULL ){
                    $content .= '<dd class="post_dictionary_definition">' . $def . '</dd>';
		}

                $content .= '</dl>';
            }

	    $content .= '</div>';
        }
    }

    return $content;
}

add_action('the_content', 'post_dictionary_add_content');


?>
