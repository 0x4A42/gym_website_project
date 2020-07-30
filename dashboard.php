<?php
session_start();
include("conn.php");

if (isset($_SESSION['gymafi_userid'])) {
  $userid = $_SESSION['gymafi_userid'];



  $getUserInfo = $conn->prepare("SELECT webdev_users.username, webdev_users.email, webdev_users.date_of_birth, 
  webdev_user_details.name,  webdev_user_details.picture,  webdev_user_stats.starting_weight, webdev_user_stats.weight_current, 
   webdev_user_stats.BMI_current, webdev_user_stats.body_fat_current,  webdev_user_stats.weight_goal
   FROM webdev_users 
   INNER JOIN webdev_user_details 
   ON webdev_users.id = webdev_user_details.user_id 
   INNER JOIN webdev_user_stats
   ON webdev_users.id = webdev_user_stats.user_id
   WHERE webdev_users.id = ?");
  $getUserInfo->bind_param("i", $userid);
  $getUserInfo->execute();
  $getUserInfo->store_result();
  $getUserInfo->bind_result(
    $username,
    $userEmail,
    $dateOfBirth,
    $userActualName,
    $userPicture,
    $startingWeight,
    $currentWeight,
    $currentBMI,
    $currentBFat,
    $goalWeight

  );
  $getUserInfo->fetch();

  $age = date_diff(date_create($dateOfBirth), date_create('now'))->y;

  $weightLost = $startingWeight - $currentWeight;
  $distanceToGoal = $currentWeight - $goalWeight;



  // some validation, as pic is optional
  if ($userPicture == "" || !$userPicture) {
    $userPicture = "default_pic.png";
  }
} else if (isset($_SESSION['gymafi_coachid'])) {
  $loggedInCoachID = $_SESSION['gymafi_coachid'];


  $getCoachInfo = $conn->prepare($getCoachInfo = "SELECT name, area, image
FROM webdev_coach
WHERE id = ?");
  $getCoachInfo->bind_param("i", $loggedInCoachID);
  $getCoachInfo->execute();
  $getCoachInfo->store_result();
  $getCoachInfo->bind_result(
    $coachName,
    $coachArea,
    $coachImage
  );
  $getCoachInfo->fetch();

  // some validation, as pic is optional
  if ($coachImage == "" || !$coachImage) {
    $coachImage = "default_pic.png";
  }

  // gets number of unapproved clients to display on the coaches 'to do' list.
  $getNumberOfUnapprovedClients = "SELECT webdev_users.id, email, date_of_birth, name, phone_number FROM webdev_users 
  INNER JOIN webdev_user_details ON webdev_users.username = webdev_user_details.username
  WHERE webdev_users.approved = 0 AND webdev_user_details.coach = $loggedInCoachID
  ORDER BY webdev_users.id ASC;";
  $executeGetNumberOfUnapprovedClients = $conn->query($getNumberOfUnapprovedClients);
  $numberOfUnapprovedClients = $executeGetNumberOfUnapprovedClients->num_rows;
  // gets number of unapproved appointments to display on the coaches 'to do' list.

  $getNumberOfUnapprovedAppointments = "SELECT * FROM webdev_appointments
WHERE coach_id = $loggedInCoachID
AND confirmed = 0;";
  $executeGetNumberOfUnapprovedAppointments = $conn->query($getNumberOfUnapprovedAppointments);
  $numberOfUnapprovedAppointments = $executeGetNumberOfUnapprovedAppointments->num_rows;
} else if (isset($_SESSION['gymafi_superadmin'])) {
  header("location: admin/superadmin.php");
} else {
  header("location: login.php");
}




if (isset($_POST['editCoachDetailsSubmit'])) {
  $updatedUsername = $_POST['newUsername'];
  $sanitisedUsername = $conn->real_escape_string(trim($updatedUsername));
  $updatedName = $_POST['newName'];
  $sanitisedName = $conn->real_escape_string(trim($updatedName));
  $updatedEmail = $_POST['newEmail'];
  $sanitisedEmail = $conn->real_escape_string(trim($updatedEmail));
  $originalUsername = $_POST['originalUsername'];
  $originalUserEmail = $_POST['originalUserEmail'];
  $originalUserActualName = $_POST['originalUserActualName'];


  if (($originalUsername == $sanitisedUsername) && ($originalUserEmail == $sanitisedEmail)
    &&  ($originalUserActualName == $sanitisedName)
  ) {
    $updateFailed = "Your details have not been updated - you have not changed any fields.";
  } else if (preg_match('~[0-9]~', $sanitisedName)) {
    $updateFailed = "Your details have not been updated - name must only contain letters.";
  } else if ((strlen($sanitisedUsername) > 25)) {
    $updateFailed = "Error with changing username - out of range boundary. Must be under 25 characters.";
  } else if ((strlen($sanitisedName) > 35)) {
    $updateFailed = "Error with changing name -  out of range boundary. Must be under 35 characters.";
  } else if ((strlen($sanitisedEmail) > 35)) {
    $updateFailed = "Error with changing email - out of range boundary. Must be under 55 characters.";
  } else if (($updatedUsername == null) || ($updatedEmail == null)
    || ($updatedName == null)
  ) {
    $updateFailed = "Your details have not been updated - null fields submitted.";
  } else {
    // transaction adapted from online tutorial <https://www.youtube.com/watch?v=CNt9HPqDIVc>
    $conn->autocommit(false);

    $error = array();

    $a = $conn->query("UPDATE webdev_users 
    SET webdev_users.username = '$sanitisedUsername', webdev_users.email = '$sanitisedEmail'
    WHERE webdev_users.id = '$loggedInCoachID'");
    if ($a == false) {
      array_push($error, 'Problem updating user details');
      echo $conn->error;
    }
    $b = $conn->query("UPDATE webdev_coach SET name = '$sanitisedName'
    WHERE user_id = '$loggedInCoachID'");
    if ($b == false) {
      array_push($error, 'Problem updating user details.');
      echo $conn->error;
    }

    /**
     * If error error is not empty, problem occured and rollback happens..
     */
    if (!empty($error)) {
      $conn->rollback();
    } else {

      //commit if all ok
      $conn->commit();
      $updateSuccessful = "Your details have successfully been updated.";
    }
  }
}

/**
 * Attempts to change the password of the user.
 * Captures and sanitises the posted data.
 * If both fields have been successfully posted, checks they meet the validation checks. 
 * Will also check that the current password entered equals the one on file. 
 * If it passes all checks, submits the change to the new password.
 */
if (isset($_POST['newPasswordSubmit'])) {
  $confirmCurrent = $_POST['currentPassword'];
  $sanitisedConfirmCurrent = $conn->real_escape_string(trim($confirmCurrent));
  $updatedPassword = $_POST['newPass'];
  $sanitisedUpdatedPassword = $conn->real_escape_string(trim($updatedPassword));
  $confirmUpdatedPass = $_POST['confirmNewPass'];
  $sanitisedConfirmUpdatedPass = $conn->real_escape_string(trim($confirmUpdatedPass));
  // grab password stored in database, decrypt it
  $grabCurrentPass = $conn->prepare("SELECT AES_DECRYPT(password, '09UYO2ELHJ290OYEH22098H9ty') AS password from webdev_users 
  WHERE id = ?");
  $grabCurrentPass->bind_param("i", $loggedInCoachID);
  $grabCurrentPass->execute();
  $grabCurrentPass->store_result();
  $grabCurrentPass->bind_result($currentPass);
  $grabCurrentPass->fetch();


  /**
   * Determines password has been changed. If all the same, does not send query and outputs error message.
   * If new password is different from old but if confirmed password is not the same as initial new password, 
   * displays error and no query is sent.
   * Else, if all is okay it sends the query and a success message is sent.
   *  
   * Regex for password check from online guide
   * https://stackoverflow.com/questions/42467243/regex-strong-password-the-special-characters
   *
   */
  if ($updatedPassword != $confirmUpdatedPass) {
    $updateFailed = "Your password has not been changed - confirm different from new password.";
  } else if (($updatedPassword ==  $currentPass) && ($confirmUpdatedPass == $currentPass)) {
    $updateFailed = "Your password has not been changed - same as current.";
  } else if ($sanitisedConfirmCurrent !=  $currentPass) {
    $updateFailed = "Your password has not been changed - current password incorrect.";
  } else if (!preg_match("/\d/", $confirmUpdatedPass)) {
    $updateFailed = "Your password has not been changed -  password should contain one digit at least.";
  }
  if (!preg_match("/[A-Z]/", $confirmUpdatedPass)) {
    $updateFailed = "Your password has not been changed -  password should contain at least one uppercase Letter";
  }
  if (!preg_match("/[a-z]/", $confirmUpdatedPass)) {
    $updateFailed = "Your password has not been changed -  password must contain at least one lowercase letter.";
  }
  if (!preg_match("/\W/", $confirmUpdatedPass)) {
    $updateFailed = "Your password has not been changed -  password should contain at least one special character.";
  } else {

    $changePassword = "UPDATE webdev_users
  SET webdev_users.password = AES_ENCRYPT('$sanitisedConfirmUpdatedPass', '09UYO2ELHJ290OYEH22098H9ty')
  WHERE webdev_users.id = '$loggedInCoachID'";
    $executeChangePass = $conn->query($changePassword);

    if (!$executeChangePass) {
      echo $conn->error;
    }
    $updateSuccessful = "Your password has been changed.";
  }
}
?>


<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gymafi | Dashboard</title>
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
        <div class='buttons'>
          <?php

          if (isset($userid)) { // no need for profile on a coach account since it tracks stats, etc. 
          ?>
            <a class='button is-primary' href='profile.php'>
              Profile
            </a>
          <?php
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
  if (isset($userid)) {
    echo " <section class='hero is-dark is-small'>
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
  <nav class='navbar is-dark' role='navigation' aria-label='main navigation'>";
  } else if (isset($loggedInCoachID)) {
    echo " <section class='hero is-small'>
    <div class='hero-body has-background-info'>
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
  <nav class='navbar is-info' role='navigation' aria-label='main navigation'>";
  }
  ?>







  <a role='button' class='navbar-burger' aria-label='menu' aria-expanded='false'>
    <span aria-hidden='true'></span>
    <span aria-hidden='true'></span>
    <span aria-hidden='true'></span>
  </a>
  </div>
  <?php

  /**
   * Navbar for client
   */
  if (isset($userid)) {

  ?>

    <div id='navbarBasicExample' class='navbar-menu has-background-dark'>
      <div class='navbar-start myNav'>
        <a class='navbar-item has-text-white has-background-primary' href='dashboard.php'>
          Dashboard
        </a>


        <a class='navbar-item has-background-dark has-text-white' href='appointments.php'>
          Appointments
        </a>

        <a class='navbar-item has-background-dark has-text-white' href='inbox.php'>
          Inbox
        </a>

        <a class='navbar-item has-background-dark has-text-white' href='performance.php'>
          Performance Log
        </a>
        <a class='navbar-item has-text-white' href='gallery.php'>
          Your Gallery
        </a>

      </div> <!-- end of navbar dropdown-->
    </div> <!-- end of nav-bar item-->
    </div> <!-- end of navbarBasicExample-->


    </div>
    </nav>
  <?php
    /**
     * Navbar for coach
     */
  } else if (isset($_SESSION['gymafi_coachid'])) {
  ?>

    <div id='navbarBasicExample' class='navbar-menu has-background-info'>
      <div class='navbar-start myNav'>
        <a class='navbar-item has-text-black  has-background-warning' href='dashboard.php'>
          Dashboard
        </a>


        <a class='navbar-item has-text-black' href='admin/approvals.php'>
          Approvals
        </a>

        <a class='navbar-item has-text-black' href='appointments.php'>
          Appointments
        </a>


        <a class='navbar-item has-text-black ' href='admin/groups.php'>
          Groups
        </a>

        <a class='navbar-item has-text-black' href='inbox.php'>
          Inbox
        </a>

        <div class='navbar-item has-dropdown is-hoverable'>
          <a class='navbar-link has-text-black'>
            More
          </a>

          <div class='navbar-dropdown has-background-info has-text-black'>
            <a class='navbar-item has-text-black' href='admin/manageclients.php'>
              Edit Client Info
            </a>

            <a class='navbar-item has-text-black' href='admin/editcontent.php'>
              Edit Site Content
            </a>
          </div>



        </div> <!-- end of navbar dropdown-->
      </div> <!-- end of nav-bar item-->
    </div> <!-- end of navbarBasicExample-->


    </div>
    </nav>
  <?php
  }
  ?>

  <!-- Displays a little box of the user's info-->
  <div id='dashColumns'>
    <div class='columns'>
      <div class='column is-3'>
        <article class='message is-dark'>

          <div class='message-header'>

            <?php
            if (isset($userid)) {
              echo "
       <p id='userPic'> <img src='images/uploaded/$userPicture' class='dashProfilePic' > </p>";
            } else if (isset($loggedInCoachID)) {
              if (isset($coachImage)) {
                echo "
              <p id='userPic'> <img src='images/uploaded/$coachImage' class='dashProfilePic'> </p>";
              }
            }


            echo "
       </div>
      
       <div class='message-body'>
       ";
            if (isset($userid)) {


              echo "
          <p class='dashProfileDetails'><strong>", htmlentities($userActualName, ENT_QUOTES), "</strong></p>
          <p class='dashProfileDetails'><strong>Age: ", htmlentities($age, ENT_QUOTES), "</strong></p>
          ";

              $getDietPlan = $conn->prepare("SELECT diet_plan, meal_type
          FROM  webdev_training_regime
          INNER JOIN webdev_training_meals 
          ON webdev_training_regime.diet_plan = webdev_training_meals.id 
          WHERE user_id = ?");
              $getDietPlan->bind_param("i", $userid);
              $getDietPlan->execute();
              $getDietPlan->store_result();
              $getDietPlan->bind_result(
                $dietPlan,
                $mealType

              );
              $getDietPlan->fetch();

              echo "   <p class='dashProfileDetails'><strong>Current diet plan: $mealType</strong></p>

  
          <p class='dashProfileDetails'><strong>BMI: ", htmlentities($currentBMI, ENT_QUOTES), "</strong></p>
          <p class='dashProfileDetails'><strong>Body Fat: ", htmlentities($currentBFat, ENT_QUOTES), " (%)</strong></p>";
            ?>

              <div id='dashButtons'> <a class='button is-info' href='appointments.php'>
                  Make Appointment
                </a>

                <a class='button is-info' href='inbox.php'>
                  Inbox
                </a>

                <a class='button is-info' href='profile.php'>
                  Edit Profile
                </a>

              </div>


            <?php
            } else if (isset($loggedInCoachID)) {
              $getNumberOfClients = "SELECT * FROM webdev_user_details WHERE coach = $loggedInCoachID";
              $executeGetNumberOfClients = $conn->query($getNumberOfClients);
              $numberOfClients = $executeGetNumberOfClients->num_rows;

              $getNumberOfGroups = "SELECT * FROM webdev_groups WHERE coach = $loggedInCoachID";
              $executeGetNumberOfGroups  = $conn->query($getNumberOfGroups);
              $numberOfGroups = $executeGetNumberOfGroups->num_rows;
              //print coach stuff here 
              echo "
    <p class='dashProfileDetails'><strong>", htmlentities($coachName, ENT_QUOTES), "</strong></p>
    <p class='dashProfileDetails'><strong>Area: ", htmlentities($coachArea, ENT_QUOTES), "</strong></p>
    <p class='dashProfileDetails'><strong>Number of clients:", htmlentities($numberOfClients, ENT_QUOTES), " </strong></p>
    <p class='dashProfileDetails'><strong>Number of groups: ", htmlentities($numberOfGroups, ENT_QUOTES), "</strong></p>
    ";
            ?>
              <form action='dashboard.php' method='POST'>
                <div id='dashButtons'> <a class='button is-info' href='admin/approvals.php'>
                    Manage Pending
                  </a>

                  <a class='button is-info' href='inbox.php'>
                    Inbox
                  </a>


                  <a class='button is-info' id='editCoachDetails'>
                    Edit Details
                  </a>

              </form>

              <?php
              if (isset($updateFailed)) {
                echo "<p class='displayError'> $updateFailed</p>";
              } else if (isset($updateSuccessful)) {
                echo "<p class='displaySucc'> $updateSuccessful</p>";
              }
              ?>
          </div>
        <?php
            }
        ?>

      </div> <!-- end of message body-->
      </article>
    </div><!-- end of column-->


    <!-- Displays the user's upcoming appointments -->

    <div class='column is-7' id='rightColumns'>
      <article class='message is-dark'>
        <div class='message-header'>
          <p class='profileBadgeText'>
            <h1 class='title dashTitleText'>Upcoming Session(s)</h1>
          </p>
        </div>
        <div class='message-body'>
          <div class='sessionTitle'>Individual sessions: </div>
          <?php
          if (isset($userid)) {


            $grabConfirmedAppointsments = "SELECT * FROM webdev_appointments 
        WHERE user_id = '$userid' 
        AND confirmed = '1'
        ORDER BY date ASC;";
          } else if (isset($loggedInCoachID)) {
            $grabConfirmedAppointsments = "SELECT date, time, details, duration, name FROM webdev_appointments 
        INNER JOIN webdev_user_details
        ON webdev_appointments.user_id = webdev_user_details.user_id
        WHERE coach_id = $loggedInCoachID
        AND confirmed = '1'
        ORDER BY date ASC";
          }
          $executeGrabConfirmedAppointsments = $conn->query($grabConfirmedAppointsments);

          if (!$executeGrabConfirmedAppointsments) {
            echo $conn->error;
          }
          $numOfAppts = $executeGrabConfirmedAppointsments->num_rows;
          $apptCount = 0;
          if ($numOfAppts > 0) {
            echo " <div class='tile'>";

            while (($row = $executeGrabConfirmedAppointsments->fetch_assoc()) && ($apptCount < 4)) {
              $date = $row['date'];
              $dateText = date("M jS", strtotime($date));
              $time = $row['time'];
              $details = $row['details'];
              $duration = $row['duration'];
              if (isset($loggedInCoachID)) {
                $clientName = $row['name'];
              }
              echo "<p class='profileBadges'><article class='tile is-child notification is-info' >
          <p class='title profileBadgeText'>", htmlentities($dateText, ENT_QUOTES), htmlentities($time, ENT_QUOTES), " <br>
          (", htmlentities($duration, ENT_QUOTES), ")</p>";
              if (isset($loggedInCoachID)) {
                echo "<p class='subtitle profileBadgeText'>", htmlentities($details, ENT_QUOTES), " (with ", htmlentities($clientName, ENT_QUOTES), ") </p>";
              } else {
                echo "<p class='subtitle profileBadgeText'>", htmlentities($details, ENT_QUOTES), "</p>";
              }
              echo "
        </article>
        </p>";
              $apptCount += 1;
            }
            echo "</div>";
          } else {
            echo "<div> You have no confirmed upcoming sessions.</div>";
          }

          echo "
        <div class='sessionTitle'>Group sessions: </div>";
          if (isset($userid)) {


            $getGroupAppointments = "SELECT date, time, group_id, duration, details, group_name
          FROM webdev_appointments 
          INNER JOIN webdev_groups
          ON webdev_appointments.group_id = webdev_groups.id
          WHERE webdev_groups.member_one = $userid OR webdev_groups.member_two = $userid
          OR webdev_groups.member_three = $userid OR webdev_groups.member_four = $userid 
          AND confirmed = '1'
          ORDER BY date ASC
          ;";
          } else if (isset($loggedInCoachID)) {
            $getGroupAppointments = "SELECT date, time, group_id, duration, details, group_name
          FROM webdev_appointments 
          INNER JOIN webdev_groups
          ON webdev_appointments.group_id = webdev_groups.id
          WHERE webdev_groups.coach = $loggedInCoachID
          AND confirmed = '1'
          ORDER BY date ASC
          ;";
          }
          $executeGetGroupAppointments = $conn->query($getGroupAppointments);

          if (!$executeGetGroupAppointments) {
            echo $conn->error;
          }
          $numOfGrpAppts = $executeGetGroupAppointments->num_rows;
          $apptCountGrp = 0;
          if ($numOfGrpAppts > 0) {
            echo " <div class='tile'>";

            while (($row = $executeGetGroupAppointments->fetch_assoc()) && ($apptCountGrp < 4)) {
              $dateGroup = $row['date'];
              $dateTextGroup = date("M jS", strtotime($dateGroup));
              $timeGroup = $row['time'];
              $detailsGroup = $row['details'];
              $durationGroup = $row['duration'];
              $groupNameGroup = $row['group_name'];
              echo "<p class='profileBadges'><article class='tile is-child notification is-warning' >
          <p class='title profileBadgeText'>$dateTextGroup <br>($durationGroup)</p>
          <p class='subtitle profileBadgeText'>$detailsGroup ($groupNameGroup)</p>
        </article>
        </p>";
              $apptCountGrp += 1;
            }
            echo "</div>";
          } else {
            echo "<div> You have no confirmed upcoming group sessions.</div>";
          }
          echo "
       </div> <!-- end of message body-->
     </article>";

          if (isset($userid)) {


          ?>
            <!-- Displays the user's current training programme -->
            <div class='columns'>
              <div class='column'>
                <article class='message is-dark'>
                  <div class='message-header'>
                    <p class='profileBadgeText'>
                      <h1 class='title dashTitleText'>Individual Training Programme</h1>
                    </p>

                  </div>
                  <div class='message-body'>


                    <div class='tile'>

                    <?php

                    $getTrainingSession = "SELECT monday, plan 
                    FROM  webdev_training_plans
                    INNER JOIN webdev_training_regime
                    ON webdev_training_plans.id = webdev_training_regime.monday
                    WHERE user_id = $userid";
                    $executeGetTrainingSession = $conn->query($getTrainingSession);
                    if (!$executeGetTrainingSession) {
                      echo $conn->error;
                    }

                    while ($row = $executeGetTrainingSession->fetch_assoc()) {
                      $monday = $row['plan'];

                      echo "
                <p class='profileBadges'><article class='tile is-child notification is-danger' >
                <p class='title profileBadgeText'>Mon</p>
                <p class='subtitle profileBadgeText'>$monday</p>
                </article>
                </p>";
                    }

                    $getTrainingSession = "SELECT tuesday, plan 
                  FROM  webdev_training_plans
                  INNER JOIN webdev_training_regime
                  ON webdev_training_plans.id = webdev_training_regime.tuesday
                  WHERE user_id = $userid";

                    $executeGetTrainingSession = $conn->query($getTrainingSession);
                    if (!$executeGetTrainingSession) {
                      echo $conn->error;
                    }

                    while ($row = $executeGetTrainingSession->fetch_assoc()) {
                      $tuesday = $row['plan'];

                      echo "<p class='profileBadges'><article class='tile is-child notification is-link' >
                    <p class='title profileBadgeText'>Tues</p>
                    <p class='subtitle profileBadgeText'>$tuesday</p>
                    </article>
                    </p>";
                    }

                    $getTrainingSession = "SELECT wednesday, plan 
                  FROM  webdev_training_plans
                  INNER JOIN webdev_training_regime
                  ON webdev_training_plans.id = webdev_training_regime.wednesday
                  WHERE user_id = $userid";
                    $executeGetTrainingSession = $conn->query($getTrainingSession);
                    if (!$executeGetTrainingSession) {
                      echo $conn->error;
                    }

                    while ($row = $executeGetTrainingSession->fetch_assoc()) {
                      $wednesday = $row['plan'];

                      echo "<p class='profileBadges'><article class='tile is-child notification is-warning' >
                  <p class='title profileBadgeText'>Wed</p>
                  <p class='subtitle profileBadgeText'>$wednesday</p>
                  </article>
                  </p>";
                    }

                    $getTrainingSession = "SELECT thursday, plan 
                  FROM  webdev_training_plans
                  INNER JOIN webdev_training_regime
                  ON webdev_training_plans.id = webdev_training_regime.thursday
                  WHERE user_id = $userid";

                    $executeGetTrainingSession = $conn->query($getTrainingSession);
                    if (!$executeGetTrainingSession) {
                      echo $conn->error;
                    }

                    while ($row = $executeGetTrainingSession->fetch_assoc()) {
                      $thursday = $row['plan'];

                      echo "<p class='profileBadges'><article class='tile is-child notification is-primary' >
                    <p class='title profileBadgeText'>Thur</p>
                    <p class='subtitle profileBadgeText'>$thursday</p>
                    </article>
                    </p>";
                    }

                    $getTrainingSession = "SELECT friday, plan 
                  FROM  webdev_training_plans
                  INNER JOIN webdev_training_regime
                  ON webdev_training_plans.id = webdev_training_regime.friday
                  WHERE user_id = $userid";

                    $executeGetTrainingSession = $conn->query($getTrainingSession);
                    if (!$executeGetTrainingSession) {
                      echo $conn->error;
                    }

                    while ($row = $executeGetTrainingSession->fetch_assoc()) {
                      $friday = $row['plan'];

                      echo "<p class='profileBadges'><article class='tile is-child notification is-danger' >
                    <p class='title profileBadgeText'>Fri</p>
                    <p class='subtitle profileBadgeText'>$friday</p>
                    </article>
                    </p>";
                    }

                    $getTrainingSession = "SELECT saturday, plan 
                  FROM  webdev_training_plans
                  INNER JOIN webdev_training_regime
                  ON webdev_training_plans.id = webdev_training_regime.saturday
                  WHERE user_id = $userid";

                    $executeGetTrainingSession = $conn->query($getTrainingSession);
                    if (!$executeGetTrainingSession) {
                      echo $conn->error;
                    }

                    while ($row = $executeGetTrainingSession->fetch_assoc()) {
                      $saturday = $row['plan'];

                      echo "<p class='profileBadges'><article class='tile is-child notification is-link' >
                    <p class='title profileBadgeText'>Sat</p>
                    <p class='subtitle profileBadgeText'>$saturday</p>
                    </article>
                    </p>";
                    }

                    $getTrainingSession = "SELECT sunday, plan 
                  FROM  webdev_training_plans
                  INNER JOIN webdev_training_regime
                  ON webdev_training_plans.id = webdev_training_regime.tuesday
                  WHERE user_id = $userid";

                    $executeGetTrainingSession = $conn->query($getTrainingSession);
                    if (!$executeGetTrainingSession) {
                      echo $conn->error;
                    }

                    while ($row = $executeGetTrainingSession->fetch_assoc()) {
                      $sunday = $row['plan'];

                      echo "<p class='profileBadges'><article class='tile is-child notification is-warning' >
                    <p class='title profileBadgeText'>Sun</p>
                    <p class='subtitle profileBadgeText'>$sunday</p>
                    </article>
                    </p>";
                    }
                    echo "
                </div>
                </div> <!-- end of message body-->";
                  }


                  echo "
                </article>
                <div class='columns'>
                <div class='column'>
                <article class='message is-dark'>
                <div class='message-header'>";
                  if (isset($userid)) {
                    echo "
                 <p class='profileBadgeText'><h1 class='title dashTitleText'> Stats</h1></p>";
                  } else if (isset($loggedInCoachID)) {
                    echo "
                 <p class='profileBadgeText'><h1 class='title dashTitleText'>To Do:</h1></p>";
                  }
                  echo "
                </div>
                <div class='message-body'>
                <div class='tile'>
                <!-- The magical tile element! -->
                <p class='profileBadges'><article class='tile is-child notification is-primary' >
                ";
                  if (isset($userid)) {
                    echo "
                <p class='title profileBadgeText'>Total weight lost: </p>
                <p class='subtitle profileBadgeText'>$weightLost kg</p>";
                  } else if (isset($loggedInCoachID)) {
                    echo "<p class='title profileBadgeText'>Pending clients: </p>
                <p class='subtitle profileBadgeText'>$numberOfUnapprovedClients</p>";
                  }
                  echo "
                </article>
                </p>
                <p class='profileBadges'>
                <article class='tile is-child notification is-warning profileBadges'>";
                  if (isset($userid)) {
                    echo "
                  <p class='title profileBadgeText'>Distance to goal: </p>
                  <p class='subtitle profileBadgeText'> $distanceToGoal kg</p>";
                  } else if (isset($loggedInCoachID)) {
                    echo "<p class='title profileBadgeText'>Pending appointments: </p>
                 <p class='subtitle profileBadgeText'>$numberOfUnapprovedAppointments</p>";
                  }
                  echo "
                </article>
                </p>

                <p class='profileBadges'>
                <article class='tile is-child notification is-link profileBadges'>
                ";
                  if (isset($userid)) {
                    $determineUnreadMessages = "SELECT * FROM webdev_inbox WHERE recipient = '$userid' AND hide = 0";
                  } else if (isset($loggedInCoachID)) {
                    $determineUnreadMessages = "SELECT * FROM webdev_inbox WHERE recipient = '$loggedInCoachID' AND hide = 0";
                  }

                  $executeDetermineUnreadMessages = $conn->query($determineUnreadMessages);
                  if (!$executeDetermineUnreadMessages) {
                    echo $conn->error;
                  }

                  $num = $executeDetermineUnreadMessages->num_rows;
                  echo "
                  <p class='title profileBadgeText'>Unread messages:</p>
                  <p class='subtitle profileBadgeText'>$num</p>
                  ";
                    ?>
                </article>
                </p>
              </div>
            </div> <!-- end of message body-->

      </article>
    </div><!-- end of columns-->
  </div><!-- end of columns-->


  </div>
  </div>
  </div>
  </div>
  </div>

  <?php

  /**
   * Gets all of the details of the user who is currently logged in,
   * to display on their profile page as well as to fill the values of the edit profile modals.
   */
  $selectUser = $conn->prepare("SELECT webdev_users.username, webdev_users.email, webdev_coach.name
FROM webdev_users
INNER JOIN webdev_coach
ON webdev_users.id = webdev_coach.user_id 
WHERE webdev_users.id  = ?");
  $selectUser->bind_param("i", $loggedInCoachID);
  $selectUser->execute();
  $selectUser->store_result();
  $selectUser->bind_result(
    $username,
    $userEmail,
    $userActualName
  );
  $selectUser->fetch();

  ?>
  <!-- modal for changing personal details-->

  <div class='modal' id='editCoachDetailsModal'>
    <div class='modal-background '></div>
    <div class='modal-card'>
      <header class='modal-card-head'>
        <p class='modal-card-title'>Update Your Details</p>
        <button class='delete cancelUpdate' aria-label='close'></button>

      </header>

      <section class='modal-card-body'>
        <div class='field is-grouped is-grouped-multiline' id='modalSelectionButtons'>

          <p class='control'>
            <button class='button'>
              Personal Details   <span class='icon is-small'>
                <i class='fas fa-user-shield'></i>
              </span>
            </button>
          </p>


          <p class='control'>
            <button class='button updatePassword'>
              Password  <span class='icon is-small'>
                <i class='fas fa-key'></i>
              </span>
            </button>
          </p>

        </div>

        <form action='dashboard.php' method='POST' id='changeCoachDetailsForm'>
          <div class='field'>
            <label class='label'>Username</label>
            <div class='control'>
              <input class='input' id='usernameChange' type='text' value="<?php echo $username ?>" name='newUsername'>
            </div>
            <p class='help is-danger' id='usernameChangeWarn'> </p>
          </div>

          <?php
          echo "
      <input type='hidden' name='originalUsername' value='$username'>
      <input type='hidden' name='originalUserEmail' value='$userEmail'>
      <input type='hidden' name='originalUserActualName' value='$userActualName'>";
          ?>
          <div class='field'>
            <label class='label'>Your Name: </label>
            <div class='control'>
              <input class='input' id='realNameChange' type='text' value="<?php echo $userActualName ?>" name='newName'>
            </div>
            <p class='help is-danger' id='realNameChangeWarn'> </p>
          </div>


          <div class='field'>
            <label class='label'>Email address: </label>
            <div class='control'>
              <input class='input' type='email' id='emailChange' value="<?php echo $userEmail ?>" name='newEmail'>
            </div>
            <p class='help is-danger' id='emailChangeWarn'> </p>
          </div>



          <footer class='modal-card-foot'>
            <p class='control'>
              <input type='submit' class='button is-success editCoachDetailsIfValid' id='editCoachDetails' value='Save changes' name='editCoachDetailsSubmit'>
              <button class='button cancelUpdate'>Cancel</button>
          </footer>
        </form>
      </section>

    </div>
  </div>


  <div class='modal ' id='editPassword'>
    <div class='modal-background '></div>
    <div class='modal-card'>
      <header class='modal-card-head'>
        <p class='modal-card-title'>Change Password</p>

        <button class='delete cancelUpdate' aria-label='close'></button>

      </header>

      <section class='modal-card-body'>
        <div class='field is-grouped is-grouped-multiline' id='modalSelectionButtons'>
          <p class='control'>
            <button class='button updatePersonalDetailsButton'>
              Personal Details   <span class='icon is-small'>
                <i class='fas fa-user-shield'></i>
              </span>
            </button>
          </p>

          <p class='control'>
            <button class='button'>
              Password  <span class='icon is-small'>
                <i class='fas fa-key'></i>
              </span>
            </button>
          </p>

        </div>

        <form action='dashboard.php' method='POST' id='changePassForm'>
          <div class='field'>
            <label class='label'>Current password: </label>
            <div class='control'>
              <input class='input' type='password' id='currentPass' name='currentPassword'>
            </div>
            <p class='help is-danger' id='currentPassWarn'> </p>
          </div>

          <div class='field'>

            <label class='label'>New password: </label>

            <p>Password should be between 8-16 characters and contain one of the following: an uppercase and lowercase letter, a number and a special character.</p>
            <div class='control'>
              <input class='input' id='newPass' type='password' name='newPass'>
            </div>
            <p class='help is-danger' id='newPassWarn'> </p>
          </div>

          <div class='field'>
            <label class='label'>Confirm new password: </label>
            <div class='control'>
              <input class='input' id='confirmNewPass' type='password' name='confirmNewPass'>
            </div>
            <p class='help is-danger' id='confirmNewPassWarn'> </p>
          </div>





          <footer class='modal-card-foot'>
            <p class='control'>
              <input type='submit' class='button is-success passwordIfValid' id='newPasswordSubmit' value='Save changes' name='newPasswordSubmit'>
              <button class='button cancelUpdate'>Cancel</button>
          </footer>
        </form>
      </section>

    </div>
  </div>

  </div><!-- end of package info-->


  <!-- page footer-->

  <div class="myFooter">
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