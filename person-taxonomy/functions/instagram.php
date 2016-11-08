<?php

/**
 * Getting Instagram username
 *
 * @return string
 *
 */

function get_instagram_username () {
  return get_person_option('instagram');
}


/**
 * Getting Instagram link
 *
 * @return string
 *
 */

function get_instagram_link () {
  $link = '';
  $username = get_instagram_username();

  if ($username != '') {
    $link = '<a href="https://instagram.com/' . $username . '"> ' . $username . ' </a>';
  }

  return $link;
}


/**
 * Getting Instagram pictures (max = 20)
 *
 * @param int $max
 *
 * @return array $data
 *
 */

function get_instagram_pictures ($max = 20) {
  $data = [];
  $username = get_instagram_username();

  if ($username) {
    $result = fetch_curl_data('https://www.instagram.com/' . $username . '/media/');
    $result = json_decode($result, JSON_UNESCAPED_SLASHES);

    if ($result) {
      $i = 0;

      foreach ($result['items'] as $post) {
        $link = $post['link'];
        $text = $post['caption']['text'];
        $image = $post['images']['standard_resolution']['url'];
        array_push($data, array('link' => $link, 'text' => $text, 'image' => $image));

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

