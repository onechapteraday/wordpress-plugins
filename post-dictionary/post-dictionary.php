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
    add_menu_page( 'Gestion des dictionnaires', 'Dictionnaires', 'manage_options', 'post-dictionaries', 'post_dictionary_home_page', 'dashicons-book', 21 );
    add_submenu_page('post-dictionaries', 'Création d\'un nouveau dictionnaire', 'Créer un dictionnaire', 'administrator', 'post-dictionaries-create', 'post_dictionary_create_page');
}

function post_dictionary_home_page() {
    global $wpdb;

    $post_id = $_GET['post_id'];
    $entry_id = $_GET['entry_id'];

    # Modify entry from dictionary $post_id

    if( $entry_id ) {
	$action = $_GET['action'];

	if( $action == 'edit' ) {
            post_dictionary_edit_entry($entry_id);
	}

	if( $action == 'delete' ) {
            post_dictionary_delete_entry($entry_id);
	}
    }

    # Display dictionary for post_id

    elseif( $post_id ) {
        post_dictionary_list_page($post_id);
    }

    # If no dictionary has to be displayed
    else {
	$posts_table = $wpdb->prefix . 'posts';
	$plugin_table = $wpdb->prefix . 'postdictionary_data';

        # Retrieve all dictionaries
        $sql = "SELECT DISTINCT post_id, post_title, COUNT(d.id) as count
                FROM $plugin_table d, $posts_table p
                WHERE d.post_id = p.ID
                GROUP BY (post_id);";

        $dictionaries = $wpdb->get_results( $sql );

        echo '<h1>Gestion des dictionnaires</h1>';
        echo '<p>Ci-dessous, vous trouverez la liste de tous les dictionnaires existants sur votre blog.</p>';

        echo '<h2>Liste des dictionnaires existants</h2>';

        if( $dictionaries ) {
            echo '<table class="wp-list-table widefat fixed striped posts">';
            echo '<thead><tr>';
            echo '<th class="column-primary">Articles</th>';
            echo '<th>Entrées dans le dictionnaire</th>';
            echo '</tr></thead>';

            foreach( $dictionaries as $result ) {
                $path = 'admin.php?page=post-dictionaries&post_id=' . $result->post_id;

                echo '<tr>';
                echo '<td>' . $result->post_title;
                echo '<div class="row-actions visible">';
                    echo '<span class="activate"><a href="' . admin_url($path) . '" class="edit" aria-label="Voir ' . $result->title . '">Voir dictionnaire</a> | </span>';
                echo '</div>';
                echo '</td>';
                echo '<td>' . $result->count . '</td>';
                echo '</tr>';
            }

            echo '</table>';
        }
    }
}

function post_dictionary_create_page() {
    global $wpdb;

    echo '<h1>Création d\'un nouveau dictionnaire</h1>';
}

function post_dictionary_list_page($post_id) {
    global $wpdb;

    if( $post_id ) {
	# Display post title
	$posts_table = $wpdb->prefix . 'posts';

	$sql = "SELECT post_title
	        FROM $posts_table
		WHERE ID = $post_id;";
	
	$post = $wpdb->get_results( $sql );
	$post_title = $post[0]->post_title;

        echo '<h1>Dictionnaire de l\'article <strong>' . $post_title . '</strong></h1>';
	echo '<p>Voici la liste ordonnée des éléments présents dans le dictionnaire de cet article.</p>';

	# Display dictionary

        $table_name = $wpdb->prefix . 'postdictionary_data';

        $sql = "SELECT id, entry, information, definition
                FROM $table_name
                WHERE post_id = $post_id
                ORDER BY entry ASC";

        $entries = $wpdb->get_results( $sql );

        if( $entries ) {
            echo '<table class="wp-list-table widefat fixed striped posts">';
            echo '<thead><tr>';
            echo '<th class="column-primary">Entrée</th>';
            echo '<th>Information</th>';
            echo '<th>Définition</th>';
            echo '<th>Actions</th>';
            echo '</tr></thead>';

            foreach( $entries as $element ) {
                $path = 'admin.php?page=post-dictionaries&post_id=' . $post_id . '&entry_id=' . $element->id;

                echo '<tr>';
                echo '<td>' . $element->entry . '</td>';
                echo '<td>' . $element->information . '</td>';
                echo '<td>' . $element->definition . '</td>';
                echo '<td>';
		    echo '<a href="' . admin_url($path . '&action=edit'). '"><span class="dashicons dashicons-edit"></span> Éditer</a>';
		    echo ' | <a href="' . admin_url($path . '&action=delete'). '"><span class="dashicons dashicons-trash"></span> Supprimer</a></td>';
                echo '</tr>';
            }

            echo '</table>';
        }
    } else {
        echo '<p>Veuiller sélectionner un dictionnaire existant.</p>';
    }
}

function post_dictionary_edit_entry( $entry_id ) {
  echo '<h1>Éditer entrée du dictionnaire</h1>';
}

function post_dictionary_delete_entry( $entry_id ) {
  echo '<h1>Supprimer entrée du dictionnaire</h1>';
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
