<?php
session_start();
include("conn.php");

$hasResults = false; // controls if else later 
/*
* searches the database to determine if the coach ID passed in URL actually has enough (or any) reviews.
* If there are no results, boolean remains false. If there are, turns true. 
*/
if (isset($_GET['coachID'])) {
  // want to read in from database all the data
  $coachID = $_GET['coachID'];
  // checks this coach actually has testimonials,
  $findIfCoachHasTestimonials = "SELECT * FROM webdev_testimonials WHERE coach_id = '$coachID'";
  $coachHasTestimonials = $conn->query($findIfCoachHasTestimonials);
  if (!$coachHasTestimonials) {
    echo $conn->error;
  }

  $num = $coachHasTestimonials->num_rows;

  // if returns any results, attempts to reset password and email them the value
  if ($num > 6) {
    $hasResults = true;
  }


  /** If the coach ID posted in the URL has at least 6 testimonials, prints 6 random testimonials from that coach
   * else grab 6 random testimonials from the database for all coaches - 
   *  to stop user causing errors if they pass in a coach without testimonies.
   *
   */
  if ($hasResults == true) {
    // grabs all testimonials of coach if they have 6 + testimonials
    $findNumberOfTestimonials = "SELECT * FROM webdev_testimonials 
  WHERE coach_id = '$coachID' 
  ORDER BY id DESC";
    $runTestimonials = $conn->query($findNumberOfTestimonials);
  } else { // else, if <6 for a specific coach, just get from all
    $findNumberOfTestimonials = "SELECT * FROM webdev_testimonials ORDER BY id DESC";
    $runTestimonials = $conn->query($findNumberOfTestimonials);
  }
  $possibleTestimonials = array();
  // cycles through all testimonials for that coach, stores the IDs in an array to use be randomly grabbed and displayed
  while ($row = $runTestimonials->fetch_assoc()) {
    $testimonialID = $row['id'];
    $possibleTestimonials[] = $testimonialID;
  }

  // generate a new array from all possible testimonials, generate 6 numbers and display.
  $testimonialsToGrab = array();


  /**
   * Generates a random number between IDs in the array from the database for testimonials 6 times.
   * If the number is already in the array, rerolls it. 
   * Pushes it and updates the counter if it is a unique number.
   * When printing out the 2d array, example value corresponds to:
   *[0][0] = the user ID
   *[0][1] = the coach ID
   *[0][2] = the testimonial title 
   *[0][3] = the actual testimonial text
   *
   */
  $randomCount = 0;
  do {
    do {
      $randomArrayKey = array_rand($possibleTestimonials, 1);
      $randomID = $possibleTestimonials[$randomArrayKey];
    } while (in_array($randomID, $testimonialsToGrab)); // if in array, rerolls
    $testimonialsToGrab[] = $randomID;
    $randomCount++;
  } while ($randomCount < 6);

  $testimonialQuery = implode(",", $testimonialsToGrab);
  $generateTestimonials = "SELECT * FROM webdev_testimonials WHERE id IN($testimonialQuery)";
  $testimonialResults = $conn->query($generateTestimonials);

  if (!$testimonialResults) {
    echo $conn->error;
  }
  $testimonials = array();
  while ($row = $testimonialResults->fetch_assoc()) {


    // store data of this row's details in a temporary array
    $tempTest = array($row['user_id'], $row['coach_id'], $row['title'], $row['testimonial']);


    // push it into the permanent array
    array_push($testimonials, $tempTest);
  } // end of while loop
}


/**
 * If coach ID hasn't been passed through, just gets 6 testimonials from all possible.
 */

