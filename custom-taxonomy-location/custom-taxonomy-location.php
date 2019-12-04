<?php
/**
 *
 * Plugin Name: Custom Taxonomy Location
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin allows you to describe your posts with locations.
 * Version: 0.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */

global $LOCATION_TEXTDOMAIN;

$LOCATION_TEXTDOMAIN = 'location-taxonomy';


/*
 * Make plugin available for translation.
 * Translations can be filed in the /languages directory.
 *
 */

function location_taxonomy_load_textdomain() {
  global $LOCATION_TEXTDOMAIN;
  $locale = apply_filters( 'plugin_locale', get_locale(), $LOCATION_TEXTDOMAIN );

  # Load i18n
  $path = basename( dirname( __FILE__ ) ) . '/languages/';
  $loaded = load_plugin_textdomain( $LOCATION_TEXTDOMAIN, false, $path );
}

add_action( 'init', 'location_taxonomy_load_textdomain', 0 );


/**
 * Adding location taxonomy
 *
 */

function add_location_taxonomy() {
  global $LOCATION_TEXTDOMAIN;

  $labels = array (
    'name'                       => _x( 'Locations', 'taxonomy general name', $LOCATION_TEXTDOMAIN ),
    'singular_name'              => _x( 'Location', 'taxonomy singular name', $LOCATION_TEXTDOMAIN ),
    'search_items'               => __( 'Search Locations', $LOCATION_TEXTDOMAIN ),
    'popular_items'              => __( 'Popular Locations', $LOCATION_TEXTDOMAIN ),
    'all_items'                  => __( 'All Locations', $LOCATION_TEXTDOMAIN ),
    'parent_item'                => null,
    'parent_item_colon'          => null,
    'edit_item'                  => __( 'Edit Location', $LOCATION_TEXTDOMAIN ),
    'update_item'                => __( 'Update Location', $LOCATION_TEXTDOMAIN ),
    'add_new_item'               => __( 'Add New Location', $LOCATION_TEXTDOMAIN ),
    'new_item_name'              => __( 'New Location Name', $LOCATION_TEXTDOMAIN ),
    'separate_items_with_commas' => __( 'Separate locations with commas', $LOCATION_TEXTDOMAIN ),
    'add_or_remove_items'        => __( 'Add or remove locations', $LOCATION_TEXTDOMAIN ),
    'choose_from_most_used'      => __( 'Choose from the most used locations', $LOCATION_TEXTDOMAIN ),
    'not_found'                  => __( 'No locations found.', $LOCATION_TEXTDOMAIN ),
    'back_to_items'              => __( '← Back to locations', $LOCATION_TEXTDOMAIN ),
    'menu_name'                  => __( 'Locations', $LOCATION_TEXTDOMAIN ),
  );

  $args = array (
    'hierarchical'          => true,
    'labels'                => $labels,
    'show_ui'               => true,
    'show_admin_column'     => true,
    'update_count_callback' => '_update_post_term_count',
    'query_var'             => true,
    'rewrite'               => array( 'slug' => 'location', 'with_front' => 'true', 'hierarchical' => true ),
    'show_tagcloud'         => true,
  );

  register_taxonomy ('location', array('post', 'attachment'), $args);
}

add_action ('init', 'add_location_taxonomy', 0);


/**
 * Adding custom fields in location taxonomy
 *
 */

function add_new_location_field() {
  global $LOCATION_TEXTDOMAIN;

  # This will add the custom meta fields to the 'Add new term' page
  ?>
  <div class="form-field">
    <label for="term_meta[iso]"><?php _e( 'Code ISO', $LOCATION_TEXTDOMAIN ); ?></label>
    <input type="text" name="term_meta[iso]" id="term_meta[iso]" value="">
    <p class="description"><?php _e( 'Enter the code ISO of the location.', $LOCATION_TEXTDOMAIN ); ?></p>
  </div>
<?php
}

add_action( 'location_add_form_fields', 'add_new_location_field', 10, 2 );


/**
 * Editing custom fields in location taxonomy
 *
 * @param object $term
 *
 */

function edit_location_field ($term) {
  global $LOCATION_TEXTDOMAIN;

  # Put the term ID into a variable
  $t_id = $term->term_id;

  # Retrieve the existing values for this meta field
  # This will return an array
  $term_meta = get_option( "taxonomy_$t_id" );

  ?>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[iso]"><?php _e( 'Code ISO', $LOCATION_TEXTDOMAIN ); ?></label></th>
    <td>
        <input type="text" name="term_meta[iso]" id="term_meta[iso]" value="<?php echo esc_attr( $term_meta['iso'] ) ? esc_attr( $term_meta['iso'] ) : ''; ?>">
        <p class="description"><?php _e( 'Enter the code ISO of the location.', $LOCATION_TEXTDOMAIN); ?></p>
    </td>
  </tr>
  <?php
}

