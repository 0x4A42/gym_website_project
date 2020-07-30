<?php
session_start();
include("../conn.php");

/*
* Ensures user logged in is an admin/coach, 
* if an ordinary user is logged in it kicks them to the dashboard, 
* otherwise kicks a non-logged in user to the login page
*/
if (!isset($_SESSION['gymafi_coachid'])) {

  if (isset($_SESSION['gymafi_userid'])) {
    header("location: ../dashboard.php");
  } else if (isset($_SESSION['gymafi_superadmin'])) {
    header("location: superadmin.php");
  } else {
    header("location: ../login.php");
  }
}

$coach_id = $_SESSION['gymafi_coachid']; // store coach's id from session in local variable

/**if a user is approved, sets the boolean value to 1 in the database
 *and sends them a message in the internal messaging system.
 */
if (isset($_POST['approveSignup'])) {
  $userToApprove = $_POST['userID'];
  $sanitisedUserToApprove = $conn->real_escape_string(trim($userToApprove));
  $findPersonToApprove = "SELECT webdev_users.username FROM webdev_users 
  INNER JOIN webdev_user_details 
  ON webdev_users.username = webdev_user_details.username
  WHERE webdev_users.id = $sanitisedUserToApprove";
  $executeFindPerson = $conn->query($findPersonToApprove);

  while ($row = $executeFindPerson->fetch_assoc()) {
    $userToUpdate = $row['username'];

    $approveUser = "UPDATE webdev_users
    SET approved = 1
    WHERE id = $sanitisedUserToApprove; ";

    $addIDToDetails = "UPDATE webdev_user_details
    SET user_id = $sanitisedUserToApprove
    WHERE username = '$userToUpdate'";

    // sets default values for approved users stats so they don't get a bunch of errors on the profile page.
    $setDefaultStats = "INSERT INTO webdev_user_stats (user_id, height, starting_weight, weight_current, 
  weight_goal, BMI_current, BMI_goal, body_fat_current, body_fat_goal)
  VALUES ($sanitisedUserToApprove, 0, 0, 0, 0, 0, 0, 0, 0)";

    // sets default values for approved users' trainig regime
    $setDefaultRegime = "INSERT INTO webdev_training_regime(user_id, diet_plan, monday, tuesday, wednesday, thursday, friday, saturday,sunday)
  VALUES ($sanitisedUserToApprove, 5, 11, 11, 11, 11, 11, 11, 11);";

    // send user a message on the internal messaging system
    $sendMessage = "INSERT INTO webdev_inbox (recipient, sender, subject, message, attachment, coach, user, hide) VALUES
  ('$sanitisedUserToApprove', '$coach_id', 'Welcome to Gymafi', 'Welcome - your account has been approved for Gymafi and 
  I have accepted you as a client. Please set up your initial details by visiting your profile. 
  Hope to see you soon!', 'beginners_help.pdf', '$coach_id', '$sanitisedUserToApprove', 0)";

    // transaction adapted from online tutorial https://www.youtube.com/watch?v=CNt9HPqDIVc
    $conn->autocommit(false);

    $error = array();

    $a = $conn->query($approveUser);
    if ($a == false) {
      array_push($error, 'Problem approving user.');
      echo $conn->error;
    }
    $b = $conn->query($addIDToDetails);
    if ($b == false) {
      array_push($error, 'Problem adding user ID.');
      echo $conn->error;
    }
    $c =  $conn->query($setDefaultStats);
    if ($c == false) {
      array_push($error, 'Problem setting default stats of user.');
      echo $conn->error;
    }
    $d = $conn->query($setDefaultRegime);
    if ($d == false) {
      array_push($error, 'Problem setting default regime.');
      echo $conn->error;
    }
    $e = $conn->query($sendMessage);
    if ($e == false) {
      array_push($error, 'Problem sending user a message.');
      echo $conn->error;
    }
    /**
     * If error array is not empty, one of the queries in the transaction 
     * has failed and it is rolled back. Else, commits the transaction.
     */
    if (!empty($error)) {
      $conn->rollback();
      $failed = "There was an error..";
    } else {
      $conn->commit();
      $success = "User successfully approved.";
    }
  }
}



/**
 * If the appointment is approved, sets the confirmed boolean to 1
 * in the database and sends the user a message on the internal messaging system.
 */
