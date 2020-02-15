<?php
/**
 * Create a dashboard widget with birthdays of the current month.
 *
 */

function birthdays_dashboard_widget(){
  # Get all persons created
  $terms = get_terms( array(
    'taxonomy' => 'person',
    'hide_empty' => false,
    'fields' => 'all',
    'limit' => -1,
  ) );

  # Get today's month and day
  $now = new DateTime();
  $month = date( 'm' );
  $day = date( 'd' );
  $results = array();

  # Retrieve all persons born this month
  foreach( $terms as $person ){
    $id = $person->term_id;
    $term_meta = get_option( 'taxonomy_' . $id );

    if( $term_meta[ 'birthdate' ] != '' ){
      $birthdate = DateTime::createFromFormat( 'Y-m-d', $term_meta[ 'birthdate' ] );

      if( $month == $birthdate->format( 'm' ) ){
        array_push( $results, $person );
      }
    }
  }

  # If array has results
  $count = sizeof( $results );

  if( $count > 0 ){
    # Sort array by day number
    uasort( $results, function( $a, $b ){
      $a_id = $a->term_id;
      $b_id = $b->term_id;
      $a_meta = get_option( 'taxonomy_' . $a_id );
      $b_meta = get_option( 'taxonomy_' . $b_id );
      $a_date = DateTime::createFromFormat( 'Y-m-d', $a_meta[ 'birthdate' ] );
      $b_date = DateTime::createFromFormat( 'Y-m-d', $b_meta[ 'birthdate' ] );
      $a_str = $a_date->format( 'j' );
      $b_str = $b_date->format( 'j' );

      return ( $a_str < $b_str ) ? -1 : 1;
    });

    # Display all persons with their birthday and age
    ?>
    <table cellspacing="0" cellpadding="3" width="100%">
      <?php
      foreach( $results as $person ){
        $id = $person->term_id;
        $name = $person->name;
        $slug = $person->slug;
        $description = $person->description;
        $link = get_term_link( $person );

        # Retrieve all custom data
        $term_meta = get_option( 'taxonomy_' . $id );
        $gender    = $term_meta[ 'gender' ];
        $birthdate = DateTime::createFromFormat( 'Y-m-d', $term_meta[ 'birthdate' ] );
        $deathdate = DateTime::createFromFormat( 'Y-m-d', $term_meta[ 'deathdate' ] );

        if( $deathdate == '' ) $age = $now->diff( $birthdate )->y;
	else $age = $deathdate->diff( $birthdate )->y;

	# Display data
        ?>
        <tr<?php if( $day == $birthdate->format( 'd' ) ) { ?> class="highlight"<?php } ?>>
	  <td><?php echo $birthdate->format( 'd.' ); ?></td>
          <td>
            <a href="<?php echo $link; ?>" target="blank">
	      <?php echo $name; ?>
            </a>
          </td>
          <td><small><?php echo $age . __( ' years old', 'person-taxonomy' ); ?></small></td>
          <td>
	      <small><em><?php
                  echo '(' . $birthdate->format( 'Y' ) . '-';

                  if( $deathdate != '' ){
                      echo $deathdate->format( 'Y' );
                  }
                  else {
                      echo '.........';
                  } echo ')'; ?></em></small>
	  </td>
        </tr>
        <?php
      }
      ?>
    </table>
    <?php
  } else {
    echo __( 'There is nobody born this month among your persons.', 'person-taxonomy' );
  }
}


/**
 * Create a dashboard widget with death anniversaries of the current month.
 *
 */

