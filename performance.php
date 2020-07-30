<?php
session_start();
include("conn.php");

if (isset($_SESSION['gymafi_coachid'])) {

  header("location: dashboard.php");
} else if (isset($_SESSION['gymafi_superadmin'])) {
  header("location: admin/superadmin.php");
} else if (
  !(isset($_SESSION['gymafi_coachid'])) && !(isset($_SESSION['gymafi_superadmin']))
  && !(isset($_SESSION['gymafi_userid']))
) {
  header("location: login.php");
} 
$userid = $_SESSION['gymafi_userid'];
$todaysDate = date('Y-m-d'); // variable to ensure the user can only make a log for sessions which have already happened.

/**
 * If the user clicks the button to create a new log, opens this modal.
 * Allows them to enter a small comment and rating about how they thought the session went.
 * Once submitted, the coach can they edit and add their own comments/rating which the user will see.
 */
if (isset($_POST['createLog'])) {
  if (!isset($_POST['sessionToLog'])) {
    $logCreationFailed = "Cannot create log - empty entry selected.";
  }
  $sessionIDToLog = $_POST['sessionToLog'];
  $getSessionInfo = "SELECT * FROM webdev_appointments WHERE user_id = '$userid' AND id = '$sessionIDToLog'";
  $executeGetSessionInfo = $conn->query($getSessionInfo);

  if (!$executeGetSessionInfo) {
    echo $conn->error;
  }

  while ($row = $executeGetSessionInfo->fetch_assoc()) {

    echo "<div class='modal is-active' id='addLog'>
  <div class='modal-background'></div>
  <div class='modal-card'>
    <header class='modal-card-head'>
      <p class='modal-card-title'>Add new Log</p>
      <button class='delete cancelUpdate' aria-label='close' ></button>
    
    </header>
   
    <section class='modal-card-body'>
   
      <form action='performance.php' method='POST' id='addALog'>
        <div class='field'>
          <label class='label'>Your comments: </label>
          <div class='control'>
            <input class='input' type='text' placeholder='Went well, enjoyed the routine' id='userComments' name='userComments'>
          </div>
          <p class='help is-danger' id='commentWarn'> </p>
        </div>
        <input type='hidden' id='userID' name='sessionID' value='$sessionIDToLog'>
        <div class='field'>
        <label class='label'>Your rating (out of 5): </label>
        <div class='control'>
          <input class='input' type='number' placeholder='4' id='userRating' name='userRating'>
        </div>
        <p class='help is-danger' id='ratingWarn'> </p>
      </div>
      <div class='field'>
      
      <footer class='modal-card-foot'>
      <input type='submit' class='button is-success submitLogBut' id='logSubmitBut' value='Save changes' name='logSubmit'>

    </footer>


      </form>
    </section>
   
  </div>
</div>";
  }
}

/**
 * If a user submits a new log, attempts to validate it.
 */
if (isset($_POST['logSubmit'])) {
  $appointmentID = $_POST['sessionID'];
  $sanitisedComment = $conn->real_escape_string(trim($_POST['userComments']));
  $sanitisedRating = $conn->real_escape_string(trim($_POST['userRating']));

  /**
   * Checks if a log already exists for this record, if so the log 
   * is not created and error message displayed
   */

  $checkIfLogExists = "SELECT * FROM webdev_appointments_logs WHERE appointment_id = '$appointmentID'";

  $executeCheckIfLogExists = $conn->query($checkIfLogExists);

  $num = $executeCheckIfLogExists->num_rows;

  if ($num > 0) {
    $logCreationFailed = "Log already exists for this session. Try editing instead!";
  } else {
    if (!ctype_digit($sanitisedRating)) {
      $logCreationFailed = "Your log has not been submitted - rating not numeric.";
    } else if ($sanitisedRating < 1 || $sanitisedRating > 5) {
      $logCreationFailed = "Your log has not been submitted - rating not within range (1-5).";
    } else if (strlen($_POST['userComments']) > 100) {
      $logCreationFailed = "Comment too long - must be < 100 characters.";
    } else {
      $writeUserLog = "INSERT INTO webdev_appointments_logs
  (appointment_id,  user_comments, user_rating)
  VALUES ('$appointmentID','$sanitisedComment', '$sanitisedRating');
   ";

      $executeWriteUserLog  = $conn->query($writeUserLog);

      if (!$executeWriteUserLog) {
        echo $conn->error;
        $logCreationFailed = "Error with creating log. Please check your input and try again.";
      }
    }
  }
}

