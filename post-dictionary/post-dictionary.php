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

function post_dictionary_install_table(){
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();
    $table_name      = $wpdb->prefix . 'postdictionary_data';

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
             id int(11) NOT NULL AUTO_INCREMENT,
             post_id int(11) NOT NULL,
             entry text NOT NULL,
             information text,
             definition text,
             PRIMARY KEY (id)
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

add_action( 'admin_menu', 'post_dictionary_setup_menu' );

function post_dictionary_setup_menu(){
    add_menu_page( 'Gestion des dictionnaires', 'Dictionnaires', 'manage_options', 'post-dictionaries', 'post_dictionary_home_page', 'dashicons-text', 20 );
    add_submenu_page( 'post-dictionaries', 'Création d\'un nouveau dictionnaire', 'Créer un dictionnaire', 'administrator', 'post-dictionaries-create', 'post_dictionary_create_page');
}

function post_dictionary_home_page(){
    global $wpdb;

    $post_id  = ( isset( $_GET['post_id']  )) ? $_GET['post_id']  : '';
    $entry_id = ( isset( $_GET['entry_id'] )) ? $_GET['entry_id'] : '';
    $action   = ( isset( $_GET['action']   )) ? $_GET['action']   : '';

    # Manipulate entry for dictionary of $post_id

    if( $action ){
	if( $entry_id ){
	    if( $action == 'edit' ){
                post_dictionary_edit_entry( $entry_id );
	    }

	    if( $action == 'delete' ){
                post_dictionary_delete_entry( $entry_id );
	    }
	}

	if( $action == 'add' ){
            post_dictionary_add_entry();
	}
    }

    # Display dictionary for post_id

    elseif( $post_id ){
        post_dictionary_list_page( $post_id );
    }

    # If no dictionary has to be displayed

    else {
	$posts_table  = $wpdb->prefix . 'posts';
	$plugin_table = $wpdb->prefix . 'postdictionary_data';

        # Retrieve all dictionaries

        $sql = "SELECT DISTINCT post_id, post_title, COUNT(d.id) as count
                FROM $plugin_table d, $posts_table p
                WHERE d.post_id = p.ID
                GROUP BY (post_id);";

        $dictionaries = $wpdb->get_results( $sql );

        ?>
        <div class="wrap wrap-post-dictionary">
            <h1>Gestion des dictionnaires</h1>
            <p>Ci-dessous, vous trouverez la liste de tous les dictionnaires existants sur votre blog.</p>

            <h2>Liste des dictionnaires existants</h2>
            <?php

            if( $dictionaries ){
                ?>
                <table class="wp-list-table widefat fixed striped posts">
                    <thead>
                        <tr>
                            <th>Articles</th>
                            <th>Entrées dans le dictionnaire</th>
                        </tr>
                    </thead>
                    <?php

                    foreach( $dictionaries as $result ){
                        $path = 'admin.php?page=post-dictionaries&post_id=' . $result->post_id;

                        ?>
                        <tr>
                            <td><?php echo $result->post_title; ?>
                                <div class="row-actions visible">
                                    <span class="activate">
                                        <a href="<?php echo admin_url( $path ); ?>" aria-label="Voir <?php echo $result->post_title; ?>">
                                            Voir dictionnaire
                                        </a>
                                    </span>
                                    <span class="activate">
                                    <a href="<?php echo admin_url( $path . '&action=add' ); ?>" aria-label="Ajouter élément <?php echo $result->post_title; ?>">
                                        Ajouter un élément
                                    </a>
                                    </span>
                                </div>
                            </td>
                            <td><?php echo $result->count; ?></td>
                        </tr>
                        <?php
                    }
                ?>
                </table>
                <?php
            }

	    else {
	        ?>
                <p>Vous n'avez encore aucun dictionnaire sur votre blog.</p>
                <?php
	    }
            ?>
        </div>
        <?php
    }
}

function post_dictionary_create_page(){
    global $wpdb;

    $posts_table  = $wpdb->prefix . 'posts';
    $plugin_table = $wpdb->prefix . 'postdictionary_data';

    if( isset( $_POST['submit_form'] ) ){
        $post_id = $_POST['post_id'];
        $return  = 'admin.php?page=post-dictionaries&post_id=' . $post_id . '&action=add&from=new';

        # Redirect to dictionary page
        wp_redirect( admin_url( $return ) );
        exit;
    }

    else {
        #TODO : Add other types of posts

        $sql = "SELECT ID, post_title
                FROM $posts_table
                WHERE post_status = 'publish'
                AND post_type = 'post'
                AND ID NOT IN(
                    SELECT post_id
                    FROM $plugin_table
                )
                ORDER BY post_title";

        $posts = $wpdb->get_results( $sql );
        ?>
        <div class="wrap wrap-post-dictionary">
            <h1>Création d'un nouveau dictionnaire</h1>
            <p>Choississez l'article pour lequel vous souhaitez créer un dictionnaire.</p>

            <form id="dictionaryform" action="#dictionaryform" name="create_dictionary" method="post">
                <select name="post_id" id="post_id">';
                    <?php
                    if( $posts ){
                        foreach( $posts as $element ){
	                    echo '<option value="' . $element->ID . '">' . $element->post_title . '</option>';
                        }
                    }
                    ?>
                    <p class="submit"><input type="submit" name="submit_form" value="Ajouter une entrée" class="button button-primary" /></p>
                </select>
            </form>
        </div>
        <?php
    }
}

function post_dictionary_list_page( $post_id ){
    global $wpdb;

    ?>
    <div class="wrap wrap-post-dictionary">
        <?php
        if( $post_id ){

            # Display post title
            $posts_table = $wpdb->prefix . 'posts';

            $sql = "SELECT post_title
                    FROM $posts_table
                    WHERE ID = $post_id;";

            $post       = $wpdb->get_results( $sql );
            $post_title = $post[0]->post_title;
            $add_path   = 'admin.php?page=post-dictionaries&post_id=' . $post_id . '&action=add';

            ?>
            <h1>Dictionnaire de l'article «&nbsp;<?php echo $post_title; ?>&nbsp;»</h1>
            <a href="<?php echo admin_url( $add_path ); ?>">Ajouter une nouvelle entrée</a>
            <?php

            # Display dictionary

            $table_name = $wpdb->prefix . 'postdictionary_data';

            $sql = "SELECT id, entry, information, definition
                    FROM $table_name
                    WHERE post_id = $post_id
                    ORDER BY entry ASC";

            $entries = $wpdb->get_results( $sql );

            if( $entries ){
                ?>
                <p>Voici la liste ordonnée des éléments présents dans le dictionnaire de cet article.</p>

                <table class="wp-list-table wp-list-post-dictionary widefat fixed striped posts">
                    <colgroup>
                       <col span="1" style="width: 26.67%;">
                       <col span="1" style="width: 26.67%;">
                       <col span="1" style="width: 26.67%;">
                       <col span="1" style="width: 20%;">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>Entrée</th>
                            <th>Information</th>
                            <th>Définition</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php

                    foreach( $entries as $element ){
                        $path = 'admin.php?page=post-dictionaries&post_id=' . $post_id . '&entry_id=' . $element->id;

                        ?>
                        <tr>
                            <td class="entry"><?php echo $element->entry; ?></td>
                            <td class="infos"><?php echo $element->information; ?></td>
                            <td class="definition"><?php echo $element->definition; ?></td>
                            <td class="actions">
                                <a href="<?php echo admin_url( $path . '&action=edit' ); ?>"><span class="dashicons dashicons-edit"></span><span class="action">Éditer</span></a>
                                |
                                <a href="<?php echo  admin_url( $path . '&action=delete' ); ?>"><span class="dashicons dashicons-trash"></span><span class="action">Supprimer</span></a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
                <?php
            } else {
                ?>
                <p>Cet article ne possède pas de dictionnaire.</p>
                <?php
            }
        } else {
            ?>
            <p>Veuiller sélectionner un dictionnaire existant.</p>
            <?php
        }
        ?>
        <p class="submit"><a href="<?php echo admin_url( 'admin.php?page=post-dictionaries' ); ?>">Retour à la liste des dictionnaires</a></p>
    </div>
    <?php
}

function post_dictionary_add_entry(){
    global $wpdb;

    $post_id      = $_GET['post_id'];
    $plugin_table = $wpdb->prefix . 'postdictionary_data';
    $return       = 'admin.php?page=post-dictionaries&post_id=' . $post_id;

    if( !isset( $action ) ){
        $action = $_GET['action'];
    }

    if( isset( $_POST['submit_form'] ) ){
        $form_entry       = stripslashes_deep( $_POST['dictionary']['entry']       );
        $form_information = stripslashes_deep( $_POST['dictionary']['information'] );
        $form_definition  = stripslashes_deep( $_POST['dictionary']['definition']  );

        $wpdb->insert(
            $plugin_table,
            array(
	        'post_id'     => $post_id,
                'entry'       => $form_entry,
                'information' => $form_information,
                'definition'  => $form_definition
            )
        );

        # Redirect to dictionary page
        wp_redirect( admin_url( $return ) );
        exit;
    }

    else {
	$posts_table = $wpdb->prefix . 'posts';

	$sql = "SELECT post_title
	        FROM $posts_table
		WHERE ID = $post_id;";

	$post       = $wpdb->get_results( $sql );
	$post_title = $post[0]->post_title;

        ?>
        <div class="wrap wrap-post-dictionary">
            <h1>Ajouter une entrée dans le dictionnaire de l'article «&nbsp;<?php echo $post_title; ?>&nbsp;»</h1>

            <form id="addform" action="#addform" name="edit_dictionary_entry" method="post">
                <table class="form-table">
                    <tr class="form-field">
                        <th>
                            <label for="dictionary[entry]"><?php _e( 'Entrée', 'post_dictionary' ); ?></label>
                        </th>

                        <td>
                            <input type="text" name="dictionary[entry]" id="dictionary[entry]" />
                        </td>
                    </tr>

                    <tr class="form-field">
                        <th>
                            <label for="dictionary[information]"><?php _e( 'Information', 'post_dictionary' ); ?></label>
                        </th>

                        <td>
                            <input type="text" name="dictionary[information]" id="dictionary[information]" />
                        </td>
                    </tr>

                    <tr class="form-field">
                        <th>
                            <label for="dictionary[definition]"><?php _e( 'Définition', 'post_dictionary' ); ?></label>
                        </th>

                        <td>
                            <input type="text" name="dictionary[definition]" id="dictionary[definition]" />
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <input type="submit" name="submit_form" value="Ajouter une entrée" class="button button-primary" />
                </p>
            </form>
            <?php

            $from = ( isset( $_GET['from'] ) ) ? $_GET['from'] : '';

	    if( $from != 'new' ){
                echo '<a href="' . $return . '">Retour au dictionnaire</a>';
	    }
            ?>
	</div>
        <?php
    }
}

function post_dictionary_edit_entry( $entry_id ){
    global $wpdb;

    ?>
    <div class="wrap wrap-post-dictionary">
        <?php

        if( !isset( $entry_id ) ){
            $entry_id = $_GET['entry_id'];
        }

        if( $entry_id ){
            $post_id = $_GET['post_id'];
            $return  = 'admin.php?page=post-dictionaries&post_id=' . $post_id;

            $plugin_table = $wpdb->prefix . 'postdictionary_data';

            ?>
            <h1>Éditer entrée du dictionnaire</h1>
            <?php

            if ( isset( $_POST['submit_form'] ) ){
                $form_entry       = stripslashes_deep( $_POST['dictionary']['entry']      );
                $form_information = stripslashes_deep( $_POST['dictionary']['information']);
                $form_definition  = stripslashes_deep( $_POST['dictionary']['definition'] );

                $wpdb->update(
                    $plugin_table,
                    array(
                        'entry' => $form_entry,
                        'information' => $form_information,
                        'definition' => $form_definition
                    ),
                    array( 'id' => $entry_id )
                );
            }

            $sql = "SELECT entry, information, definition
                    FROM $plugin_table
                    WHERE id = $entry_id";

            $entry = $wpdb->get_results( $sql )[0];

            ?>
            <form id="editform" action="#editform" name="edit_dictionary_entry" method="post">
                <table class="form-table">
                    <tr class="form-field">
                        <th>
                            <label for="dictionary[entry]"><?php _e( 'Entrée', 'post_dictionary' ); ?></label>
                        </th>

                        <td>
                            <input type="text" name="dictionary[entry]" id="dictionary[entry]" value="<?php echo $entry->entry; ?>">
                        </td>
                    </tr>

                    <tr class="form-field">
                        <th>
                            <label for="dictionary[information]"><?php _e( 'Information', 'post_dictionary' ); ?></label>
                        </th>

                        <td>
                            <input type="text" name="dictionary[information]" id="dictionary[information]" value="<?php echo $entry->information; ?>">
                        </td>
                    </tr>

                    <tr class="form-field">
                        <th>
                            <label for="dictionary[definition]"><?php _e( 'Définition', 'post_dictionary' ); ?></label>
                        </th>

                        <td>
                            <input type="text" name="dictionary[definition]" id="dictionary[definition]" value="<?php echo $entry->definition; ?>">
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <input type="submit" name="submit_form" value="Mettre à jour" class="button button-primary" />
                </p>
            </form>
            <?php

            if ( isset( $_POST['submit_form'] )){
                ?>
                <p>Cette entrée du dictionnaire a bien été modifiée&#8239;!</p>
                <?php
            }

            ?>
            <a href="<?php echo $return; ?>">Retour au dictionnaire</a>
            <?php
        }
        ?>
    </div>
    <?php
}

function post_dictionary_delete_entry( $entry_id ){
    global $wpdb;

    if( !isset( $entry_id ) ){
        $entry_id = $_GET['entry_id'];
    }

    if( $entry_id ){
	$post_id      = $_GET['post_id'];
	$plugin_table = $wpdb->prefix . 'postdictionary_data';
	$return       = 'admin.php?page=post-dictionaries&post_id=' . $post_id;

        if( isset( $_POST['submit_form'] ) ){
            $wpdb->delete(
                $plugin_table,
                array( 'id' => $entry_id )
            );

	    # Redirect to dictionary page
	    wp_redirect( admin_url( $return ) );
	    exit;
        }

	else {
            ?>
            <div class="wrap wrap-post-dictionary">
                <h1>Supprimer entrée du dictionnaire</h1>

                <form id="deleteform" action="#deleteform" name="delete_dictionary_entry" method="post">
	            <p>Êtes-vous sûr de vouloir supprimer cette entrée ?</p>

                    <p class="submit">
                        <input type="submit" name="submit_form" value="Supprimer l'entrée" class="button button-primary" />
                    </p>
	        </form>

                <a href="<?php echo $return; ?>">Retour au dictionnaire</a>
            </div>
            <?php
	}
    }
}

/**
 * Buffer output using ob_start to fix redirect
 *
 */

function app_output_buffer(){
    ob_start();
}

add_action('init', 'app_output_buffer');


/**
 * Add dictionary content at the end of posts
 *
 */

function post_dictionary_add_content( $content ){
    if( is_single() ){
        global $wpdb;

        $ID   = get_the_ID();
	$last = '';

        $table_name = $wpdb->prefix . 'postdictionary_data';

	$sql = "SELECT entry, information, definition
	        FROM $table_name
		WHERE post_id = $ID
		ORDER BY entry ASC";

	$entries = $wpdb->get_results( $sql );

	if( $entries ){
	    # Display all letters with anchor

	    $sql = "SELECT DISTINCT LEFT(entry, 1) capitale
	            FROM $table_name
		    WHERE post_id = $ID
		    ORDER BY entry ASC";

            $letters = $wpdb->get_results( $sql );

            $translit = array('Á'=>'A','À'=>'A','Â'=>'A','Ä'=>'A','Ã'=>'A','Å'=>'A','Ç'=>'C','É'=>'E','È'=>'E','Ê'=>'E','Ë'=>'E','Í'=>'I','Ï'=>'I','Î'=>'I','Ì'=>'I','Ñ'=>'N','Ó'=>'O','Ò'=>'O','Ô'=>'O','Ö'=>'O','Õ'=>'O','Ú'=>'U','Ù'=>'U','Û'=>'U','Ü'=>'U','Ý'=>'Y','á'=>'a','à'=>'a','â'=>'a','ä'=>'a','ã'=>'a','å'=>'a','ç'=>'c','é'=>'e','è'=>'e','ê'=>'e','ë'=>'e','í'=>'i','ì'=>'i','î'=>'i','ï'=>'i','ñ'=>'n','ó'=>'o','ò'=>'o','ô'=>'o','ö'=>'o','õ'=>'o','ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u','ý'=>'y','ÿ'=>'y');

	    $content .= '<div class="post_dictionary_letters">';
	    $content .= '<h3>' . __('Par ordre alphabétique', 'post_dictionary') . '</h3>';

	    $content .= '<ul>';
            $last_letter = '';

	    foreach( $letters as $letter ){
                $letter->capitale = strtr( $letter->capitale, $translit );

                if( $letter != $last_letter ){
	            $content .= '<li><a href="#letter_' . strtolower( $letter->capitale ) . '" class="post_dictionary_capitale">' . $letter->capitale . '</a></li>';
                    $last_letter = $letter;
	        }
	    }

	    $content .= '</ul>';

	    $content .= '</div>';

	    # Display all terms in dictionary

	    foreach( $entries as $element ){
                $entry = strtr( $element->entry, $translit );
                $real_ = $element->entry;
                $info  = $element->information;
                $def   = $element->definition;

	        $current = strtolower( $entry[0] );

	        # Display letter

		if( $last != $current ){
	            if( $last != '' ) $content .= '</div>';
	            $content .= '<div class="post_dictionary_data">';
		    $content .= '<div id="letter_' . $current . '" class="post_dictionary_letter">' . $current . '</div>';
		    $last = $current;
		}

		# Display term

                $content .= '<dl>';
                $content .= '<dt class="post_dictionary_term">' . $real_ . '</dt>';

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


# Add new styles

function post_dictionary_custom_style(){
    wp_enqueue_style( 'post-dictionary-styles', plugins_url( 'css/post-dictionary-styles.css', __FILE__ ) );
}

add_action( 'admin_enqueue_scripts', 'post_dictionary_custom_style' );
add_action( 'login_enqueue_scripts', 'post_dictionary_custom_style' );

?>