if (isset($_POST['approveAppt'])) {
  $apptToApprove = $_POST['apptID'];
  $userID = $_POST['userID'];
  $apptTime = $_POST['apptTime'];
  $apptDate = $_POST['apptDate'];
  $apptDetails = $_POST['apptDetails'];
  $user = $_POST['apptClient'];


  $approveAppt = "UPDATE webdev_appointments
    SET confirmed = 1
    WHERE id = $apptToApprove; ";


  if ($userID != 0 || $userID != null) {
    $sendMessage = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, hide) VALUES
    ('$userID', '$coach_id', 'CONFIRMATION of Appointment - $apptDate at $apptTime', 'Dear $user, your request for an appointment for $apptDetails on $apptDate
    at $apptTime has been confirmed. I will see you then!', '$coach_id', '$userID', 0)";
  }

  // transaction adapted from online tutorial https://www.youtube.com/watch?v=CNt9HPqDIVc
  $conn->autocommit(false);

  $error = array();

  $a = $conn->query($approveAppt);
  if ($a == false) {
    array_push($error, 'Problem pushing to db');
    $apptFailed = "There was an error with approving the appointment";
  }
  $b = $conn->query($sendMessage);
  if ($b == false) {
    array_push($error, 'Problem pushing to db');
    $apptFailed = "There was an error with sending the user a message.";
  }

  /**
   * If error array is not empty, one of the queries in the transaction 
   * has failed and it is rolled back. Else, commits the transaction.
   */
  if (!empty($error)) {
    $conn->rollback();
    $apptFailed = "There was an error..";
  } else {
    //commit
    $conn->commit();
    $apptSuccess = "Appointment successfully approved.";
  }
}
// if the appointment is rejected, deletes row from database.
if (isset($_POST['rejectAppt'])) {

  $userID = $_POST['userID'];
  $apptTime = $_POST['apptTime'];
  $apptDate = $_POST['apptDate'];
  $apptDetails = $_POST['apptDetails'];
  $user = $_POST['apptClient'];
  $apptToReject = $_POST['apptID'];
  $rejectAppt = "DELETE FROM webdev_appointments
    WHERE id = $apptToReject; ";

  $executeRejectAppt = $conn->query($rejectAppt);

  if ($userID != 0 || $userID != null) {
    $sendMessage = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, hide) VALUES
    ('$userID', '$coach_id', 'REJECTION of Appointment - $apptDate at $apptTime', 'Dear $user, your request for an appointment for $apptDetails on $apptDate
    at $apptTime has been rejected. Feel free to try another date/time. Regards.', '$coach_id', '$userID', 0)";
  }

  // transaction adapted from online tutorial https://www.youtube.com/watch?v=CNt9HPqDIVc
  $conn->autocommit(false);

  $error = array();

  $a = $conn->query($rejectAppt);
  if ($a == false) {
    array_push($error, 'Problem pushing to db');
    $apptFailed = "There was an error with approving the appointment";
  }
  $b = $conn->query($sendMessage);
  if ($b == false) {
    array_push($error, 'Problem pushing to db');
    $apptFailed = "There was an error with sending the user a message.";
  }


  /**
   * If error array is not empty, one of the queries in the transaction 
   * has failed and it is rolled back. Else, commits the transaction.
   */
  if (!empty($error)) {
    $conn->rollback();
    $apptFailed = "There was an error with rejecting the appointment";
  } else {
    //commit
    $conn->commit();
    $apptSuccess = "Appointment successfully rejected.";
  }
}

/**
 * If a user is rejected by a coach, deletes all of their information from the system
 * and emails them to confirm that deletion has occured.
 */
