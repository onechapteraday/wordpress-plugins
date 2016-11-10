<?php

/**
 * Getting Twitter username
 *
 * @return string
 *
 */

function get_twitter_username () {
  return get_person_option('twitter');
}


/**
 * Getting the last tweets from username
 *
 * @return array $data
 *
 */

function get_twitter_statuses ($max = 3) {
  global $TWITTER_API_KEY;
  global $TWITTER_API_SECRET;
  $data = [];
  $username = get_twitter_username();

  if ($username != '') {
    # Authentification variables
    $api_key = urlencode($TWITTER_API_KEY);
    $api_secret = urlencode($TWITTER_API_SECRET);
    $auth_url = 'https://api.twitter.com/oauth2/token';

    $api_credentials = base64_encode($api_key . ':' . $api_secret);
    $auth_headers = 'Authorization: Basic ' . $api_credentials . "\r\n" . 'Content-Type: application/x-www-form-urlencoded;charset=UTF-8' . "\r\n";

    $auth_context = stream_context_create(
      array(
        'http' => array(
          'header' => $auth_headers,
          'method' => 'POST',
          'content'=> http_build_query (array('grant_type' => 'client_credentials')),
        )
      )
    );

    $auth_response = json_decode(file_get_contents($auth_url, 0, $auth_context), true);
    $auth_token = $auth_response['access_token'];

    # Get tweets
    $data_url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
    $data_context = stream_context_create(
      array(
        'http' => array(
          'header' => 'Authorization: Bearer ' . $auth_token . "\r\n"
        )
      )
    );

    $results = json_decode(file_get_contents($data_url . '?count=' . $max . '&screen_name=' . urlencode($username), 0, $data_context), true);

    # Save tweets
    foreach($results as $tweet) {
      $date = $tweet['created_at'];
      $text = $tweet['text'];
      $user = $tweet['user']['screen_name'];

      array_push($data, array('date' => $date, 'text' => $text, 'user' => $user));
    }
  }

  return $data;
}

?>
