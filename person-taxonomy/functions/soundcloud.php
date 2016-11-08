<?php

/**
 * Getting Soundcloud username
 *
 * @return string
 *
 */

function get_soundcloud_username () {
  return get_person_option('soundcloud');
}


/**
 * Getting Souncloud link
 *
 * @return string
 *
 */

function get_soundcloud_link () {
  $soundcloud = get_soundcloud_username();
  if ($soundcloud != '') {
    $soundcloud = 'https://soundcloud.com/' . $soundcloud;
  }

  return $soundcloud;
}


/**
 * Getting Soundcloud tracks (max = 1)
 *
 * @param int $max
 *
 * @return array $data
 *
 */

function get_soundcloud_tracks ($max = 1, $width = '100%', $height = 250) {
  $data = [];
  $username = get_person_option('soundcloud');
  global $SOUNDCLOUD_CLIENT_ID;

  if ($username) {
    $client_id = $SOUNDCLOUD_CLIENT_ID;
    $results = fetch_curl_data('https://api.soundcloud.com/users/' . $username . '/tracks.json?client_id=' . $client_id);
    $results = json_decode($results, JSON_UNESCAPED_SLASHES);

    if ($results) {
      $i = 0;

      foreach($results as $track) {
        $user = $track['user']['id'];
        $url = $track['permalink_url'];
        $title = $track['title'];

        $json = fetch_curl_data('http://soundcloud.com/oembed?format=json&url=' . $url . '&maxheight=' . $height . '&maxwidth' . $width);
        $json = json_decode($json, JSON_UNESCAPED_SLASHES);
        $iframe = $json['html'];

        array_push($data, array('user' => $user, 'title' => $title, 'html' => $iframe));

        $i += 1;
        if ($i >= $max) {
          break;
        }
      }
    }
  }

  return $data;
}

?>