if (isset($_POST['rejectSignup'])) {
  $userToDel = $_POST['userID'];
  // get user email to email them about account deletion
  $getDetails = $conn->prepare("SELECT email, username FROM webdev_users
    WHERE id = ?");
  $getDetails->bind_param("i", $userToDel);
  $getDetails->execute();
  $getDetails->store_result();
  $getDetails->bind_result($userEmail, $username);
  $getDetails->fetch();

 

  // transaction adapted from online tutorial <https://www.youtube.com/watch?v=CNt9HPqDIVc>
  $conn->autocommit(false);

  $error = array();

  $a = $conn->query("DELETE FROM webdev_user_details WHERE username = '$username'");
  if ($a == false) {
    array_push($error, 'Problem deleting user data');
    echo $conn->error;
  }

  $b = $conn->query("DELETE FROM webdev_users WHERE id = $userToDel");
  if ($b == false) {
    array_push($error, 'Problem with deleting user data.');
    echo $conn->error;
  }

  /**
   * If error array is not empty, one of the queries in the transaction 
   * has failed and it is rolled back. Else, commits the transaction.
   */
  if (!empty($error)) {
    $conn->rollback();
    echo $conn->error;
  } else {
    $conn->commit();
    $success = "User successfully rejected.";
    // mail them to confirm account has been rejected and delete.
    $message = "Dear user, \n
    This is an automated message, please do not reply. \n
    You are receiving this email because your request to sign up with Gymafi has been rejected at this time. \n
    Your details have been deleted from our system. \n
    Please feel free to sign up in the future when our coaches have more availability. \n
    We are sorry for any inconvenience. \n

    Kind regards, \n
    Gymafi";

    mail($userEmail, 'Gymafi - Rejection of Application', $message);
  }
}
?>


<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gymafi | Approvals</title>
  <link href="../styles/bulma.css" rel="stylesheet">
  <link href="../styles/gui.css" rel="stylesheet">
  <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
  <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
  <script src="../script/myScript.js"></script>


</head>

<body class="has-background-grey-lighter" id='approvalBody'>

  <!-- log out button-->
  <nav class='navbar is-dark' role='navigation' aria-label='main navigation'>
    <div class='navbar-end'>
      <div class='navbar-item'>
        <div class='buttons'>
          <a class='button is-danger' href='logout.php'>
            Logout
          </a>
        </div>
      </div>
    </div>
    </div>
  </nav>

  <!-- page header/hero-->
  <section class='hero is-small'>
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

  <!-- Navigation bar-->
  <nav class='navbar is-info' role='navigation' aria-label='main navigation'>


    <a role='button' class='navbar-burger' aria-label='menu' aria-expanded='false'>
      <span aria-hidden='true'></span>
      <span aria-hidden='true'></span>
      <span aria-hidden='true'></span>
    </a>
    </div>

    <div id='navbarBasicExample' class='navbar-menu has-background-info'>
      <div class='navbar-start myNav'>
        <a class='navbar-item has-text-black' href='../dashboard.php'>
          Dashboard
        </a>


        <a class='navbar-item has-text-black has-background-warning' href='approvals.php'>
          Approvals
        </a>

        <a class='navbar-item has-text-black' href='../appointments.php'>
          Appointments
        </a>


        <a class='navbar-item has-text-black' href='groups.php'>
          Groups
        </a>

        <a class='navbar-item has-text-black' href='../inbox.php'>
          Inbox
        </a>

        <div class="navbar-item has-dropdown is-hoverable">
          <a class="navbar-link has-text-black">
            More
          </a>

          <div class="navbar-dropdown has-background-info has-text-black">
            <a class='navbar-item has-text-black' href='manageclients.php'>
              Edit Client Info
            </a>

            <a class='navbar-item has-text-black' href='editcontent.php'>
              Edit Site Content
            </a>
          </div>



        </div> <!-- end of navbar dropdown-->
      </div> <!-- end of nav-bar item-->
    </div> <!-- end of navbarBasicExample-->


    </div>
  </nav>

  <div class='container' id='approvalCont'>

    <article class="message is-dark">
      <div class="message-header">
        <p>Approve Users</p>

      </div>
      <div class="message-body">

        <?php
        // displays message to user based on if the request failed or succeeded
        if (isset($failed)) {
          echo "<p class='displayError'>$failed</p>";
        } else if (isset($success)) {
          echo "<p class='displaySucc'>$success</p>";
        }
        //get all unapproved users who signed up for the coach who is logged in
        $unapprovedUsers = "SELECT webdev_users.id, email, date_of_birth, name, phone_number FROM webdev_users 
INNER JOIN webdev_user_details ON webdev_users.username = webdev_user_details.username
WHERE webdev_users.approved = 0 AND webdev_user_details.coach = $coach_id
ORDER BY webdev_users.id ASC;";

        $fetchUnapprovedUsers = $conn->query($unapprovedUsers);
        if (!$fetchUnapprovedUsers) {
          echo $conn->error;
        }

        /**
         * Counts the number of results returned, if <5 sets variable to only print out as many as there are,
         * otherwise will be issues with printing out blank entries to screen,
         * Else, max of 5 by default.
         */
        $numberOfUsers = $fetchUnapprovedUsers->num_rows;
        if ($numberOfUsers < 5) {
          $maxCount = $numberOfUsers;
        } else {
          $maxCount = 5;
        }
        $userCounter = 0;

        // shows only 5 pending users at a time, from oldest to newest.
        while ($userCounter < $maxCount) {
          $row = $fetchUnapprovedUsers->fetch_assoc();

          $pendingUserID = $row['id'];
          $name = $row['name'];
          $email = $row['email'];
          $phoneNumber = $row['phone_number'];
          $date_of_birth = $row['date_of_birth'];
          $userCounter++;

          echo "
    <!-- prints out all of the unapproved users for this coach in an individual element-->
<article class='message is-link'>
  <div class='message-header'>
    <p>", htmlentities($name, ENT_QUOTES), "</p>
  </div>
  <div class='message-body'>
    <p>", htmlentities($email, ENT_QUOTES), " </p>  <p> ", htmlentities($phoneNumber, ENT_QUOTES), "</p>  <p>", htmlentities($date_of_birth, ENT_QUOTES), " </p> ";

        ?>
          <form action='approvals.php' method='POST'>
            <nav class='level'>
              <!-- Left side -->
              <div class='level-left'>


              </div>

              <!-- Right side -->
              <div class='level-right'>
                <!-- hidden input to hold the id of the appointment, posted and used update/delete entry in db.-->
                <?php
                echo "
    <input type='hidden' id='userID' name='userID' value='$pendingUserID'>";
                ?>
                <!--  buttons to accept/reject user -->
                <input type='submit' class='button is-danger rejectButton' onclick="return confirm(' Are you sure you wish to reject this user?')" value='Reject' name='rejectSignup'>
                <input type='submit' class='button is-primary' value='Approve' name='approveSignup'>

              </div>
          </form>
          </nav>

      </div>
    </article>

  <?php
        }

  ?>

  </article>





  <article class="message is-dark">
    <div class="message-header">
      <p>Approve Appointments</p>

    </div>
    <div class="message-body">

      <?php

      //gather all unapproved appointments
      if (isset($apptFailed)) {
        echo "<p class='displayError'>$apptFailed</p>";
      } else if (isset($apptSuccess)) {
        echo "<p class='displaySucc'>$apptSuccess</p>";
      }
      $unapprovedAppt = "SELECT webdev_appointments.id, webdev_appointments.date, webdev_appointments.time, 
            webdev_appointments.duration, webdev_appointments.user_id, webdev_appointments.details, 
            webdev_user_details.name FROM webdev_appointments 
INNER JOIN webdev_user_details ON webdev_appointments.user_id = webdev_user_details.user_id
WHERE coach_id = '$coach_id' AND confirmed = 0
ORDER BY webdev_appointments.id ASC";


      $fetchUnapprovedAppts = $conn->query($unapprovedAppt);
      if (!$fetchUnapprovedAppts) {
        echo $conn->error;
      }


      /**
       * Counts the number of results returned, if <5 sets variable to only print out as many as there are,
       * otherwise will be issues with printing out blank entries to screen,
       * Else, max of 5 by default.
       */
      $numberOfAppts = $fetchUnapprovedAppts->num_rows;
      if ($numberOfUsers < 5) {
        $maxApptCount = $numberOfAppts;
      } else {
        $maxApptCount = 5;
      }
      $apptCounter = 0;

      // shows only 5 pending appointments at a time, from oldest to newest.
      while ($apptCounter < $maxApptCount) {
        $row = $fetchUnapprovedAppts->fetch_assoc();
        $appointmentID = $row['id'];
        $date = $row['date'];
        $time = $row['time'];
        $duration = $row['duration'];
        $details = $row['details'];
        $user = $row['name'];
        $userID = $row['user_id'];
        $apptCounter++;

        echo "
<!-- print out all unapproved appointments for that coach -->
<article class='message is-link'>
  <div class='message-header'>
    <p>Session time: ", htmlentities($date, ENT_QUOTES), " at ", htmlentities($time, ENT_QUOTES), " for ", htmlentities($duration, ENT_QUOTES), "</p>
  </div>
  <div class='message-body'>
    <p> Session with: ", htmlentities($user, ENT_QUOTES), " </p>  
    <p> Session details: ", htmlentities($details, ENT_QUOTES), "</p> 
    <form action='approvals.php' method='POST'>
    <nav class='level'>
    <!-- Left side -->
    <div class='level-left' >
      
      
    </div>
  
    <!-- Right side -->
    <div class='level-right'>";
      ?>
        <!-- hidden input to hold the id of the appointment, posted and used update/delete entry in db.-->
        <?php
        echo "
    <input type='hidden' id='apptID' name='apptID' value='$appointmentID'>
    <input type='hidden' id='apptID' name='userID' value='$userID'>
    <input type='hidden' id='apptID' name='apptDate' value='$date'>
    <input type='hidden' id='apptID' name='apptTime' value='$time'>
    <input type='hidden' id='apptID' name='apptDetails' value='$details'>
    <input type='hidden' id='apptID' name='apptClient' value='$user'>";
        ?>
        <!-- buttons to reject/accept the appointments -->
        <input type='submit' class='button is-danger rejectButton' onclick="return confirm(' Are you sure you wish to reject this appointment?')" value='Reject' name='rejectAppt'>
        <input type='submit' class='button is-primary' value='Approve' name='approveAppt'>

    </div>
    </form>
    </nav>
  </div>
  </article>
<?php
      };
?>
</article>
</div>
</div>


<div class="myFooter">
  <footer class="footer has-background-dark alsoMyFooter" id='approvalsFooter'>
    <div class="content has-text-centered has-text-white">
      <p>
        <span id="boldFoot">CSC7062 Project</span> by Jordan Brown (40282125).
      </p>
    </div>
  </footer>
</div>
</body>


</html>