add_action( 'location_edit_form_fields', 'edit_location_field', 10, 2 );


/**
 * Saving custom fields in location taxonomy
 *
 * @param int $term_id
 *
 */

function save_location_custom_meta ($term_id) {
  if (isset($_POST['term_meta'])) {
    $t_id = $term_id;
    $term_meta = get_option("taxonomy_$t_id");
    $cat_keys = array_keys($_POST['term_meta']);

    foreach ($cat_keys as $key) {
      if (isset($_POST['term_meta'][$key])) {
        $term_meta[$key] = $_POST['term_meta'][$key];
      }
    }

    # Save the option array
    update_option( "taxonomy_$t_id", $term_meta );
  }
}

add_action( 'edited_location', 'save_location_custom_meta', 10, 2 );
add_action( 'create_location', 'save_location_custom_meta', 10, 2 );


/**/

function location_add_field_columns( $columns ) {
  global $LOCATION_TEXTDOMAIN;
  $columns['iso'] = __( 'Code ISO', $LOCATION_TEXTDOMAIN );

  return $columns;
}

add_filter( 'manage_edit-location_columns', 'location_add_field_columns' );


/**/

function location_add_field_column_contents( $content, $column_name, $term_id ) {
    switch( $column_name ) {
        case 'iso' :
            $content = get_term_meta( $term_id, 'iso', true );
            break;
    }

    return $content;
}

add_filter( 'manage_location_custom_column', 'location_add_field_column_contents', 10, 3 );


/**
 * Add automatically continents and countries
 *
 */

