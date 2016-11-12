<?php
/**
 *
 * Plugin Name: Location Custom Taxonomy
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin allows you to describe your posts with locations.
 * Version: 0.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */


/**
 * Adding location taxonomy
 *
 */

function add_location_taxonomy() {
  $labels = array (
    'name'                       => _x( 'Locations', 'taxonomy general name' ),
    'singular_name'              => _x( 'Location', 'taxonomy singular name' ),
    'search_items'               => __( 'Search Locations' ),
    'popular_items'              => __( 'Popular Locations' ),
    'all_items'                  => __( 'All Locations' ),
    'parent_item'                => null,
    'parent_item_colon'          => null,
    'edit_item'                  => __( 'Edit Location' ),
    'update_item'                => __( 'Update Location' ),
    'add_new_item'               => __( 'Add New Location' ),
    'new_item_name'              => __( 'New Location Name' ),
    'separate_items_with_commas' => __( 'Separate locations with commas' ),
    'add_or_remove_items'        => __( 'Add or remove locations' ),
    'choose_from_most_used'      => __( 'Choose from the most used locations' ),
    'not_found'                  => __( 'No locations found.' ),
    'menu_name'                  => __( 'Locations' ),
  );

  $args = array (
    'hierarchical'          => true,
    'labels'                => $labels,
    'show_ui'               => true,
    'show_admin_column'     => true,
    'update_count_callback' => '_update_post_term_count',
    'query_var'             => true,
    'rewrite'               => array( 'slug' => 'location', 'with_front' => 'true' ),
  );

  register_taxonomy ('location', array('post', 'attachment'), $args);
}

add_action ('init', 'add_location_taxonomy', 0);


/**
 * Add automatically continents and countries
 *
 */