if (!isset($_GET['coachID'])) {
  // grabs all testimonials
  $findNumberOfTestimonials = "SELECT * FROM webdev_testimonials ORDER BY id DESC";
  $runTestimonials = $conn->query($findNumberOfTestimonials);
  $possibleTestimonials = array();
  // cycles through all testimonials for that coach, stores the IDs in an array to use be randomly grabbed and displayed
  while ($row = $runTestimonials->fetch_assoc()) {
    $testimonialID = $row['id'];
    $possibleTestimonials[] = $testimonialID;
  }


  // generate a new array from all possible testimonials, generate 6 numbers and display.
  $testimonialsToGrab = array();

  /**
   * Generates a random number between IDs in the array from the database for testimonials 6 times.
   * If the number is already in the array, rerolls it. 
   * Pushes it and updates the counter if it is a unique number.
   * When printing out the 2d array, example value corresponds to:
   *[0][0] = the user ID
   *[0][1] = the coach ID
   *[0][2] = the testimonial title 
   *[0][3] = the actual testimonial text 
   */
  $randomCount = 0;
  do {
    do {
      $randomArrayKey = array_rand($possibleTestimonials, 1);
      $randomID = $possibleTestimonials[$randomArrayKey];
    } while (in_array($randomID, $testimonialsToGrab)); // if in array, rerolls
    $testimonialsToGrab[] = $randomID;
    $randomCount++;
  } while ($randomCount < 6);

  $testimonialQuery = implode(",", $testimonialsToGrab);
  $generateTestimonials = "SELECT * FROM webdev_testimonials WHERE id IN($testimonialQuery)";
  $testimonialResults = $conn->query($generateTestimonials);

  if (!$testimonialResults) {
    echo $conn->error;
  }
  $testimonials = array();
  while ($row = $testimonialResults->fetch_assoc()) {


    // store data of this row's details in a temporary array
    $tempTest = array($row['user_id'], $row['coach_id'], $row['title'], $row['testimonial']);


    // push it into the permanent array
    array_push($testimonials, $tempTest);
  } // end fo while loop
}



?>

<!DOCTYPE html>
<html>

<head>
  <meta charset='UTF-8'>
  <meta name='viewport' content='width=device-width, initial-scale=1'>
  <title>Gymafi | Testimonials</title>
  <link href='styles/bulma.css' rel='stylesheet'>
  <link href='styles/gui.css' rel='stylesheet'>
  <script defer src='https://use.fontawesome.com/releases/v5.3.1/js/all.js'></script>
  <script src='https://code.jquery.com/jquery-3.4.1.js' integrity='sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=' crossorigin='anonymous'></script>
  <script src='script/myScript.js'></script>


</head>

<body class='has-background-grey-lighter' id='testimonialBody'>
  <!--If not logged in, displays login/sign up buttons. 
    Else, if logged in displays log out button. -->
  <?php
  if ((!isset($_SESSION['gymafi_userid']) && (!isset($_SESSION['gymafi_coachid'])
    && (!isset($_SESSION['gymafi_superadmin']))))) {
  ?> <nav class='navbar is-dark' role='navigation' aria-label='main navigation'>
      <div class='navbar-end'>
        <div class='navbar-item'>
          <div class='buttons loginButtons'>
            <a class='button is-primary' href='signup.php'>
              <strong>Sign up</strong>
            </a>
            <a class='button is-link' href='login.php'>
              <strong>Log in</strong>
            </a>
          </div>
        </div>
      </div>
    </nav>
  <?php
  } else {
  ?>
    <nav class='navbar is-dark' role='navigation' aria-label='main navigation'>
      <div class='navbar-end'>
        <div class='navbar-item'>
          <div class='buttons loginButtons'>
            <?php
            if (isset($_SESSION['gymafi_userid'])) { // no need for profile on a coach account since it tracks stats, etc. 
            ?>
              <a class='button is-primary' href='profile.php'>
                Profile
              </a>
              <?php
            } else {
              if (isset($_SESSION['gymafi_coachid'])) {


              ?>
                <a class='button is-primary' href='dashboard.php'>
                  Dashboard
                </a>
              <?php
              } else if (isset($_SESSION['gymafi_superadmin'])) {
              ?>
                <a class='button is-primary' href='admin/superadmin.php'>
                  Admin
                </a>
            <?php
              }
            }
            ?>
            <a class='button is-danger' href='logout.php'>
              Logout
            </a>
          </div>
        </div>
      </div>
      </div>
    </nav>

  <?php
  }
  ?>
  <section class='hero is-dark is-small'>
    <div class='hero-body'>
      <div class='container'>
        <h1 class='title myTitle'>
          Gymafi
        </h1>
        <h2 class='subtitle myTitle'>
          Unlocking Your Potential
        </h2>
      </div>
    </div>
  </section>

  <nav class='navbar is-dark' role='navigation' aria-label='main navigation'>

    <a role='button' class='navbar-burger' aria-label='menu' aria-expanded='false'>
      <span aria-hidden='true'></span>
      <span aria-hidden='true'></span>
      <span aria-hidden='true'></span>
    </a>
    </div>

    <div id='navbarBasicExample' class='navbar-menu has-background-dark'>
      <div class='navbar-start myNavPublic'>
        <a class='navbar-item has-text-white' href='index.php'>
          Home
        </a>

        <a class="navbar-item has-text-white" href='aboutus.php'>
          About Us
        </a>

        <a class='navbar-item  has-text-white' href='coaches.php'>
          Coaches
        </a>

        <a class="navbar-item has-text-white" href='contact.php'>
          Contact
        </a>

        <a class="navbar-item has-text-white has-background-primary" href='testimonials.php'>
          Testimonials
        </a>
      </div> <!-- end of navbarBasicExample-->


    </div>
  </nav>


  <?php
  echo "<div class='field is-grouped' id='testimonialSelectButtons'>
  ";
  $getCoachInfo = "SELECT id, name FROM webdev_coach";
  $executeGetCoachInfo = $conn->query($getCoachInfo);
  if (!$executeGetCoachInfo) {
    echo $conn->error;
  }

  while ($row = $executeGetCoachInfo->fetch_assoc()) {
    $coachID = $row['id'];
    $coachName = $row['name'];
    echo " <div class='testimonialSelectButton'> <a class='button is-dark' href='testimonials.php?coachID=$coachID'>
    <strong>$coachName</strong>
         </a>
         </div>";
  }
  echo "</div>
 