function add_locations() {
  global $LOCATION_TEXTDOMAIN;

  $continents = array(
    array('iso' => 'AF', 'name' => 'Africa'),
    array('iso' => 'AS', 'name' => 'Asia'),
    array('iso' => 'EU', 'name' => 'Europe'),
    array('iso' => 'NA', 'name' => 'North America'),
    array('iso' => 'OC', 'name' => 'Oceania'),
    array('iso' => 'SA', 'name' => 'South America'),
    array('iso' => 'AN', 'name' => 'Antarctica')
  );

  $countries = array(
    array('iso' => 'AF', 'name' => 'Afghanistan', 'continent' => 'AS'),
    array('iso' => 'AL', 'name' => 'Albania', 'continent' => 'EU'),
    array('iso' => 'DZ', 'name' => 'Algeria', 'continent' => 'AF'),
    array('iso' => 'AS', 'name' => 'American Samoa', 'continent' => 'OC'),
    array('iso' => 'AD', 'name' => 'Andorra', 'continent' => 'EU'),
    array('iso' => 'AO', 'name' => 'Angola', 'continent' => 'AF'),
    array('iso' => 'AI', 'name' => 'Anguilla', 'continent' => 'NA'),
    array('iso' => 'AQ', 'name' => 'Antarctique', 'continent' => 'AN'),
    array('iso' => 'AG', 'name' => 'Antigua and Barbuda', 'continent' => 'NA'),
    array('iso' => 'AR', 'name' => 'Argentina', 'continent' => 'SA'),
    array('iso' => 'AM', 'name' => 'Armenia', 'continent' => 'AS'),
    array('iso' => 'AW', 'name' => 'Aruba', 'continent' => 'NA'),
    array('iso' => 'AU', 'name' => 'Australia', 'continent' => 'OC'),
    array('iso' => 'AT', 'name' => 'Austria', 'continent' => 'EU'),
    array('iso' => 'AZ', 'name' => 'Azerbaijan', 'continent' => 'AS'),
    array('iso' => 'BS', 'name' => 'Bahamas', 'continent' => 'NA'),
    array('iso' => 'BH', 'name' => 'Bahrain', 'continent' => 'AS'),
    array('iso' => 'BD', 'name' => 'Bangladesh', 'continent' => 'AS'),
    array('iso' => 'BB', 'name' => 'Barbados', 'continent' => 'NA'),
    array('iso' => 'BY', 'name' => 'Belarus', 'continent' => 'EU'),
    array('iso' => 'BE', 'name' => 'Belgium', 'continent' => 'EU'),
    array('iso' => 'BZ', 'name' => 'Belize', 'continent' => 'NA'),
    array('iso' => 'BJ', 'name' => 'Benin', 'continent' => 'AF'),
    array('iso' => 'BM', 'name' => 'Bermuda', 'continent' => 'NA'),
    array('iso' => 'BT', 'name' => 'Bhutan', 'continent' => 'AS'),
    array('iso' => 'BO', 'name' => 'Bolivia', 'continent' => 'SA'),
    array('iso' => 'BQ', 'name' => 'Bonaire', 'continent' => 'NA'),
    array('iso' => 'BA', 'name' => 'Bosnia and Herzegovina', 'continent' => 'EU'),
    array('iso' => 'BW', 'name' => 'Botswana', 'continent' => 'AF'),
    array('iso' => 'BV', 'name' => 'Bouvet Island', 'continent' => 'AN'),
    array('iso' => 'BR', 'name' => 'Brazil', 'continent' => 'SA'),
    array('iso' => 'IO', 'name' => 'British Indian Ocean Territory', 'continent' => 'AS'),
    array('iso' => 'VG', 'name' => 'British Virgin Islands', 'continent' => 'NA'),
    array('iso' => 'BN', 'name' => 'Brunei', 'continent' => 'AS'),
    array('iso' => 'BG', 'name' => 'Bulgaria', 'continent' => 'EU'),
    array('iso' => 'BF', 'name' => 'Burkina Faso', 'continent' => 'AF'),
    array('iso' => 'BI', 'name' => 'Burundi', 'continent' => 'AF'),
    array('iso' => 'KH', 'name' => 'Cambodia', 'continent' => 'AS'),
    array('iso' => 'CM', 'name' => 'Cameroon', 'continent' => 'AF'),
    array('iso' => 'CA', 'name' => 'Canada', 'continent' => 'NA'),
    array('iso' => 'CV', 'name' => 'Cape Verde', 'continent' => 'AF'),
    array('iso' => 'KY', 'name' => 'Cayman Islands', 'continent' => 'NA'),
    array('iso' => 'CF', 'name' => 'Central African Republic', 'continent' => 'AF'),
    array('iso' => 'TD', 'name' => 'Chad', 'continent' => 'AF'),
    array('iso' => 'CL', 'name' => 'Chile', 'continent' => 'SA'),
    array('iso' => 'CN', 'name' => 'China', 'continent' => 'AS'),
    array('iso' => 'CX', 'name' => 'Christmas Island', 'continent' => 'AS'),
    array('iso' => 'CC', 'name' => 'Cocos (Keeling) Islands', 'continent' => 'AS'),
    array('iso' => 'CO', 'name' => 'Colombia', 'continent' => 'SA'),
    array('iso' => 'KM', 'name' => 'Comoros', 'continent' => 'AF'),
    array('iso' => 'CK', 'name' => 'Cook Islands', 'continent' => 'OC'),
    array('iso' => 'CR', 'name' => 'Costa Rica', 'continent' => 'NA'),
    array('iso' => 'HR', 'name' => 'Croatia', 'continent' => 'EU'),
    array('iso' => 'CU', 'name' => 'Cuba', 'continent' => 'NA'),
    array('iso' => 'CY', 'name' => 'Curaçao', 'continent' => 'NA'),
    array('iso' => 'CY', 'name' => 'Cyprus', 'continent' => 'EU'),
    array('iso' => 'CZ', 'name' => 'Czechia', 'continent' => 'EU'),
    array('iso' => 'CD', 'name' => 'Democratic Republic of the Congo', 'continent' => 'AF'),
    array('iso' => 'DK', 'name' => 'Denmark', 'continent' => 'EU'),
    array('iso' => 'DJ', 'name' => 'Djibouti', 'continent' => 'AF'),
    array('iso' => 'DM', 'name' => 'Dominica', 'continent' => 'NA'),
    array('iso' => 'DO', 'name' => 'Dominican Republic', 'continent' => 'NA'),
    array('iso' => 'TP', 'name' => 'East Timor', 'continent' => 'OC'),
    array('iso' => 'EC', 'name' => 'Ecuador', 'continent' => 'SA'),
    array('iso' => 'EG', 'name' => 'Egypt', 'continent' => 'AF'),
    array('iso' => 'SV', 'name' => 'El Salvador', 'continent' => 'NA'),
    array('iso' => 'GQ', 'name' => 'Equatorial Guinea', 'continent' => 'AF'),
    array('iso' => 'ER', 'name' => 'Eritrea', 'continent' => 'AF'),
    array('iso' => 'EE', 'name' => 'Estonia', 'continent' => 'EU'),
    array('iso' => 'ET', 'name' => 'Ethiopia', 'continent' => 'AF'),
    array('iso' => 'FK', 'name' => 'Falkland Islands', 'continent' => 'SA'),
    array('iso' => 'FO', 'name' => 'Faroe Islands', 'continent' => 'EU'),
    array('iso' => 'FJ', 'name' => 'Fiji', 'continent' => 'OC'),
    array('iso' => 'FI', 'name' => 'Finland', 'continent' => 'EU'),
    array('iso' => 'FR', 'name' => 'France', 'continent' => 'EU'),
    array('iso' => 'GF', 'name' => 'French Guiana', 'continent' => 'SA'),
    array('iso' => 'PF', 'name' => 'French Polynesia', 'continent' => 'OC'),
    array('iso' => 'TF', 'name' => 'French Southern Territories', 'continent' => 'AN'),
    array('iso' => 'GA', 'name' => 'Gabon', 'continent' => 'AF'),
    array('iso' => 'GM', 'name' => 'Gambia', 'continent' => 'AF'),
    array('iso' => 'GE', 'name' => 'Georgia', 'continent' => 'AS'),
    array('iso' => 'DE', 'name' => 'Germany', 'continent' => 'EU'),
    array('iso' => 'GH', 'name' => 'Ghana', 'continent' => 'AF'),
    array('iso' => 'GI', 'name' => 'Gibraltar', 'continent' => 'EU'),
    array('iso' => 'GR', 'name' => 'Greece', 'continent' => 'EU'),
    array('iso' => 'GL', 'name' => 'Greenland', 'continent' => 'AN'),
    array('iso' => 'GD', 'name' => 'Grenada', 'continent' => 'NA'),
    array('iso' => 'GP', 'name' => 'Guadeloupe', 'continent' => 'NA'),
    array('iso' => 'GU', 'name' => 'Guam', 'continent' => 'OC'),
    array('iso' => 'GT', 'name' => 'Guatemala', 'continent' => 'NA'),
    array('iso' => 'GN', 'name' => 'Guernsey', 'continent' => 'EU'),
    array('iso' => 'GN', 'name' => 'Guinea', 'continent' => 'AF'),
    array('iso' => 'GW', 'name' => 'Guinea-Bissau', 'continent' => 'AF'),
    array('iso' => 'GY', 'name' => 'Guyana', 'continent' => 'SA'),
    array('iso' => 'HT', 'name' => 'Haiti', 'continent' => 'NA'),
    array('iso' => 'HM', 'name' => 'Heard Island and McDonald Islands', 'continent' => 'AN'),
    array('iso' => 'HN', 'name' => 'Honduras', 'continent' => 'NA'),
    array('iso' => 'HK', 'name' => 'Hong Kong', 'continent' => 'AS'),
    array('iso' => 'HU', 'name' => 'Hungary', 'continent' => 'EU'),
    array('iso' => 'IS', 'name' => 'Iceland', 'continent' => 'EU'),
    array('iso' => 'IN', 'name' => 'India', 'continent' => 'AS'),
    array('iso' => 'ID', 'name' => 'Indonesia', 'continent' => 'AS'),
    array('iso' => 'IR', 'name' => 'Iran', 'continent' => 'AS'),
    array('iso' => 'IQ', 'name' => 'Iraq', 'continent' => 'AS'),
    array('iso' => 'IE', 'name' => 'Ireland', 'continent' => 'EU'),
    array('iso' => 'IM', 'name' => 'Isle of Man', 'continent' => 'EU'),
    array('iso' => 'IL', 'name' => 'Israel', 'continent' => 'AS'),
    array('iso' => 'IT', 'name' => 'Italy', 'continent' => 'EU'),
    array('iso' => 'CI', 'name' => 'Ivory Coast', 'continent' => 'AF'),
    array('iso' => 'JM', 'name' => 'Jamaica', 'continent' => 'NA'),
    array('iso' => 'JP', 'name' => 'Japan', 'continent' => 'AS'),
    array('iso' => 'JE', 'name' => 'Jersey', 'continent' => 'EU'),
    array('iso' => 'JO', 'name' => 'Jordan', 'continent' => 'AS'),
    array('iso' => 'KZ', 'name' => 'Kazakhstan', 'continent' => 'AS'),
    array('iso' => 'KE', 'name' => 'Kenya', 'continent' => 'AF'),
    array('iso' => 'KI', 'name' => 'Kiribati', 'continent' => 'OC'),
    array('iso' => 'XK', 'name' => 'Kosovo', 'continent' => 'EU'),
    array('iso' => 'KW', 'name' => 'Kuwait', 'continent' => 'AS'),
    array('iso' => 'KG', 'name' => 'Kyrgyzstan', 'continent' => 'AS'),
    array('iso' => 'LA', 'name' => 'Laos', 'continent' => 'AS'),
    array('iso' => 'LV', 'name' => 'Latvia', 'continent' => 'EU'),
    array('iso' => 'LB', 'name' => 'Lebanon', 'continent' => 'AS'),
    array('iso' => 'LS', 'name' => 'Lesotho', 'continent' => 'AF'),
    array('iso' => 'LR', 'name' => 'Liberia', 'continent' => 'AF'),
    array('iso' => 'LY', 'name' => 'Libya', 'continent' => 'AF'),
    array('iso' => 'LI', 'name' => 'Liechtenstein', 'continent' => 'EU'),
    array('iso' => 'LT', 'name' => 'Lithuania', 'continent' => 'EU'),
    array('iso' => 'LU', 'name' => 'Luxembourg', 'continent' => 'EU'),
    array('iso' => 'MO', 'name' => 'Macao', 'continent' => 'AS'),
    array('iso' => 'MK', 'name' => 'Macedonia', 'continent' => 'EU'),
    array('iso' => 'MG', 'name' => 'Madagascar', 'continent' => 'AF'),
    array('iso' => 'MW', 'name' => 'Malawi', 'continent' => 'AF'),
    array('iso' => 'MY', 'name' => 'Malaysia', 'continent' => 'AS'),
    array('iso' => 'MV', 'name' => 'Maldives', 'continent' => 'AS'),
    array('iso' => 'ML', 'name' => 'Mali', 'continent' => 'AF'),
    array('iso' => 'MT', 'name' => 'Malta', 'continent' => 'EU'),
    array('iso' => 'MH', 'name' => 'Marshall Islands', 'continent' => 'OC'),
    array('iso' => 'MQ', 'name' => 'Martinique', 'continent' => 'NA'),
    array('iso' => 'MR', 'name' => 'Mauritania', 'continent' => 'AF'),
    array('iso' => 'MU', 'name' => 'Mauritius', 'continent' => 'AF'),
    array('iso' => 'YT', 'name' => 'Mayotte', 'continent' => 'AF'),
    array('iso' => 'MX', 'name' => 'Mexico', 'continent' => 'NA'),
    array('iso' => 'FM', 'name' => 'Micronesia', 'continent' => 'OC'),
    array('iso' => 'MD', 'name' => 'Moldova', 'continent' => 'EU'),
    array('iso' => 'MC', 'name' => 'Monaco', 'continent' => 'EU'),
    array('iso' => 'MN', 'name' => 'Mongolia', 'continent' => 'AS'),
    array('iso' => 'ME', 'name' => 'Montenegro', 'continent' => 'EU'),
    array('iso' => 'MS', 'name' => 'Montserrat', 'continent' => 'NA'),
    array('iso' => 'MA', 'name' => 'Morocco', 'continent' => 'AF'),
    array('iso' => 'MZ', 'name' => 'Mozambique', 'continent' => 'AF'),
    array('iso' => 'MM', 'name' => 'Myanmar', 'continent' => 'AS'),
    array('iso' => 'NA', 'name' => 'Namibia', 'continent' => 'AF'),
    array('iso' => 'NR', 'name' => 'Nauru', 'continent' => 'OC'),
    array('iso' => 'NP', 'name' => 'Nepal', 'continent' => 'AS'),
    array('iso' => 'NL', 'name' => 'Netherlands', 'continent' => 'EU'),
    array('iso' => 'NC', 'name' => 'New Caledonia', 'continent' => 'OC'),
    array('iso' => 'NZ', 'name' => 'New Zealand', 'continent' => 'OC'),
    array('iso' => 'NI', 'name' => 'Nicaragua', 'continent' => 'NA'),
    array('iso' => 'NE', 'name' => 'Niger', 'continent' => 'AF'),
    array('iso' => 'NG', 'name' => 'Nigeria', 'continent' => 'AF'),
    array('iso' => 'NU', 'name' => 'Niue', 'continent' => 'OC'),
    array('iso' => 'NF', 'name' => 'Norfolk Island', 'continent' => 'OC'),
    array('iso' => 'KP', 'name' => 'North Korea', 'continent' => 'AS'),
    array('iso' => 'MP', 'name' => 'Northern Mariana Islands', 'continent' => 'OC'),
    array('iso' => 'NO', 'name' => 'Norway', 'continent' => 'EU'),
    array('iso' => 'OM', 'name' => 'Oman', 'continent' => 'AS'),
    array('iso' => 'PK', 'name' => 'Pakistan', 'continent' => 'AS'),
    array('iso' => 'PW', 'name' => 'Palau', 'continent' => 'OC'),
    array('iso' => 'PS', 'name' => 'Palestine', 'continent' => 'AS'),
    array('iso' => 'PA', 'name' => 'Panama', 'continent' => 'NA'),
    array('iso' => 'PG', 'name' => 'Papua New Guinea', 'continent' => 'OC'),
    array('iso' => 'PY', 'name' => 'Paraguay', 'continent' => 'SA'),
    array('iso' => 'PE', 'name' => 'Peru', 'continent' => 'SA'),
    array('iso' => 'PH', 'name' => 'Philippines', 'continent' => 'AS'),
    array('iso' => 'PN', 'name' => 'Pitcairn Islands', 'continent' => 'OC'),
    array('iso' => 'PL', 'name' => 'Poland', 'continent' => 'EU'),
    array('iso' => 'PT', 'name' => 'Portugal', 'continent' => 'EU'),
    array('iso' => 'PR', 'name' => 'Puerto Rico', 'continent' => 'NA'),
    array('iso' => 'QA', 'name' => 'Qatar', 'continent' => 'AS'),
    array('iso' => 'CG', 'name' => 'Republic of the Congo', 'continent' => 'AF'),
    array('iso' => 'RE', 'name' => 'Réunion', 'continent' => 'AF'),
    array('iso' => 'RO', 'name' => 'Romania', 'continent' => 'EU'),
    array('iso' => 'RU', 'name' => 'Russia', 'continent' => 'EU'),
    array('iso' => 'RW', 'name' => 'Rwanda', 'continent' => 'AF'),
    array('iso' => 'BL', 'name' => 'Saint Barthélemy', 'continent' => 'NA'),
    array('iso' => 'SH', 'name' => 'Saint Helena', 'continent' => 'AF'),
    array('iso' => 'KN', 'name' => 'Saint Kitts and Nevis', 'continent' => 'NA'),
    array('iso' => 'LC', 'name' => 'Saint Lucia', 'continent' => 'NA'),
    array('iso' => 'MF', 'name' => 'Saint Martin', 'continent' => 'NA'),
    array('iso' => 'PM', 'name' => 'Saint Pierre and Miquelon', 'continent' => 'NA'),
    array('iso' => 'VC', 'name' => 'Saint Vincent and the Grenadines', 'continent' => 'NA'),
    array('iso' => 'WS', 'name' => 'Samoa', 'continent' => 'OC'),
    array('iso' => 'SM', 'name' => 'San Marino', 'continent' => 'EU'),
    array('iso' => 'ST', 'name' => 'São Tomé and Príncipe', 'continent' => 'AF'),
    array('iso' => 'SA', 'name' => 'Saudi Arabia', 'continent' => 'AS'),
    array('iso' => 'SN', 'name' => 'Senegal', 'continent' => 'AF'),
    array('iso' => 'RS', 'name' => 'Serbia', 'continent' => 'EU'),
    array('iso' => 'SC', 'name' => 'Seychelles', 'continent' => 'AF'),
    array('iso' => 'SL', 'name' => 'Sierra Leone', 'continent' => 'AF'),
    array('iso' => 'SG', 'name' => 'Singapore', 'continent' => 'AS'),
    array('iso' => 'SX', 'name' => 'Sint Marteen', 'continent' => 'NA'),
    array('iso' => 'SK', 'name' => 'Slovakia', 'continent' => 'EU'),
    array('iso' => 'SI', 'name' => 'Slovenia', 'continent' => 'EU'),
    array('iso' => 'SB', 'name' => 'Solomon Islands', 'continent' => 'OC'),
    array('iso' => 'SO', 'name' => 'Somalia', 'continent' => 'AF'),
    array('iso' => 'ZA', 'name' => 'South Africa', 'continent' => 'AF'),
    array('iso' => 'GS', 'name' => 'South Georgia and the South Sandwich Islands', 'continent' => 'AN'),
    array('iso' => 'KR', 'name' => 'South Korea', 'continent' => 'AS'),
    array('iso' => 'SS', 'name' => 'South Sudan', 'continent' => 'AF'),
    array('iso' => 'ES', 'name' => 'Spain', 'continent' => 'EU'),
    array('iso' => 'LK', 'name' => 'Sri Lanka', 'continent' => 'AS'),
    array('iso' => 'SD', 'name' => 'Sudan', 'continent' => 'AF'),
    array('iso' => 'SR', 'name' => 'Suriname', 'continent' => 'SA'),
    array('iso' => 'SJ', 'name' => 'Svalbard and Jan Mayen', 'continent' => 'EU'),
    array('iso' => 'SZ', 'name' => 'Swaziland', 'continent' => 'AF'),
    array('iso' => 'SE', 'name' => 'Sweden', 'continent' => 'EU'),
    array('iso' => 'CH', 'name' => 'Switzerland', 'continent' => 'EU'),
    array('iso' => 'SY', 'name' => 'Syria', 'continent' => 'AS'),
    array('iso' => 'TW', 'name' => 'Taiwan', 'continent' => 'AS'),
    array('iso' => 'TJ', 'name' => 'Tajikistan', 'continent' => 'AS'),
    array('iso' => 'TZ', 'name' => 'Tanzania', 'continent' => 'AF'),
    array('iso' => 'TH', 'name' => 'Thailand', 'continent' => 'AS'),
    array('iso' => 'TG', 'name' => 'Togo', 'continent' => 'AF'),
    array('iso' => 'TK', 'name' => 'Tokelau', 'continent' => 'OC'),
    array('iso' => 'TO', 'name' => 'Tonga', 'continent' => 'OC'),
    array('iso' => 'TT', 'name' => 'Trinidad and Tobago', 'continent' => 'NA'),
    array('iso' => 'TN', 'name' => 'Tunisia', 'continent' => 'AF'),
    array('iso' => 'TR', 'name' => 'Turkey', 'continent' => 'AS'),
    array('iso' => 'TM', 'name' => 'Turkmenistan', 'continent' => 'AS'),
    array('iso' => 'TC', 'name' => 'Turks and Caicos Islands', 'continent' => 'NA'),
    array('iso' => 'TV', 'name' => 'Tuvalu', 'continent' => 'OC'),
    array('iso' => 'UG', 'name' => 'Uganda', 'continent' => 'AF'),
    array('iso' => 'UA', 'name' => 'Ukraine', 'continent' => 'EU'),
    array('iso' => 'AE', 'name' => 'United Arab Emirates', 'continent' => 'AS'),
    array('iso' => 'GB', 'name' => 'United Kingdom', 'continent' => 'EU'),
    array('iso' => 'US', 'name' => 'United States', 'continent' => 'NA'),
    array('iso' => 'UM', 'name' => 'United States Minor Outlying Islands', 'continent' => 'OC'),
    array('iso' => 'VI', 'name' => 'United States Virgin Islands', 'continent' => 'NA'),
    array('iso' => 'UY', 'name' => 'Uruguay', 'continent' => 'SA'),
    array('iso' => 'UZ', 'name' => 'Uzbekistan', 'continent' => 'AS'),
    array('iso' => 'VU', 'name' => 'Vanuatu', 'continent' => 'OC'),
    array('iso' => 'VA', 'name' => 'Vatican City', 'continent' => 'EU'),
    array('iso' => 'VE', 'name' => 'Venezuela', 'continent' => 'SA'),
    array('iso' => 'VN', 'name' => 'Vietnam', 'continent' => 'AS'),
    array('iso' => 'WF', 'name' => 'Wallis and Futuna', 'continent' => 'OC'),
    array('iso' => 'EH', 'name' => 'Western Sahara', 'continent' => 'AF'),
    array('iso' => 'YE', 'name' => 'Yemen', 'continent' => 'AS'),
    array('iso' => 'ZM', 'name' => 'Zambia', 'continent' => 'AF'),
    array('iso' => 'ZW', 'name' => 'Zimbabwe', 'continent' => 'AF'),
  );

  foreach($continents as $continent) {
    $continent_name = $continent['name'];
    $continent_iso = $continent['iso'];

    if(!get_term_by('name', $continent_name, 'location')) {
      $continent_term = wp_insert_term($continent_name, 'location');
      $continent_id = $continent_term['term_id'];

      add_term_meta ($continent_id, 'iso', $continent_iso);
    }
  }

  foreach($countries as $country) {
    $country_name = $country['name'];
    $country_iso = $country['iso'];
    $country_continent_iso = $country['continent'];

    foreach ($continents as $continent) {
      if($continent['iso'] == $country_continent_iso) {
        $country_continent_name = $continent['name'];
        break;
      }
    }

    $continent_term = get_term_by('name', $country_continent_name, 'location');
    $continent_term_id = $continent_term->term_id;

    if(!get_term_by('name', $country_name, 'location')) {
      $country_term = wp_insert_term($country_name, 'location', array('parent' => $continent_term_id));
      #delete_option('location_children');

      $country_id = $country_term['term_id'];
      add_term_meta ($country_id, 'iso', $country_iso);
    }
  }
}