function deathdays_dashboard_widget(){
  # Get all persons created
  $terms = get_terms( array(
    'taxonomy' => 'person',
    'hide_empty' => false,
    'fields' => 'all',
    'limit' => -1,
  ) );

  # Get today's month and day
  $now = new DateTime();
  $month = date( 'm' );
  $day = date( 'd' );
  $results = array();

  # Retrieve all persons who died this month
  foreach( $terms as $person ){
    $id = $person->term_id;
    $term_meta = get_option( 'taxonomy_' . $id );

    if( $term_meta[ 'deathdate' ] != '' ){
      $deathdate = DateTime::createFromFormat( 'Y-m-d', $term_meta[ 'deathdate' ] );

      if( $month == $deathdate->format( 'm' ) ){
        array_push( $results, $person );
      }
    }
  }

  # If array has results
  $count = sizeof( $results );

  if( $count > 0 ){
    # Sort array by day number
    uasort( $results, function( $a, $b ){
      $a_id = $a->term_id;
      $b_id = $b->term_id;
      $a_meta = get_option( 'taxonomy_' . $a_id );
      $b_meta = get_option( 'taxonomy_' . $b_id );
      $a_date = DateTime::createFromFormat( 'Y-m-d', $a_meta[ 'deathdate' ] );
      $b_date = DateTime::createFromFormat( 'Y-m-d', $b_meta[ 'deathdate' ] );
      $a_str = $a_date->format( 'd ');
      $b_str = $b_date->format( 'd ');

      return ( $a_str < $b_str ) ? -1 : 1;
    } );

    # Display all persons with their deathday and age
    ?>
    <table cellspacing="0" cellpadding="3" width="100%">
      <?php
      foreach( $results as $person ){
        $id = $person->term_id;
        $name = $person->name;
        $slug = $person->slug;
        $description = $person->description;
        $link = get_term_link( $person );

        # Retrieve all custom data
        $term_meta = get_option( 'taxonomy_' . $id );
        $gender    = $term_meta[ 'gender' ];
        $birthdate = DateTime::createFromFormat( 'Y-m-d', $term_meta[ 'birthdate' ] );
        $deathdate = DateTime::createFromFormat( 'Y-m-d', $term_meta[ 'deathdate' ] );

        $now = new DateTime();
	$age = $deathdate->diff( $birthdate )->y;

	# Display data
        ?>
        <tr<?php if( $day == $deathdate->format( 'd' ) ) { ?> class="highlight"<?php } ?>>
	  <td><?php echo $deathdate->format( 'd.' ); ?></td>
          <td>
            <a href="<?php echo $link; ?>" target="blank">
	      <?php echo $name; ?>
            </a>
          </td>
          <td><small><?php echo $age . __( ' years old', 'person-taxonomy' ); ?></small></td>
          <td>
	      <small><em title="<?php
                      $since_date = $now->diff( $deathdate )->y;

                      if( $gender == 1){
                          echo _x( 'deceased on ', 'year of date for female', 'person-taxonomy' );
                          echo $deathdate->format( 'Y' );

                          if( $since_date > 1 ){
                              printf( __( ', %s years ago', 'person-taxonomy' ), $since_date );
                          } else {
                              printf( __( ', %s year ago', 'person-taxonomy' ), $since_date );
                          }
                      } else {
                          echo _x( 'deceased on ', 'year of date for male', 'person-taxonomy' );
                          echo $deathdate->format( 'Y' );

                          if( $since_date > 1 ){
                              printf( __( ', %s years ago', 'person-taxonomy' ), $since_date );
                          } else {
                              printf( __( ', %s year ago', 'person-taxonomy' ), $since_date );
                          }
                      }
                  ?>"><?php
                  echo '(' . $birthdate->format( 'Y' ) . '-';

                  if( $deathdate != '' ){
                      echo $deathdate->format( 'Y' );
                  }
                  else {
                      echo '......';
                  } echo ')'; ?></em></small>
	  </td>
        </tr>
        <?php
      }
      ?>
    </table>
    <?php
  } else {
    echo __( 'There is nobody who died this month among your persons.', 'person-taxonomy' );
  }
}


/**
 * Create a dashboard widget with persons without posts
 *
 */

function persons_without_posts_dashboard_widget(){
  # Get all persons created
  $terms = get_terms( array(
    'taxonomy'   => 'person',
    'fields'     => 'all',
    'orderby'    => 'name',
    'order'      => 'asc',
    'limit'      => -1,
    'hide_empty' => false,
  ) );

  $results = array();

  foreach( $terms as $term ){
    if( $term->count < 1 ){
      array_push( $results, $term );
    }
  }

  # Display all persons with their deathday and age
  if( !empty( $results ) ){
    $sentence = count( $results )  . ' ' .  __( 'Persons without posts', 'person-taxonomy' ) . '.';
    echo strtolower( $sentence );
  ?>
  <table cellspacing="0" cellpadding="3" width="100%" style="margin-top:5px;">
    <?php
    foreach( $results as $term ){
      if( $term->count < 1 ){
      ?>
      <tr>
          <td>
            <a href="<?php echo get_edit_term_link( $term->term_id, 'person', 'post' ); ?>" target="blank">
              <?php echo $term->name; ?>
          </td>
          <td><?php echo $term->count; ?></td>
      </tr>
      <?php
      }
    }
    ?>
  </table>
  <?php
  } else {
    echo __( 'All your persons have a least one post.', 'person-taxonomy' );
  }
}


/**
 * Add dashboard widgets
 *
 */

function add_dashboard_widgets(){
  $now = new DateTime();

  $birthdays_title       = __( 'Birthdays of the month', 'person-taxonomy' );
  $birthdays_function    = 'birthdays_dashboard_widget';
  $birthdays_widget_slug = 'birthdays-dashboard-widget';

  $deathdays_title       = __( 'Death anniversaries of the month', 'person-taxonomy' );
  $deathdays_function    = 'deathdays_dashboard_widget';
  $deathdays_widget_slug = 'deathdays-dashboard-widget';

  $persons_without_posts_title       = __( 'Persons without posts', 'person-taxonomy' );
  $persons_without_posts_function    = 'persons_without_posts_dashboard_widget';
  $persons_without_posts_widget_slug = 'persons-without-posts-dashboard-widget';

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

  wp_add_dashboard_widget(
    $persons_without_posts_widget_slug,
    $persons_without_posts_title,
    $persons_without_posts_function
  );
}

if( $DASHBOARD_WIDGET_ENABLED ){
  add_action( 'wp_dashboard_setup', 'add_dashboard_widgets' );
}

?>