/**
 * Allows the user to edit an existing log.
 */
if (isset($_POST['editLog'])) {
  if (!isset($_POST['logToEdit'])) {
    $editFailed = "Cannot edit log - empty entry selected.";
  }
  $logToEdit = $_POST['logToEdit'];

  $findExistingLog = $conn->prepare("SELECT user_comments, user_rating FROM webdev_appointments_logs
  INNER JOIN webdev_appointments
  ON webdev_appointments_logs.appointment_id = webdev_appointments.id
  WHERE user_id = ? AND webdev_appointments_logs.id = ?");
  $findExistingLog->bind_param("ii", $userid, $logToEdit);
  $findExistingLog->execute();
  $findExistingLog->store_result();
  $findExistingLog->bind_result($userComment, $userRating);
  $findExistingLog->fetch();

  echo "<div class='modal is-active' id='editLog'>
  <div class='modal-background'></div>
  <div class='modal-card'>
    <header class='modal-card-head'>
      <p class='modal-card-title'>Edit a Log</p>
      <button class='delete cancelUpdate' aria-label='close' ></button>
    
    </header>
   
    <section class='modal-card-body'>
   
      <form action='performance.php' method='POST' id='editALog'>
        <div class='field'>
          <label class='label'>Your comments: </label>
          <div class='control'>";
?>
  <input class='input' type='text' value="<?php echo $userComment ?>" id='editUserComments' name='editUserComments'>
  </div>
  <p class='help is-danger' id='editCommentsWarn'> </p>
  </div>
<?php
  echo "
        <input type='hidden' id='userID' name='editSessionID' value='$logToEdit'>
        <div class='field'>
        <label class='label'>Your rating (out of 5): </label>
        <div class='control'>
          <input class='input' type='number' value='$userRating' id='editUserRating' name='editUserRating'>
        </div>
        <p class='help is-danger' id='editRatingWarn'> </p>
      </div>
      <div class='field'>
      
      <footer class='modal-card-foot'>
      <input type='submit' class='button is-success editLogBut' id='editLogBut' value='Save changes' name='editSubmit'>

    </footer>


      </form>
    </section>
   
  </div>
</div>";
}

/**
 * If the user submits the edit log form, attempts to validate it.
 * Sanitises the data, checks if it is the same as the existing data.
 * If it is different, processes the update. 
 */
if (isset($_POST['editSubmit'])) {

  $logToEdit = $conn->real_escape_string(trim($_POST['editSessionID']));
  $userComment = $_POST['editUserComments'];
  $sanitisedEditedComment = $conn->real_escape_string(trim($userComment));
  $sanitisedEditedRating = $conn->real_escape_string(trim($_POST['editUserRating']));


  $findExistingLog = $conn->prepare("SELECT user_comments, user_rating FROM webdev_appointments_logs WHERE id = ?");
  $findExistingLog->bind_param("i", $logToEdit);
  $findExistingLog->execute();
  $findExistingLog->store_result();
  $findExistingLog->bind_result($oldUserComment, $oldUserRating);
  $findExistingLog->fetch();


  /**
   * Checks if input is the same, if so does not send query.
   */

  if (($userComment == $oldUserComment) && ($sanitisedEditedRating == $oldUserRating)) {
    $editFailed = "Your log has not been submitted - same as current.";
  } else if (!ctype_digit($sanitisedEditedRating)) {
    $editFailed = "Your log has not been edited - rating not numeric.";
  } else if ($sanitisedEditedRating < 1 || $sanitisedEditedRating > 5) {
    $editFailed = "Your log has not been edited - rating not within range (1-5).";
  } else if (strlen($userComment) > 100) {
    $editFailed = "Comment too long - must be < 100 characters.";
  } else {
    $editUserLog = "UPDATE webdev_appointments_logs
  SET user_comments = '$sanitisedEditedComment', user_rating = '$sanitisedEditedRating'
  WHERE id = '$logToEdit'";

    $executeEditUserLog  = $conn->query($editUserLog);

    if (!$executeEditUserLog) {
      echo $conn->error;
      $editFailed = "Error with editing log. Please check your input and try again.";
    } else {
      $editSuccess = "Your log has successfully been edited.";
    }
  }
}

/**
 * If the user attempts to delete a log, processes it.
 */
if (isset($_POST['deleteLog'])) {
  if (!isset($_POST['logToDelete'])) {
    $deleteError = "Cannot edit log - empty entry selected.";
  }


  $logID = $conn->real_escape_string(trim($_POST['logToDelete']));

  $deleteLog = "DELETE FROM webdev_appointments_logs WHERE id='$logID';";

  $executeDeleteLog = $conn->query($deleteLog);

  if (!$executeDeleteLog) {
    echo $conn->error;
    $deleteError = "Sorry, your log could not be deleted.";
  } else {
    $successfulDelete = "Your log has successfully been be deleted.";
  }
}



?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gymafi | Logs</title>
  <link href="styles/bulma.css" rel="stylesheet">
  <link href="styles/lightbox.css" rel="stylesheet">
  <link href="styles/gui.css" rel="stylesheet">
  <link href='styles/Chart.css' rel="stylesheet">
  <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
  <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
  <script src="script/myScript.js"></script>

  <script src='script/Chart.bundle.js'></script>
  <script src='script/Chart.js'></script>





</head>

<body class="has-background-grey-lighter">

  <nav class='navbar is-dark' role='navigation' aria-label='main navigation'>
    <div class='navbar-end'>
      <div class='navbar-item'>
        <div class='buttons'>
          <a class='button is-primary' href='profile.php'>
            Profile
          </a>
          <a class='button is-danger' href='logout.php'>
            Logout
          </a>
        </div>
      </div>
    </div>
    </div>
  </nav>

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
      <div class='navbar-start myNav'>
        <a class='navbar-item has-text-white' href='dashboard.php'>
          Dashboard
        </a>

        <a class='navbar-item has-background-dark has-text-white ' href='appointments.php'>
          Appointments
        </a>
        <a class='navbar-item has-background-dark has-text-white' href='inbox.php'>
          Inbox
        </a>

        <a class='navbar-item has-background-dark has-text-white has-background-primary'>
          Performance Log
        </a>
        <a class='navbar-item has-text-white ' href='gallery.php'>
          Your Gallery
        </a>




      </div> <!-- end of navbarBasicExample-->


    </div>
  </nav>



  <?php


  /**
   * If user's account has not been approved by their desired coach, displays an error image/message and does not show the 
   * webpage as an approved use would see. 
   * User can click on the button to return to the dashboard.
   */
  $checkIfApproved = "SELECT * FROM webdev_users WHERE id = $userid";
  $executeCheckApproval = $conn->query($checkIfApproved);

  if (!$executeCheckApproval) {
    echo $conn->error;
  }

  while ($row = $executeCheckApproval->fetch_assoc()) {
    $isApproved = $row['approved'];
  }
  if ($isApproved == 1) {
  ?>
    <div id='dashColumns'>
      <div class='columns'>
        <div class='column is-3'>
          <article class='message is-link'>
            <div class='message-header'>
              Create a log
            </div>

            <div class='message-body'>
              <p> Select a session to log: </p>
              <form action='performance.php' method='post'>
                <div class='control select'>
                  <select name='sessionToLog'>

                    <?php
                    // only selects the appointments which do not already have a log and are before today's date.
                    $selectAllSessions = "SELECT date, time, details, id FROM webdev_appointments WHERE user_id = '$userid' 
                    AND confirmed = '1' AND  id NOT IN(SELECT appointment_id FROM webdev_appointments_logs)
                    AND webdev_appointments.date < '$todaysDate'";
                    $executeSelectAllSessions = $conn->query($selectAllSessions);

                    $num = $executeSelectAllSessions->num_rows;

                    if ($num == 0) {
                      $logCreationNotPossible = "(You have created logs for all your sessions. Try editing below instead!)";
                    }
                    while ($row = $executeSelectAllSessions->fetch_assoc()) {
                      $sessionDate = $row['date'];
                      $sessionTime = $row['time'];
                      $sessionDesc = $row['details'];
                      $sessionID = $row['id'];
                      echo "<option value='$sessionID'> ", htmlentities($sessionDesc, ENT_QUOTES), " 
                      (", htmlentities($sessionDate, ENT_QUOTES), " | ", htmlentities($sessionTime, ENT_QUOTES), ") </option>";
                    }
                    ?>
                  </select>

                </div>
                <?php
                if (isset($logCreationFailed)) {
                  echo "<p class='displayError'>$logCreationFailed</p>";
                } else if (isset($logCreationNotPossible)) {
                  echo "<p id='creationNotPossible'>$logCreationNotPossible</p>";
                }
                ?>
                <p class='logButton'><input type='submit' class='button is-primary logCreate' value='Create log' name='createLog'> </p>

            </div> <!-- end of message body-->

          </article>
          </form>

          <div class='columns'>
            <div class='column'>
              <article class='message is-warning'>
                <div class='message-header'>

                  Edit a log

                </div>

                <div class='message-body'>
                  <form action='performance.php' method='POST' id='editLogs'>
                    <p> Select a log to edit: <div class='select'>
                        <select name='logToEdit'>
                          <?php
                          $getAllLogs = "SELECT webdev_appointments_logs.id, webdev_appointments_logs.appointment_id, webdev_appointments_logs.user_comments, 
                          webdev_appointments_logs.user_rating, webdev_appointments.date, webdev_appointments.time, webdev_appointments.details,
                          webdev_appointments.user_id
                          FROM webdev_appointments_logs 
                          INNER JOIN webdev_appointments 
                          ON webdev_appointments_logs.appointment_id =  webdev_appointments.id
                          WHERE  user_id = '$userid'";
                          $executeGetAllLogs = $conn->query($getAllLogs);
                          if (!$executeGetAllLogs) {
                            echo $conn->error;
                          } else {
                            while ($row = $executeGetAllLogs->fetch_assoc()) {
                              $logID = $row['id'];
                              $logAppointmentID = $row['appointment_id'];
                              $userRating = $row['user_rating'];
                              $userComments = $row['user_comments'];
                              $apptDate = $row['date'];
                              $apptTime = $row['time'];
                              $apptDesc = $row['details'];

                              echo "<option value='$logID'>", htmlentities($apptDate, ENT_QUOTES), " | ", htmlentities($apptTime, ENT_QUOTES), " 
                              (", htmlentities($userRating, ENT_QUOTES), ")</option>";
                            }
                          }
                          ?>


                        </select>
                      </div>
                      <?php
                      if (isset($editFailed)) {
                        echo "<p class='displayError'>$editFailed</p>";
                      } else if (isset($editSuccess)) {
                        echo "<p class='displaySucc'> $editSuccess</p>";
                      }
                      ?>

                      <p class='deletePhoto logButton'><input type='submit' class='button is-warning deletePhoto' value='Edit log' name='editLog'> </p>

                  </form>
                </div> <!-- end of message body-->
              </article>
            </div>
          </div>


          <div class='columns'>
            <div class='column'>
              <article class='message is-danger'>
                <div class='message-header'>

                  Delete a log

                </div>

                <div class='message-body'>
                  <form action='performance.php' method='POST' id='deletePhotos'>
                    <p> Select a log to delete: <div class='select'>
                        <select name='logToDelete'>
                          <?php
                          $getAllLogs = "SELECT webdev_appointments_logs.id, webdev_appointments_logs.appointment_id, webdev_appointments_logs.user_comments, 
                          webdev_appointments_logs.user_rating, webdev_appointments.date, webdev_appointments.time, webdev_appointments.details,
                          webdev_appointments.user_id
                          FROM webdev_appointments_logs 
                          INNER JOIN webdev_appointments 
                          ON webdev_appointments_logs.appointment_id =  webdev_appointments.id
                          WHERE  user_id = '$userid'";
                          $executeGetAllLogs = $conn->query($getAllLogs);
                          if (!$executeGetAllLogs) {
                            echo $conn->error;
                          } else {
                            while ($row = $executeGetAllLogs->fetch_assoc()) {
                              $logID = $row['id'];
                              $logAppointmentID = $row['appointment_id'];
                              $userRating = $row['user_rating'];
                              $userComments = $row['user_comments'];
                              $apptDate = $row['date'];
                              $apptTime = $row['time'];
                              $apptDesc = $row['details'];

                              echo "<option value='$logID'>", htmlentities($apptDate, ENT_QUOTES), " | ", htmlentities($apptTime, ENT_QUOTES), " 
                              (", htmlentities($userRating, ENT_QUOTES), ")</option>";
                            }
                          }


                          echo "</select>
  </div>
  ";
                          if (isset($deleteError)) {
                            echo "<p class='displayError'> $deleteError</p>";
                          } else if (isset($successfulDelete)) {
                            echo "<p class='displaySucc'> $successfulDelete</p>";
                          }
                          ?>
                          <p class='deletePhoto logButton'><input type='submit' class='button is-danger deleteLog' onclick="return confirm('Are you sure you wish to delete your log? THIS CANNOT BE UNDONE.')" value='Delete Log' name='deleteLog'> </p>
                  </form>
                </div> <!-- end of message body-->
              </article>
            </div>
          </div>

        </div><!-- end of column-->



        <!-- Displays all the logs the user has created a log for -->
        <div class='column is-7' id='rightColumns'>
          <article class='message is-dark'>
            <div class='message-header'>
              <p>
                <h1 class='title' id='performanceLogsHead'>Performance Logs</h1>
              </p>

            </div>
            <div class='message-body'>
              <p> Displaying your most recent five logs. You can view older logs using the option on the left.</p>

              <?php


              $getAllLogs = "SELECT webdev_appointments_logs.id, webdev_appointments_logs.appointment_id, webdev_appointments_logs.coach_comments, 
              webdev_appointments_logs.coach_rating, webdev_appointments_logs.user_comments, 
              webdev_appointments_logs.user_rating, webdev_appointments.date, webdev_appointments.time, webdev_appointments.details,
              webdev_appointments.user_id
              FROM webdev_appointments_logs 
              INNER JOIN webdev_appointments 
              ON webdev_appointments_logs.appointment_id =  webdev_appointments.id
              WHERE  user_id = '$userid'
              ORDER BY webdev_appointments_logs.id DESC";
              $executeGetAllLogs = $conn->query($getAllLogs);
              if (!$executeGetAllLogs) {
                echo $conn->error;
              } else {

                $numOfLogs = $executeGetAllLogs->num_rows;
                if ($numOfLogs < 5) {
                  $maxLogs = $numOfLogs;
                } else {
                  $maxLogs = 5;
                }
                $logCounter = 0;
                while ($logCounter < $maxLogs) {

                  $logCounter++;
                  $row = $executeGetAllLogs->fetch_assoc();
                  $logID = $row['id'];
                  $logAppointmentID = $row['appointment_id'];
                  $userRating = $row['user_rating'];
                  $userComments = $row['user_comments'];
                  $apptDate = $row['date'];
                  $apptTime = $row['time'];
                  $apptDesc = $row['details'];
                  $coachComment = $row['coach_comments'];
                  $coachRating = $row['coach_rating'];

                  echo "
  <article class='message is-success'>
  <div class='message-header level'>
  
    <!-- Left side -->
    <div class='level-left' >
      <div class='level-item'>
      <p>Session date: ", htmlentities($apptDate, ENT_QUOTES), " (", htmlentities($apptTime, ENT_QUOTES), ")</p>
      </div>
      
    </div>
  
    <!-- Right side -->
    <div class='level-right'>
    <div class='level-item'>
      <p> Session description: ", htmlentities($apptDesc, ENT_QUOTES), " </p>
      </div>

    </div> 
   
 
  </div> <!-- end of message-header-->
  <div class='message-body'>
  <div class='level'>
  <!-- Left side -->
  <div class='level-left' >
  
    <p>Your session comments: ", htmlentities($userComments, ENT_QUOTES), "</p>  
    
    
  </div>

  <!-- Right side -->
  <div class='level-right'>

  <p>Your session rating: ", htmlentities($userRating, ENT_QUOTES), "  </p> 
   
  </div>
  </div>

  <div class='level'>
  <!-- Left side -->
  <div class='level-left' >
  
  <p> Comments from your coach: ", htmlentities($coachComment, ENT_QUOTES), "</p>
    
    
  </div>

  <!-- Right side -->
  <div class='level-right'>

  <p> Rating from your coach: ", htmlentities($coachRating, ENT_QUOTES), " </p>
   
  </div>
  </div>

  

</article>
          
          ";
                }
              }
              ?>
            </div>

        </div> <!-- end of message body-->
        </article>



      </div>
    </div>
    </div>
    </div>
    </div>
    <div class='columns'>
      <div class='column is-4'>

      </div>
      <!-- Displays all the scores given by both the user and the coach and displays them in a 
    line chart. Chart is from: https://www.chartjs.org/ -->
      <div class='column is-7' id='performanceGraph lineChart'>
        <article class='message is-dark'>
          <div class='message-header'>
            <p>
              <h1 class='title' id='performanceLogsHead'>Performance Graph</h1>
            </p>

          </div>
          <div class='message-body'>
            <canvas id="line-chart" width="800" height="450"></canvas>
            <?php
            $idArray = array();
            $userRatingArray = array();
            $coachRatingArray = array();
            $getAllDates = "SELECT webdev_appointments_logs.appointment_id, user_rating, coach_rating FROM webdev_appointments_logs 
                   INNER JOIN webdev_appointments 
                   ON webdev_appointments_logs.appointment_id = webdev_appointments.id
                   WHERE user_id = $userid";
            $executeGetAllDates = $conn->query($getAllDates);
            if (!$executeGetAllDates) {
              echo $conn->error;
            }
            /**
             * Grabs all of the data needed to populate the chart
             */
            while ($row = $executeGetAllDates->fetch_assoc()) {
              $id = $row['appointment_id'];
              $id .= ",";
              array_push($idArray, $id);
              $userRating = $row['user_rating'];
              $userRating .= ",";
              array_push($userRatingArray, $userRating);
              $coachRating = $row['coach_rating'];
              $coachRating .= ",";
              array_push($coachRatingArray, $coachRating);
            }
            echo "<script>
            new Chart(document.getElementById('line-chart'), {
                  type: 'line',
                  data: {
         
                labels: [";
            foreach ($idArray as $idsToPrint) {
              echo $idsToPrint;
            }

            echo "],
                
                datasets: [{
                  data: [";

            foreach ($userRatingArray as $userRatingToPrint) {
              echo $userRatingToPrint;
            }
            echo "],
                  label: 'Your Ratings',
                  borderColor: '#3e95cd',
                  fill: false
                }, {
                  data: [";

            foreach ($coachRatingArray as $coachRatingToPrint) {
              echo $coachRatingToPrint;
            }
            echo "
                ],
                  label: 'Coach Ratings',
                  borderColor: '#8e5ea2',
                  fill: false
                }, ]
              },
              options: {
                title: {
                  display: true,
                  text: 'Your session ratings'
                }
              }
            });
          
          </script>";
            ?>



          </div>

      </div> <!-- end of message body-->
      </article>

    </div>
    </div><!-- end of package info-->

  <?php
    /**
     * Error symbol/message that is displayed to the user if they try to access the page without their account having been approved, 
     * rather than simply kicking them back to the dashboard without warning. 
     */
  } else {
  ?>
    <div class='container'>

      <div class='notApproved'>
        <h1 class='title'> Your account has not yet been approved. </h1>
      </div>

      <div class='notApproved'><a href='dashboard.php'><img src='images/error.png'></a></div>
      <div class='notApprovedClick'>(Please click the image to return to the dashboard)</div>


    </div>
  <?php
  }
  ?>



  <div class='myFooter'>
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