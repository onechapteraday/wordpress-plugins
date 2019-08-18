<?php
/**
 * Fetching data with CURL
 *
 * @param object $url
 *
 * @return object $result
 *
 */

function fetch_curl_data($url){
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_TIMEOUT, 20);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $result = curl_exec($ch);

  if (FALSE === $result)
    throw new Exception(curl_error($ch), curl_errno($ch));

  curl_close($ch);
  return $result;
}


/**
 * Getting term specific argument
 *
 * @param object $arg
 *
 * @return string
 *
 */

function get_person_arg ( $arg ) {
  $term = get_queried_object();

  return $term->$arg;
}


/**
 * Getting term specific option
 *
 * @param object $option
 *
 * @return string
 *
 */

function get_person_option ( $option ) {
  $person = get_queried_object();
  $id = $person->term_id;
  $term_meta = get_option( 'taxonomy_' . $id );

  return isset( $term_meta[ $option ] ) ? $term_meta[ $option ] : false;
}


/**
 * Getting person birthdate
 *
 * @return DateTime $date
 *
 */

function get_person_birthdate () {
  $date = get_person_option( 'birthdate' );
  $date = DateTime::createFromFormat( 'Y-m-d', $date );

  return $date;
}


/**
 * Getting person deathdate
 *
 * @return DateTime $date
 *
 */

function get_person_deathdate () {
  $date = get_person_option( 'deathdate' );
  $date = DateTime::createFromFormat( 'Y-m-d', $date );

  return $date;
}

?>