/**
 * Retrieving all children from current location
 *
 * @return array $terms
 *
 */

function get_location_children() {
  $id = get_queried_object_id();

  $terms = get_terms (
    array(
      'taxonomy' => 'location',
      'orderby' => 'name',
      'hide_empty' => false,
      'limit' => -1,
      'parent' => $id
    )
  );

  return $terms;
}


/**
 * Create widget to retrieve popular locations in specific category
 *
 */


class popular_locations_in_category_widget extends WP_Widget {
    function __construct() {
        parent::__construct(
            # Base ID of your widget
            'popular_locations_in_category_widget',

            # Widget name will appear in UI
            __('Popular Locations in Category Widget', 'location-taxonomy'),

            # Widget description
            array( 'description' => __( 'This widget will show all the locations in the specific category you choose', 'location-taxonomy' ), )
        );
    }

    # Creating widget front-end
    public function widget( $args, $instance ) {
        global $LOCATION_TEXTDOMAIN;
        $title = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
        $l_count = isset( $instance['l_count'] ) ? $instance['l_count'] : '';

        # Before and after widget arguments are defined by themes
        echo $args['before_widget'];

        if ( ! empty( $title ) )
            echo $args['before_title'] . $title . $args['after_title'];

        # This is where you run the code and display the output

        # Find the category where is displayed the widget
        $categories = get_the_category();

	$catID = null;
	if ( isset ( $categories[0] ) ) {
            $catID = $categories[0]->cat_ID;
	}

        $post_types = array( 'post' );

        if( post_type_exists( 'book' ) ){
            array_push( $post_types, 'book' );
        }

        if( post_type_exists( 'album' ) ){
            array_push( $post_types, 'album' );
        }

        if( post_type_exists( 'concert' ) ){
            array_push( $post_types, 'concert' );
        }

        if( post_type_exists( 'interview' ) ){
            array_push( $post_types, 'interview' );
        }

        if ( $catID ) {
            $posts_with_category = get_posts( array(
                         'category'       => $catID,
                         'post_type'      => $post_types,
                         'number_posts'   => -1,
                         'posts_per_page' => -1,
                     ));
        }
        else {
            $posts_with_category = get_posts( array(
                         'post_type'      => $post_types,
                         'number_posts'   => -1,
                         'posts_per_page' => -1,
                     ));
        }

        $array_of_terms_in_category = array();

        foreach( $posts_with_category as $post ) {
            $terms = wp_get_post_terms( $post->ID, 'location' );

            foreach( $terms as $value ){
                if( !in_array( $value, $array_of_terms_in_category, true ) ){
                    array_push( $array_of_terms_in_category, $value->term_id );
                }
            }
        }

        $tag_args = array(
                    'format'   => 'array',
                    'number'   => $l_count,
                    'taxonomy' => 'location',
                    'orderby'  => 'count',
                    'order'    => 'DESC',
                    'include'  => $array_of_terms_in_category,
                    'echo'     => false,
                );

        echo '<div class="tagcloud">';

        $locations_array = get_terms ( 'location', $tag_args );

        if( sizeof( $locations_array ) ){
            function widget_sort_location_by_translation( $a, $b ){
                $translit = array('Á'=>'A','À'=>'A','Â'=>'A','Ä'=>'A','Ã'=>'A','Å'=>'A','Ç'=>'C','É'=>'E','È'=>'E','Ê'=>'E','Ë'=>'E','Í'=>'I','Ï'=>'I','Î'=>'I','Ì'=>'I','Ñ'=>'N','Ó'=>'O','Ò'=>'O','Ô'=>'O','Ö'=>'O','Õ'=>'O','Ú'=>'U','Ù'=>'U','Û'=>'U','Ü'=>'U','Ý'=>'Y','á'=>'a','à'=>'a','â'=>'a','ä'=>'a','ã'=>'a','å'=>'a','ç'=>'c','é'=>'e','è'=>'e','ê'=>'e','ë'=>'e','í'=>'i','ì'=>'i','î'=>'i','ï'=>'i','ñ'=>'n','ó'=>'o','ò'=>'o','ô'=>'o','ö'=>'o','õ'=>'o','ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u','ý'=>'y','ÿ'=>'y');
                $at = strtolower( strtr( $a->translation, $translit ) );
                $bt = strtolower( strtr( $b->translation, $translit ) );

                return strcoll( $at, $bt );
            }

            foreach( $locations_array as $mylocation ) {
                $mylocation->translation = __( $mylocation->name, $LOCATION_TEXTDOMAIN );
            }

            usort( $locations_array, 'widget_sort_location_by_translation' );

            echo '<ul class="wp-tag-cloud">';

	    foreach ( $locations_array as $mylocation ) {
                echo '<li><a href="' . get_term_link( $mylocation->term_id ) . '" class="tag-cloud-link tag-link-' . $mylocation->term_id . '">';
                echo $mylocation->translation;
                echo '</a></li>';
	    }

            echo '</ul>';
	}

        echo '</div>';

        echo $args['after_widget'];
    }

