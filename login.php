<?php
session_start();
include("conn.php");

/**
 * Checks if a user is already logged in, if so kicks them to the homepage.
 * If not logged in, and data has been posted from the submit button, attempts to validate their login. 
 * If valid, logs them in, otherwise displays error message. 
 */
if ((isset($_SESSION['gymafi_userid'])) ||  (isset($_SESSION['gymafi_coachid']))) {
  header("location: dashboard.php"); // kicks user out if session exists
} else if (isset($_SESSION['gymafi_superadmin'])) {
  header("location: admin/superadmin.php");
}

/**
 * If the user enters a username/email and password, attempts to validate their login
 */
if (isset($_POST['requestlogin'])) {


  $sanitisedUser = $conn->real_escape_string(trim($_POST['name']));
  $sanitisedPass = $conn->real_escape_string(trim($_POST['pass']));

  // checks user details in database, decrypts password and compared with string input. 
  $checkuser = "SELECT * FROM webdev_users 
  WHERE AES_DECRYPT(password, '09UYO2ELHJ290OYEH22098H9ty') = '$sanitisedPass' 
  AND username = '$sanitisedUser' 
  OR email = '$sanitisedUser' ";


  $result = $conn->query($checkuser);

  if (!$result) {
    echo $conn->error;
  }

  $num = $result->num_rows;
  // if returns any results, generates a session and logs them in. 
  if ($num > 0) {

    while ($row = $result->fetch_assoc()) {
      $userid = $row['id'];
      $userRole = $row['role'];
      // if account logged in is coach, finds their coach id and sets the variable to be it. 
      if ($userRole == 1) {
        $_SESSION['gymafi_superadmin'] = $userid;
        // if successfully logged in, put back to dashboard.
        header("location: admin/superadmin.php");
      }
      if ($userRole == 2) {
        $getCoachID = "SELECT webdev_coach.id FROM webdev_coach 
        WHERE webdev_coach.user_id = $userid";
        $coachResult = $conn->query($getCoachID);
        if (!$coachResult) {
          echo $conn->error;
        }
        while ($coachRow = $coachResult->fetch_assoc()) {
          $coachID = $coachRow['id'];
          $_SESSION['gymafi_coachid'] = $coachID;
        }
        // if successfully logged in, put back to dashboard.
        header("location: dashboard.php");
        // elseif normal user, just sets their user id 
      } elseif ($userRole == 3) {
        $_SESSION['gymafi_userid'] = $userid;
        // if successfully logged in, put back to dashboard.
        header("location: dashboard.php");
      }
    }
  } else {
    // if incorrect details, string to be printed below log in buttons.
    $usererror = "Username or password incorrect.";
  }
} //closing of posted data


?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gymafi | Login</title>
  <link href="styles/bulma.css" rel="stylesheet">
  <link href="styles/gui.css" rel="stylesheet">
  <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
  <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
  <script src="script/myScript.js"></script>


</head>

<body class="has-background-grey-lighter">


  <nav class='navbar is-dark' role='navigation' aria-label='main navigation'>
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



  <section class="hero is-dark is-small">
    <div class="hero-body">
      <div class="container">
        <h1 class="title myTitle">
          Gymafi
        </h1>
        <h2 class="subtitle myTitle">
          Unlocking Your Potential
        </h2>
      </div>
    </div>
  </section>

  <nav class="navbar is-dark" role="navigation" aria-label="main navigation">


    <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false">
      <span aria-hidden="true"></span>
      <span aria-hidden="true"></span>
      <span aria-hidden="true"></span>
    </a>
    </div>

    <div id="navbarBasicExample" class="navbar-menu has-background-dark">
      <div class="navbar-start myNavPublic">
        <a class="navbar-item has-text-white" href='index.php'>
          Home
        </a>

        <a class="navbar-item has-text-white" href='aboutus.php'>
          About Us
        </a>

        <a class="navbar-item has-text-white" href='coaches.php'>
          Coaches
        </a>

        <a class="navbar-item has-text-white" href='contact.php'>
          Contact
        </a>

        <a class="navbar-item has-text-white" href='testimonials.php'>
          Testimonials
        </a>
      </div> <!-- end of navbarBasicExample-->


    </div>
  </nav>

  <?php
  $getContentOfPage = $conn->prepare("SELECT webdev_page_content.title,  webdev_page_content.content, 
  webdev_page_content.content_2
  FROM webdev_page_content
  WHERE webdev_page_content.id = 15");
  $getContentOfPage->execute();
  $getContentOfPage->store_result();
  $getContentOfPage->bind_result($contentTitle, $contentOne, $contentTwo);
  $getContentOfPage->fetch();
  echo "

  <div id='signUpTitle'>
    <h2 class='title'>$contentTitle</h2>
    <h3 class='subtitle is-6'>
      <p>$contentOne</p>
      <p>$contentTwo</p>
    </h3>

  </div>
  <!--end of loginTitle -->";

  ?>

  <form action='login.php' method='POST' id='loginForm'>
    <div id="outerContain">
      <div id="loginBorder">
        <div id="innerContain ">


          <div class="columns">

            <div class="column loginCol">
              <div class="field loginCol">
                <p class="control has-icons-left loginFormPart">
                  <input class="input" type="text" id="usernameOrEmailLogIn" placeholder="Username or email" name="name">
                  <span class="icon is-small is-left">
                    <i class="fas fa-user"></i>
                  </span>

                </p>
              </div>

              </p>
            </div> <!-- end of column-->

          </div> <!-- end of columns-->
          <p class="help is-danger loginWarn" id="usernameOrEmailLogInWarn"> </p>

          <div class="columns">

            <div class="column">
              <div class="field loginCol">
                <p class="control has-icons-left loginFormPart">
                  <input class="input" type="password" id="passwordLogin" placeholder="Password" name="pass">
                  <span class="icon is-small is-left">
                    <i class="fas fa-lock"></i>
                  </span>

              </div>

              </p>

            </div> <!-- end of column-->

          </div> <!-- end of columns-->
          <p class="help is-danger loginWarn" id="passwordLoginWarn">
          </p>
        </div> <!-- end of field-->

        <div class="columns">
          <div class="column"> </div>
          <div class="column"> </div>
          <div class="column"> </div>
          <div class="column">
            <div class="field">


              <!-- if clicked, brings user to process for resetting password page-->
              <a class='button is-danger formButtonsLogin' href='resetpassword.php'>
                Forgot Password
              </a>



            </div> <!-- end of field-->
          </div> <!-- end of column-->




          <div class="column">
            <div class="field">

              <!-- if clicked, posts info and attempts to validate login through database-->
              <input type='submit' class='button is-primary formButtonsLogin loginButtonIfValid' id='loginRequest' value='Login' name='requestlogin'>



            </div> <!-- end of field-->
          </div> <!-- end of column-->


          <div class="column"> </div>
          <div class="column"> </div>

        </div> <!-- end of columns-->
  </form>

  <!-- if wrong login details, displays error-->
  <?php
  if (isset($usererror)) {
    echo "<p class='regError'>$usererror</p>";
  }

  ?>




  </div>
  </div> <!-- end of innerContainer-->
  </div> <!-- end of outerContainer-->


  <!-- page footer-->
  <div class="myFooter" id="myFooterLog">
    <footer class="footer has-background-dark alsoMyFooter">
      <div class="content has-text-centered has-text-white">
        <p>
          <span id="boldFoot">CSC7062 Project</span> by Jordan Brown (40282125).
        </p>
      </div>
    </footer>
  </div>
</body>


</html>