<div class='container'>
    <div id='myTestimonials'>
      <div class='tile is-ancestor'>
        <div class='tile is-vertical is-8'>
          <div class='tile'>
            <div class='tile is-parent is-vertical'>
              <article class='tile is-child notification is-primary'>
                <p class='title'>";
  echo htmlentities($testimonials[0][2], ENT_QUOTES);
  echo "</p>
                <p class='subtitle'>";
  echo htmlentities($testimonials[0][3], ENT_QUOTES);
  echo "</p>
              </article>
              <article class='tile is-child notification is-warning'>
                <p class='title'>";
  echo htmlentities($testimonials[1][2], ENT_QUOTES);
  echo "</p>
                <p class='subtitle'>";
  echo htmlentities($testimonials[1][3], ENT_QUOTES);
  echo "</p>
              </article>
            </div>
            <div class='tile is-parent'>
              <article class='tile is-child notification is-info'>
                <p class='title'>";
  echo  htmlentities($testimonials[2][2], ENT_QUOTES);
  echo "</p>
                <p class='subtitle'>";
  echo htmlentities($testimonials[2][3], ENT_QUOTES);
  echo "</p>
              
              </article>
            </div>
          </div>
          <div class='tile is-parent'>
            <article class='tile is-child notification is-danger'>
              <p class='title'>";
  echo htmlentities($testimonials[3][2], ENT_QUOTES);
  echo "</p>
              <p class='subtitle'>";
  echo htmlentities($testimonials[3][3], ENT_QUOTES);
  echo "</p>
              <div class='content'>
                <!-- Content -->
              </div>
            </article>
          </div>
        </div>
        <div class='tile is-parent is-vertical'>
          <article class='tile is-child notification is-primary'>
            <p class='title'>";
  echo htmlentities($testimonials[4][2], ENT_QUOTES);
  echo "</p>
            <p class='subtitle'>";
  echo htmlentities($testimonials[4][3], ENT_QUOTES);
  echo "</p>
          </article>
          <article class='tile is-child notification is-warning'>
            <p class='title'>";
  echo htmlentities($testimonials[5][2], ENT_QUOTES);
  echo "</p>
            <p class='subtitle'>";
  echo htmlentities($testimonials[5][3], ENT_QUOTES);
  echo "</p>
          </article>
        </div>
      </div>

    </div> <!-- end of 'myTestimonials'-->
  </div> <!-- end of container-->
";
  ?>

  <div class='myFooter ' id='testiFoot'>
    <footer class='footer has-background-dark alsoMyFooter'>
      <div class='content has-text-centered has-text-white'>
        <p>
          <span id='boldFoot'>CSC7062 Project</span> by Jordan Brown (40282125).
        </p>
      </div>
    </footer>
  </div>
</body>


</html>