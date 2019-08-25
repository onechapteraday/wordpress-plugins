<?php
/**
 * Create a dashboard widget with birthdays of the current month.
 *
 */

function birthdays_dashboard_widget () {
  # Get all persons created
  $terms = get_terms(array(
    'taxonomy' => 'person',
    'hide_empty' => false,
    'fields' => 'all',
    'limit' => -1,
  ));

  # Get today's month and day
  $now = new DateTime();
  $month = date('m');
  $day = date('d');
  $results = array();

  # Retrieve all persons born this month
  foreach ($terms as $person) {
    $id = $person->term_id;
    $term_meta = get_option( 'taxonomy_' . $id );
    if ($term_meta['birthdate'] != '') {
      $birthdate = DateTime::createFromFormat('Y-m-d', $term_meta['birthdate']);
      if ($month == $birthdate->format('m')) {
        array_push($results, $person);
      }
    }
  }

  # If array has results
  $count = sizeof($results);

  if($count > 0) {
    # Sort array by day number
    uasort($results, function($a, $b) {
      $a_id = $a->term_id;
      $b_id = $b->term_id;
      $a_meta = get_option( 'taxonomy_' . $a_id );
      $b_meta = get_option( 'taxonomy_' . $b_id );
      $a_date = DateTime::createFromFormat('Y-m-d', $a_meta['birthdate']);
      $b_date = DateTime::createFromFormat('Y-m-d', $b_meta['birthdate']);
      $a_str = $a_date->format('j');
      $b_str = $b_date->format('j');

      return ($a_str < $b_str) ? -1 : 1;
    });

    # Display all persons with their birthday and age
    ?>
    <table cellspacing="0" cellpadding="3" width="100%">
      <?php
      foreach ($results as $person) {
        $id = $person->term_id;
        $name = $person->name;
        $slug = $person->slug;
        $description = $person->description;
        $link = get_term_link($person);

        # Retrieve all custom data
        $term_meta = get_option( 'taxonomy_' . $id );
        $birthdate = DateTime::createFromFormat('Y-m-d', $term_meta['birthdate']);
        $deathdate = DateTime::createFromFormat('Y-m-d', $term_meta['deathdate']);
        if ($deathdate == '') $age = $now->diff($birthdate)->y;
	else $age = $deathdate->diff($birthdate)->y;

	# Display data
        ?>
        <tr<?php if ($day == $birthdate->format('d')) { ?> class="highlight"<?php } ?>>
	  <td><?php echo $birthdate->format('M. j'); ?></td>
          <td>
            <a href="<?php echo $link; ?>" target="blank">
	      <?php echo $name; ?>
            </a>
          </td>
          <td><small><?php echo $age . ' years old'; ?></small></td>
          <td>
	      <small><em><?php echo 'born on ' . $birthdate->format('Y'); if ($deathdate != '') { echo ', deceased on ' . $deathdate->format('Y'); } ?></em></small>
	  </td>
        </tr>
        <?php
      }
      ?>
    </table>
    <?php
  } else {
    echo 'There is nobody born this month among your persons.';
  }
}


/**
 * Create a dashboard widget with death anniversaries of the current month.
 *
 */

function deathdays_dashboard_widget () {
  # Get all persons created
  $terms = get_terms(array(
    'taxonomy' => 'person',
    'hide_empty' => false,
    'fields' => 'all',
    'limit' => -1,
  ));

  # Get today's month and day
  $now = new DateTime();
  $month = date('m');
  $day = date('d');
  $results = array();

  # Retrieve all persons who died this month
  foreach ($terms as $person) {
    $id = $person->term_id;
    $term_meta = get_option( 'taxonomy_' . $id );
    if ($term_meta['deathdate'] != '') {
      $deathdate = DateTime::createFromFormat('Y-m-d', $term_meta['deathdate']);
      if ($month == $deathdate->format('m')) {
        array_push($results, $person);
      }
    }
  }

  # If array has results
  $count = sizeof($results);

  if($count > 0) {
    # Sort array by day number
    uasort($results, function($a, $b) {
      $a_id = $a->term_id;
      $b_id = $b->term_id;
      $a_meta = get_option( 'taxonomy_' . $a_id );
      $b_meta = get_option( 'taxonomy_' . $b_id );
      $a_date = DateTime::createFromFormat('Y-m-d', $a_meta['deathdate']);
      $b_date = DateTime::createFromFormat('Y-m-d', $b_meta['deathdate']);
      $a_str = $a_date->format('j');
      $b_str = $b_date->format('j');

      return ($a_str < $b_str) ? -1 : 1;
    });

    # Display all persons with their deathday and age
    ?>
    <table cellspacing="0" cellpadding="3" width="100%">
      <?php
      foreach ($results as $person) {
        $id = $person->term_id;
        $name = $person->name;
        $slug = $person->slug;
        $description = $person->description;
        $link = get_term_link($person);

        # Retrieve all custom data
        $term_meta = get_option( 'taxonomy_' . $id );
        $birthdate = DateTime::createFromFormat('Y-m-d', $term_meta['birthdate']);
        $deathdate = DateTime::createFromFormat('Y-m-d', $term_meta['deathdate']);
        $now = new DateTime();
	$age = $deathdate->diff($birthdate)->y;

	# Display data
        ?>
        <tr<?php if ($day == $deathdate->format('d')) { ?> class="highlight"<?php } ?>>
	  <td><?php echo $deathdate->format('M. j'); ?></td>
          <td>
            <a href="<?php echo $link; ?>" target="blank">
	      <?php echo $name; ?>
            </a>
          </td>
          <td><small><?php echo $age . ' years old'; ?></small></td>
          <td>
	      <small><em><?php
                  echo 'died on ' . $deathdate->format( 'Y' );

                  if( $deathdate != '' ){
                      $since_date = $now->diff( $deathdate )->y;
                      echo ', deceased ' . $since_date;

                      if( $since_date > 1 ){
                          echo ' years ago';
                      } else {
                          echo ' year ago';
                      }
                  }
              ?></em></small>
	  </td>
        </tr>
        <?php
      }
      ?>
    </table>
    <?php
  } else {
    echo 'There is nobody who died this month among your persons.';
  }
}


function add_dashboard_widgets() {
  $now = new DateTime();

  $birthdays_title       = $now->format( 'F' ) . ' birthdays';
  $birthdays_function    = 'birthdays_dashboard_widget';
  $birthdays_widget_slug = 'birthdays-dashboard-widget';

  $deathdays_title       = 'Death anniversaries of the month';
  $deathdays_function    = 'deathdays_dashboard_widget';
  $deathdays_widget_slug = 'deathdays-dashboard-widget';

  wp_add_dashboard_widget(
    $birthdays_widget_slug,
    $birthdays_title,
    $birthdays_function
  );

  wp_add_dashboard_widget(
    $deathdays_widget_slug,
    $deathdays_title,
    $deathdays_function
  );
}

if( $DASHBOARD_WIDGET_ENABLED ){
  add_action( 'wp_dashboard_setup', 'add_dashboard_widgets' );
}

?>