function add_locations() {
    $continents = array(
      array('code' => 'AF', 'name' => 'Africa'),
      array('code' => 'AS', 'name' => 'Asia'),
      array('code' => 'EU', 'name' => 'Europe'),
      array('code' => 'NA', 'name' => 'North America'),
      array('code' => 'OC', 'name' => 'Oceania'),
      array('code' => 'SA', 'name' => 'South America'),
      array('code' => 'AN', 'name' => 'Antarctica')
    );

    $countries = array(
      array('iso' => 'AF', 'name' => 'AFGHANISTAN', 'continent' => 'AS'),
      array('iso' => 'AL', 'name' => 'ALBANIA', 'continent' => 'EU'),
      array('iso' => 'DZ', 'name' => 'ALGERIA', 'continent' => 'AF'),
      array('iso' => 'AS', 'name' => 'AMERICAN SAMOA', 'continent' => 'OC'),
      array('iso' => 'AD', 'name' => 'ANDORRA', 'continent' => 'EU'),
      array('iso' => 'AO', 'name' => 'ANGOLA', 'continent' => 'AF'),
      array('iso' => 'AI', 'name' => 'ANGUILLA', 'continent' => 'NA'),
      array('iso' => 'AQ', 'name' => 'ANTARCTICA', 'continent' => 'AN'),
      array('iso' => 'AG', 'name' => 'ANTIGUA AND BARBUDA', 'continent' => 'NA'),
      array('iso' => 'AR', 'name' => 'ARGENTINA', 'continent' => 'SA'),
      array('iso' => 'AM', 'name' => 'ARMENIA', 'continent' => 'AS'),
      array('iso' => 'AW', 'name' => 'ARUBA', 'continent' => 'NA'),
      array('iso' => 'AU', 'name' => 'AUSTRALIA', 'continent' => 'OC'),
      array('iso' => 'AT', 'name' => 'AUSTRIA', 'continent' => 'EU'),
      array('iso' => 'AZ', 'name' => 'AZERBAIJAN', 'continent' => 'AS'),
      array('iso' => 'BS', 'name' => 'BAHAMAS', 'continent' => 'NA'),
      array('iso' => 'BH', 'name' => 'BAHRAIN', 'continent' => 'AS'),
      array('iso' => 'BD', 'name' => 'BANGLADESH', 'continent' => 'AS'),
      array('iso' => 'BB', 'name' => 'BARBADOS', 'continent' => 'NA'),
      array('iso' => 'BY', 'name' => 'BELARUS', 'continent' => 'EU'),
      array('iso' => 'BE', 'name' => 'BELGIUM', 'continent' => 'EU'),
      array('iso' => 'BZ', 'name' => 'BELIZE', 'continent' => 'NA'),
      array('iso' => 'BJ', 'name' => 'BENIN', 'continent' => 'AF'),
      array('iso' => 'BM', 'name' => 'BERMUDA', 'continent' => 'NA'),
      array('iso' => 'BT', 'name' => 'BHUTAN', 'continent' => 'AS'),
      array('iso' => 'BO', 'name' => 'BOLIVIA', 'continent' => 'SA'),
      array('iso' => 'BQ', 'name' => 'BONAIRE', 'continent' => 'NA'),
      array('iso' => 'BA', 'name' => 'BOSNIA AND HERZEGOVINA', 'continent' => 'EU'),
      array('iso' => 'BW', 'name' => 'BOTSWANA', 'continent' => 'AF'),
      array('iso' => 'BV', 'name' => 'BOUVET ISLAND', 'continent' => 'AN'),
      array('iso' => 'BR', 'name' => 'BRAZIL', 'continent' => 'SA'),
      array('iso' => 'IO', 'name' => 'BRITISH INDIAN OCEAN TERRITORY', 'continent' => 'AS'),
      array('iso' => 'VG', 'name' => 'BRITISH VIRGIN ISLANDS', 'continent' => 'NA'),
      array('iso' => 'BN', 'name' => 'BRUNEI', 'continent' => 'AS'),
      array('iso' => 'BG', 'name' => 'BULGARIA', 'continent' => 'EU'),
      array('iso' => 'BF', 'name' => 'BURKINA FASO', 'continent' => 'AF'),
      array('iso' => 'BI', 'name' => 'BURUNDI', 'continent' => 'AF'),
      array('iso' => 'KH', 'name' => 'CAMBODIA', 'continent' => 'AS'),
      array('iso' => 'CM', 'name' => 'CAMEROON', 'continent' => 'AF'),
      array('iso' => 'CA', 'name' => 'CANADA', 'continent' => 'NA'),
      array('iso' => 'CV', 'name' => 'CAPE VERDE', 'continent' => 'AF'),
      array('iso' => 'KY', 'name' => 'CAYMAN ISLANDS', 'continent' => 'NA'),
      array('iso' => 'CF', 'name' => 'CENTRAL AFRICAN REPUBLIC', 'continent' => 'AF'),
      array('iso' => 'TD', 'name' => 'CHAD', 'continent' => 'AF'),
      array('iso' => 'CL', 'name' => 'CHILE', 'continent' => 'SA'),
      array('iso' => 'CN', 'name' => 'CHINA', 'continent' => 'AS'),
      array('iso' => 'CX', 'name' => 'CHRISTMAS ISLAND', 'continent' => 'AS'),
      array('iso' => 'CC', 'name' => 'COCOS (KEELING) ISLANDS', 'continent' => 'AS'),
      array('iso' => 'CO', 'name' => 'COLOMBIA', 'continent' => 'SA'),
      array('iso' => 'KM', 'name' => 'COMOROS', 'continent' => 'AF'),
      array('iso' => 'CK', 'name' => 'COOK ISLANDS', 'continent' => 'OC'),
      array('iso' => 'CR', 'name' => 'COSTA RICA', 'continent' => 'NA'),
      array('iso' => 'HR', 'name' => 'CROATIA', 'continent' => 'EU'),
      array('iso' => 'CU', 'name' => 'CUBA', 'continent' => 'NA'),
      array('iso' => 'CY', 'name' => 'CURACAO', 'continent' => 'NA'),
      array('iso' => 'CY', 'name' => 'CYPRUS', 'continent' => 'EU'),
      array('iso' => 'CZ', 'name' => 'CZECHIA', 'continent' => 'EU'),
      array('iso' => 'CD', 'name' => 'DEMOCRATIC REPUBLIC OF THE CONGO', 'continent' => 'AF'),
      array('iso' => 'DK', 'name' => 'DENMARK', 'continent' => 'EU'),
      array('iso' => 'DJ', 'name' => 'DJIBOUTI', 'continent' => 'AF'),
      array('iso' => 'DM', 'name' => 'DOMINICA', 'continent' => 'NA'),
      array('iso' => 'DO', 'name' => 'DOMINICAN REPUBLIC', 'continent' => 'NA'),
      array('iso' => 'TP', 'name' => 'EAST TIMOR', 'continent' => 'OC'),
      array('iso' => 'EC', 'name' => 'ECUADOR', 'continent' => 'SA'),
      array('iso' => 'EG', 'name' => 'EGYPT', 'continent' => 'AF'),
      array('iso' => 'SV', 'name' => 'EL SALVADOR', 'continent' => 'NA'),
      array('iso' => 'GQ', 'name' => 'EQUATORIAL GUINEA', 'continent' => 'AF'),
      array('iso' => 'ER', 'name' => 'ERITREA', 'continent' => 'AF'),
      array('iso' => 'EE', 'name' => 'ESTONIA', 'continent' => 'EU'),
      array('iso' => 'ET', 'name' => 'ETHIOPIA', 'continent' => 'AF'),
      array('iso' => 'FK', 'name' => 'FALKLAND ISLANDS', 'continent' => 'SA'),
      array('iso' => 'FO', 'name' => 'FAROE ISLANDS', 'continent' => 'EU'),
      array('iso' => 'FJ', 'name' => 'FIJI', 'continent' => 'OC'),
      array('iso' => 'FI', 'name' => 'FINLAND', 'continent' => 'EU'),
      array('iso' => 'FR', 'name' => 'FRANCE', 'continent' => 'EU'),
      array('iso' => 'GF', 'name' => 'FRENCH GUIANA', 'continent' => 'SA'),
      array('iso' => 'PF', 'name' => 'FRENCH POLYNESIA', 'continent' => 'OC'),
      array('iso' => 'TF', 'name' => 'FRENCH SOUTHERN TERRITORIES', 'continent' => 'AN'),
      array('iso' => 'GA', 'name' => 'GABON', 'continent' => 'AF'),
      array('iso' => 'GM', 'name' => 'GAMBIA', 'continent' => 'AF'),
      array('iso' => 'GE', 'name' => 'GEORGIA', 'continent' => 'AS'),
      array('iso' => 'DE', 'name' => 'GERMANY', 'continent' => 'EU'),
      array('iso' => 'GH', 'name' => 'GHANA', 'continent' => 'AF'),
      array('iso' => 'GI', 'name' => 'GIBRALTAR', 'continent' => 'EU'),
      array('iso' => 'GR', 'name' => 'GREECE', 'continent' => 'EU'),
      array('iso' => 'GL', 'name' => 'GREENLAND', 'continent' => 'AN'),
      array('iso' => 'GD', 'name' => 'GRENADA', 'continent' => 'NA'),
      array('iso' => 'GP', 'name' => 'GUADELOUPE', 'continent' => 'NA'),
      array('iso' => 'GU', 'name' => 'GUAM', 'continent' => 'OC'),
      array('iso' => 'GT', 'name' => 'GUATEMALA', 'continent' => 'NA'),
      array('iso' => 'GN', 'name' => 'GUERNSEY', 'continent' => 'EU'),
      array('iso' => 'GN', 'name' => 'GUINEA', 'continent' => 'AF'),
      array('iso' => 'GW', 'name' => 'GUINEA-BISSAU', 'continent' => 'AF'),
      array('iso' => 'GY', 'name' => 'GUYANA', 'continent' => 'SA'),
      array('iso' => 'HT', 'name' => 'HAITI', 'continent' => 'NA'),
      array('iso' => 'HM', 'name' => 'HEARD ISLAND AND MCDONALD ISLANDS', 'continent' => 'AN'),
      array('iso' => 'HN', 'name' => 'HONDURAS', 'continent' => 'NA'),
      array('iso' => 'HK', 'name' => 'HONG KONG', 'continent' => 'AS'),
      array('iso' => 'HU', 'name' => 'HUNGARY', 'continent' => 'EU'),
      array('iso' => 'IS', 'name' => 'ICELAND', 'continent' => 'EU'),
      array('iso' => 'IN', 'name' => 'INDIA', 'continent' => 'AS'),
      array('iso' => 'ID', 'name' => 'INDONESIA', 'continent' => 'AS'),
      array('iso' => 'IR', 'name' => 'IRAN', 'continent' => 'AS'),
      array('iso' => 'IQ', 'name' => 'IRAQ', 'continent' => 'AS'),
      array('iso' => 'IE', 'name' => 'IRELAND', 'continent' => 'EU'),
      array('iso' => 'IM', 'name' => 'ISLE OF MAN', 'continent' => 'EU'),
      array('iso' => 'IL', 'name' => 'ISRAEL', 'continent' => 'AS'),
      array('iso' => 'IT', 'name' => 'ITALY', 'continent' => 'EU'),
      array('iso' => 'CI', 'name' => 'IVORY COAST', 'continent' => 'AF'),
      array('iso' => 'JM', 'name' => 'JAMAICA', 'continent' => 'NA'),
      array('iso' => 'JP', 'name' => 'JAPAN', 'continent' => 'AS'),
      array('iso' => 'JE', 'name' => 'JERSEY', 'continent' => 'EU'),
      array('iso' => 'JO', 'name' => 'JORDAN', 'continent' => 'AS'),
      array('iso' => 'KZ', 'name' => 'KAZAKSTAN', 'continent' => 'AS'),
      array('iso' => 'KE', 'name' => 'KENYA', 'continent' => 'AF'),
      array('iso' => 'KI', 'name' => 'KIRIBATI', 'continent' => 'OC'),
      array('iso' => 'XK', 'name' => 'KOSOVO', 'continent' => 'EU'),
      array('iso' => 'KW', 'name' => 'KUWAIT', 'continent' => 'AS'),
      array('iso' => 'KG', 'name' => 'KYRGYZSTAN', 'continent' => 'AS'),
      array('iso' => 'LA', 'name' => 'LAOS', 'continent' => 'AS'),
      array('iso' => 'LV', 'name' => 'LATVIA', 'continent' => 'EU'),
      array('iso' => 'LB', 'name' => 'LEBANON', 'continent' => 'AS'),
      array('iso' => 'LS', 'name' => 'LESOTHO', 'continent' => 'AF'),
      array('iso' => 'LR', 'name' => 'LIBERIA', 'continent' => 'AF'),
      array('iso' => 'LY', 'name' => 'LIBYA', 'continent' => 'AF'),
      array('iso' => 'LI', 'name' => 'LIECHTENSTEIN', 'continent' => 'EU'),
      array('iso' => 'LT', 'name' => 'LITHUANIA', 'continent' => 'EU'),
      array('iso' => 'LU', 'name' => 'LUXEMBOURG', 'continent' => 'EU'),
      array('iso' => 'MO', 'name' => 'MACAO', 'continent' => 'AS'),
      array('iso' => 'MK', 'name' => 'MACEDONIA', 'continent' => 'EU'),
      array('iso' => 'MG', 'name' => 'MADAGASCAR', 'continent' => 'AF'),
      array('iso' => 'MW', 'name' => 'MALAWI', 'continent' => 'AF'),
      array('iso' => 'MY', 'name' => 'MALAYSIA', 'continent' => 'AS'),
      array('iso' => 'MV', 'name' => 'MALDIVES', 'continent' => 'AS'),
      array('iso' => 'ML', 'name' => 'MALI', 'continent' => 'AF'),
      array('iso' => 'MT', 'name' => 'MALTA', 'continent' => 'EU'),
      array('iso' => 'MH', 'name' => 'MARSHALL ISLANDS', 'continent' => 'OC'),
      array('iso' => 'MQ', 'name' => 'MARTINIQUE', 'continent' => 'NA'),
      array('iso' => 'MR', 'name' => 'MAURITANIA', 'continent' => 'AF'),
      array('iso' => 'MU', 'name' => 'MAURITIUS', 'continent' => 'AF'),
      array('iso' => 'YT', 'name' => 'MAYOTTE', 'continent' => 'AF'),
      array('iso' => 'MX', 'name' => 'MEXICO', 'continent' => 'NA'),
      array('iso' => 'FM', 'name' => 'MICRONESIA', 'continent' => 'OC'),
      array('iso' => 'MD', 'name' => 'MOLDOVA', 'continent' => 'EU'),
      array('iso' => 'MC', 'name' => 'MONACO', 'continent' => 'EU'),
      array('iso' => 'MN', 'name' => 'MONGOLIA', 'continent' => 'AS'),
      array('iso' => 'ME', 'name' => 'MONTENEGRO', 'continent' => 'EU'),
      array('iso' => 'MS', 'name' => 'MONTSERRAT', 'continent' => 'NA'),
      array('iso' => 'MA', 'name' => 'MOROCCO', 'continent' => 'AF'),
      array('iso' => 'MZ', 'name' => 'MOZAMBIQUE', 'continent' => 'AF'),
      array('iso' => 'MM', 'name' => 'MYANMAR', 'continent' => 'AS'),
      array('iso' => 'NA', 'name' => 'NAMIBIA', 'continent' => 'AF'),
      array('iso' => 'NR', 'name' => 'NAURU', 'continent' => 'OC'),
      array('iso' => 'NP', 'name' => 'NEPAL', 'continent' => 'AS'),
      array('iso' => 'NL', 'name' => 'NETHERLANDS', 'continent' => 'EU'),
      array('iso' => 'NC', 'name' => 'NEW CALEDONIA', 'continent' => 'OC'),
      array('iso' => 'NZ', 'name' => 'NEW ZEALAND', 'continent' => 'OC'),
      array('iso' => 'NI', 'name' => 'NICARAGUA', 'continent' => 'NA'),
      array('iso' => 'NE', 'name' => 'NIGER', 'continent' => 'AF'),
      array('iso' => 'NG', 'name' => 'NIGERIA', 'continent' => 'AF'),
      array('iso' => 'NU', 'name' => 'NIUE', 'continent' => 'OC'),
      array('iso' => 'NF', 'name' => 'NORFOLK ISLAND', 'continent' => 'OC'),
      array('iso' => 'KP', 'name' => 'NORTH KOREA', 'continent' => 'AS'),
      array('iso' => 'MP', 'name' => 'NORTHERN MARIANA ISLANDS', 'continent' => 'OC'),
      array('iso' => 'NO', 'name' => 'NORWAY', 'continent' => 'EU'),
      array('iso' => 'OM', 'name' => 'OMAN', 'continent' => 'AS'),
      array('iso' => 'PK', 'name' => 'PAKISTAN', 'continent' => 'AS'),
      array('iso' => 'PW', 'name' => 'PALAU', 'continent' => 'OC'),
      array('iso' => 'PS', 'name' => 'PALESTINE', 'continent' => 'AS'),
      array('iso' => 'PA', 'name' => 'PANAMA', 'continent' => 'NA'),
      array('iso' => 'PG', 'name' => 'PAPUA NEW GUINEA', 'continent' => 'OC'),
      array('iso' => 'PY', 'name' => 'PARAGUAY', 'continent' => 'SA'),
      array('iso' => 'PE', 'name' => 'PERU', 'continent' => 'SA'),
      array('iso' => 'PH', 'name' => 'PHILIPPINES', 'continent' => 'AS'),
      array('iso' => 'PN', 'name' => 'PITCAIRN ISLANDS', 'continent' => 'OC'),
      array('iso' => 'PL', 'name' => 'POLAND', 'continent' => 'EU'),
      array('iso' => 'PT', 'name' => 'PORTUGAL', 'continent' => 'EU'),
      array('iso' => 'PR', 'name' => 'PUERTO RICO', 'continent' => 'NA'),
      array('iso' => 'QA', 'name' => 'QATAR', 'continent' => 'AS'),
      array('iso' => 'CG', 'name' => 'REPUBLIC OF THE CONGO', 'continent' => 'AF'),
      array('iso' => 'RE', 'name' => 'RÉUNION', 'continent' => 'AF'),
      array('iso' => 'RO', 'name' => 'ROMANIA', 'continent' => 'EU'),
      array('iso' => 'RU', 'name' => 'RUSSIA', 'continent' => 'EU'),
      array('iso' => 'RW', 'name' => 'RWANDA', 'continent' => 'AF'),
      array('iso' => 'BL', 'name' => 'SAINT BARTHÉLEMY', 'continent' => 'NA'),
      array('iso' => 'SH', 'name' => 'SAINT HELENA', 'continent' => 'AF'),
      array('iso' => 'KN', 'name' => 'SAINT KITTS AND NEVIS', 'continent' => 'NA'),
      array('iso' => 'LC', 'name' => 'SAINT LUCIA', 'continent' => 'NA'),
      array('iso' => 'MF', 'name' => 'SAINT MARTIN', 'continent' => 'NA'),
      array('iso' => 'PM', 'name' => 'SAINT PIERRE AND MIQUELON', 'continent' => 'NA'),
      array('iso' => 'VC', 'name' => 'SAINT VINCENT AND THE GRENADINES', 'continent' => 'NA'),
      array('iso' => 'WS', 'name' => 'SAMOA', 'continent' => 'OC'),
      array('iso' => 'SM', 'name' => 'SAN MARINO', 'continent' => 'EU'),
      array('iso' => 'ST', 'name' => 'SÃO TOMÉ AND PRÍNCIPE', 'continent' => 'AF'),
      array('iso' => 'SA', 'name' => 'SAUDI ARABIA', 'continent' => 'AS'),
      array('iso' => 'SN', 'name' => 'SENEGAL', 'continent' => 'AF'),
      array('iso' => 'RS', 'name' => 'SERBIA', 'continent' => 'EU'),
      array('iso' => 'SC', 'name' => 'SEYCHELLES', 'continent' => 'AF'),
      array('iso' => 'SL', 'name' => 'SIERRA LEONE', 'continent' => 'AF'),
      array('iso' => 'SG', 'name' => 'SINGAPORE', 'continent' => 'AS'),
      array('iso' => 'SX', 'name' => 'SINT MARTEEN', 'continent' => 'NA'),
      array('iso' => 'SK', 'name' => 'SLOVAKIA', 'continent' => 'EU'),
      array('iso' => 'SI', 'name' => 'SLOVENIA', 'continent' => 'EU'),
      array('iso' => 'SB', 'name' => 'SOLOMON ISLANDS', 'continent' => 'OC'),
      array('iso' => 'SO', 'name' => 'SOMALIA', 'continent' => 'AF'),
      array('iso' => 'ZA', 'name' => 'SOUTH AFRICA', 'continent' => 'AF'),
      array('iso' => 'GS', 'name' => 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS', 'continent' => 'AN'),
      array('iso' => 'KR', 'name' => 'SOUTH KOREA', 'continent' => 'AS'),
      array('iso' => 'SS', 'name' => 'SOUTH SUDAN', 'continent' => 'AF'),
      array('iso' => 'ES', 'name' => 'SPAIN', 'continent' => 'EU'),
      array('iso' => 'LK', 'name' => 'SRI LANKA', 'continent' => 'AS'),
      array('iso' => 'SD', 'name' => 'SUDAN', 'continent' => 'AF'),
      array('iso' => 'SR', 'name' => 'SURINAME', 'continent' => 'SA'),
      array('iso' => 'SJ', 'name' => 'SVALBARD AND JAN MAYEN', 'continent' => 'EU'),
      array('iso' => 'SZ', 'name' => 'SWAZILAND', 'continent' => 'AF'),
      array('iso' => 'SE', 'name' => 'SWEDEN', 'continent' => 'EU'),
      array('iso' => 'CH', 'name' => 'SWITZERLAND', 'continent' => 'EU'),
      array('iso' => 'SY', 'name' => 'SYRIA', 'continent' => 'AS'),
      array('iso' => 'TW', 'name' => 'TAIWAN', 'continent' => 'AS'),
      array('iso' => 'TJ', 'name' => 'TAJIKISTAN', 'continent' => 'AS'),
      array('iso' => 'TZ', 'name' => 'TANZANIA', 'continent' => 'AF'),
      array('iso' => 'TH', 'name' => 'THAILAND', 'continent' => 'AS'),
      array('iso' => 'TG', 'name' => 'TOGO', 'continent' => 'AF'),
      array('iso' => 'TK', 'name' => 'TOKELAU', 'continent' => 'OC'),
      array('iso' => 'TO', 'name' => 'TONGA', 'continent' => 'OC'),
      array('iso' => 'TT', 'name' => 'TRINIDAD AND TOBAGO', 'continent' => 'NA'),
      array('iso' => 'TN', 'name' => 'TUNISIA', 'continent' => 'AF'),
      array('iso' => 'TR', 'name' => 'TURKEY', 'continent' => 'AS'),
      array('iso' => 'TM', 'name' => 'TURKMENISTAN', 'continent' => 'AS'),
      array('iso' => 'TC', 'name' => 'TURKS AND CAICOS ISLANDS', 'continent' => 'NA'),
      array('iso' => 'TV', 'name' => 'TUVALU', 'continent' => 'OC'),
      array('iso' => 'UG', 'name' => 'UGANDA', 'continent' => 'AF'),
      array('iso' => 'UA', 'name' => 'UKRAINE', 'continent' => 'EU'),
      array('iso' => 'AE', 'name' => 'UNITED ARAB EMIRATES', 'continent' => 'AS'),
      array('iso' => 'GB', 'name' => 'UNITED KINGDOM', 'continent' => 'EU'),
      array('iso' => 'US', 'name' => 'UNITED STATES', 'continent' => 'NA'),
      array('iso' => 'UM', 'name' => 'UNITED STATES MINOR OUTLYING ISLANDS', 'continent' => 'OC'),
      array('iso' => 'VI', 'name' => 'UNITED STATES VIRGIN ISLANDS', 'continent' => 'NA'),
      array('iso' => 'UY', 'name' => 'URUGUAY', 'continent' => 'SA'),
      array('iso' => 'UZ', 'name' => 'UZBEKISTAN', 'continent' => 'AS'),
      array('iso' => 'VU', 'name' => 'VANUATU', 'continent' => 'OC'),
      array('iso' => 'VA', 'name' => 'VATICAN CITY', 'continent' => 'EU'),
      array('iso' => 'VE', 'name' => 'VENEZUELA', 'continent' => 'SA'),
      array('iso' => 'VN', 'name' => 'VIETNAM', 'continent' => 'AS'),
      array('iso' => 'WF', 'name' => 'WALLIS AND FUTUNA', 'continent' => 'OC'),
      array('iso' => 'EH', 'name' => 'WESTERN SAHARA', 'continent' => 'AF'),
      array('iso' => 'YE', 'name' => 'YEMEN', 'continent' => 'AS'),
      array('iso' => 'ZM', 'name' => 'ZAMBIA', 'continent' => 'AF'),
      array('iso' => 'ZW', 'name' => 'ZIMBABWE', 'continent' => 'AF'),
    );

    foreach($continents as $continent) {
      $continent_name = ucwords(mb_strtolower($continent['name']));
      if(!get_term_by('name', $continent_name, 'location')) {
        wp_insert_term($continent_name, 'location');
      }
    }

    foreach($countries as $country) {
      $country_name = ucwords(mb_strtolower($country['name']));
      $country_continent_code = $country['continent'];

      foreach ($continents as $continent) {
        if($continent['code'] == $country_continent_code) {
	  $country_continent_name = $continent['name'];
	  break;
	}
      }

      $continent_term = get_term_by('name', $country_continent_name, 'location');
      $continent_term_id = $continent_term->term_id;

      if(!get_term_by('name', $country_name, 'location')) {
        wp_insert_term($country_name, 'location', array('parent' => $continent_term_id));
	delete_option('location_children');
      }
    }
}

add_action('init', 'add_locations', 100);


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
 * Flush rewrites when the plugin is activated
 *
 */

function location_flush_rewrites() {
  flush_rewrite_rules();
}

# Prevent 404 errors on locations' archive

register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
register_activation_hook( __FILE__, 'location_flush_rewrites' );

add_action( 'init', 'location_flush_rewrites' );