    # Widget Backend
    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
            $l_count = isset( $instance['l_count'] ) ? esc_attr( $instance['l_count'] ) : '';
        } else {
            $title = __( 'Locations', 'location-taxonomy' );
            $l_count = 75;
        }

        # Widget admin form
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>

	<p>
	    <label for="<?php echo $this->get_field_id( 'l_count' ); ?>"><?php _e( 'Number of locations to show:', 'location-taxonomy' ); ?></label>
	    <input type="text" name="<?php echo $this->get_field_name( 'l_count' ); ?>" value="<?php echo esc_attr( $l_count ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'l_count' ); ?>" />
	</p>
        <?php
    }

    # Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = array();

        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['l_count'] = $new_instance['l_count'];

        return $instance;
    }
}

# Register and load the widget
function popular_location_wpb_load_widget() {
    register_widget( 'popular_locations_in_category_widget' );
}

add_action( 'widgets_init', 'popular_location_wpb_load_widget' );




/**
 * Flush rewrites when the plugin is activated
 *
 */

function location_flush_rewrites() {
  flush_rewrite_rules();

  add_action('init', 'add_locations', 100);
}

# Prevent 404 errors on locations' archive

register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
register_activation_hook( __FILE__, 'location_flush_rewrites' );

add_action( 'init', 'location_flush_rewrites' );

?>
