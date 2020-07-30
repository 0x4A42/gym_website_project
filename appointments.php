<?php
session_start();
include("conn.php");


if (isset($_SESSION['gymafi_userid'])) {
    // store user id in local var
    $userid = $_SESSION['gymafi_userid'];
    // if a coach is logged in, sets their session value as local variable and gets their name to use.
} else if (isset($_SESSION['gymafi_coachid'])) {
    $loggedInCoachID = $_SESSION['gymafi_coachid'];
    $isApproved = 1;
    $getCoachName = $conn->prepare("SELECT name FROM webdev_coach WHERE id = ?");
    $getCoachName->bind_param("i", $loggedInCoachID);
    $getCoachName->execute();
    $getCoachName->store_result();
    $getCoachName->bind_result($nameOfCoach);
    $getCoachName->fetch();
} else if (isset($_SESSION['gymafi_superadmin'])) {
    header("location: admin/superadmin.php");
} else {
    header("location: login.php"); // if user is not logged in, kicks out to login page.
}

/**
 * If a normal user is logged in, grabs information of their coach to display on the calendar 
 * and to use when creating an appointment
 */
if (isset($userid)) {


    $getCoach = $conn->prepare("SELECT webdev_user_details.coach, webdev_coach.name FROM webdev_user_details 
    INNER JOIN webdev_coach 
    on webdev_user_details.coach = webdev_coach.id 
    WHERE webdev_user_details.user_id = ? ");
    $getCoach->bind_param("i", $userid);
    $getCoach->execute();
    $getCoach->store_result();
    $getCoach->bind_result($coachID, $coachName);
    $getCoach->fetch();
}
/*
* If a user makes a request for an appointment, a row is inserted into the database. 
* This will then be available for the coach to approve/reject on the approvals.php page
*
*/
if (isset($_POST['requestAppt'])) {

    $date = $_POST['date'];
    $dateFormatted = date('Y-m-d', strtotime($date));
    $sanitisedDate = $conn->real_escape_string(trim($dateFormatted));

    $time = $_POST['time'];
    $sanitisedTime = $conn->real_escape_string(trim($time));

    $length = $_POST['lengthOfSession'];
    $sanitisedLength = $conn->real_escape_string(trim($length));

    $details = $_POST['sessionDetails'];
    $sanitisedDetails = $conn->real_escape_string(trim($details));

    $findAllConfirmedAppts = "SELECT * FROM webdev_appointments WHERE confirmed = '1'";
    $executeFindAllConfirmedAppts = $conn->query($findAllConfirmedAppts);
    $existingAppt = 0;
    /**
     * Cycles through all confirmed appointments.
     * If it finds one with the same date and time as the requested appt, sets variable to 1 
     * which will then display an error and not write to db.
     */
    while ($row = $executeFindAllConfirmedAppts->fetch_assoc()) {
        $existingDate = $row['date'];
        $existingTime = $row['time'];
        if (($time == $existingTime) && ($sanitisedDate == $existingDate)) {
            $existingAppt = 1;
        }
    }
    /**
     * Checks if appointment for this date and time currently exists AND is confirmed. If so, displays error.
     * Else, still submits as coach can deny request. 
     */
    if ($existingAppt == 0) {



        /**
         * If normal user logged in, writes query as below
         */
        if (isset($userid)) {
            // if not empty, attempts to send query
            if (($dateFormatted != "") && ($time != "") && ($details != "") && $coachID != "") {
                if (strlen($details) < 100) {


                    $pendingAppointment = "INSERT INTO webdev_appointments (coach_id, user_id, date, time, duration, 
            details, confirmed)
        VALUES ('$coachID', '$userid', '$sanitisedDate', '$sanitisedTime', '$sanitisedLength', 
        '$sanitisedDetails', 0);";
                    $executePendingAppointment = $conn->query($pendingAppointment);


                    if (!$executePendingAppointment) {
                        echo $conn->error;
                        $requestError = "Sorry, your request has not been sent. Please check your input and try again.";
                    } else {
                        $requestSuccess = "Your request has been sent.";
                    }
                } else {
                    $requestError = "Sorry, your request has not been sent. Details must be < 100 characters.";
                }
            } else {
                $requestError = "Sorry, your request has not been sent. Please check your input and try again.";
            }
        }

        /**
         * If coach logged in, writes query as below
         */
        if (isset($loggedInCoachID)) {
            $clientForSession = $_POST['clientForSession'];

            $groupForSession = $_POST['groupForSession'];

            /**
             * Ensured a coach has only selected either a client or group, not both.
             * If so, then get details of client/group name.
             * Inserts appointment into the database, set as confirmed, and sends a message
             * to either the group members or client with details of the sessions.
             */
            if (($clientForSession == 0) && ($groupForSession == 0)) {
                $requestError = "Error - select either client or group to make session for.";
            } else if (($clientForSession != 0) && ($groupForSession != 0)) {
                $requestError = "Error - cannot make session for both client or group at the same time.";
            } else if (($clientForSession != 0) && ($groupForSession == 0)) {
                if (($dateFormatted != "") && ($time != "") && ($details != "")) {
                    $getClientName = $conn->prepare("SELECT name from webdev_user_details WHERE user_id = ?");
                    $getClientName->bind_param("i", $clientForSession);
                    $getClientName->execute();
                    $getClientName->store_result();
                    $getClientName->bind_result($clientName);
                    $getClientName->fetch();


                    $pendingAppointment = "INSERT INTO webdev_appointments (coach_id, user_id, date, time, duration, details, confirmed)
            VALUES ('$loggedInCoachID', '$clientForSession', '$sanitisedDate', '$sanitisedTime', '$sanitisedLength', '$sanitisedDetails', 1);";
                    $executePendingAppointment = $conn->query($pendingAppointment);


                    $sendMessage = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, hide) VALUES
                ('$clientForSession', '$loggedInCoachID', 'Appointment - $sanitisedDate at $sanitisedTime', 'Dear $clientName, I have created an appointment for $sanitisedDetails on $sanitisedDate
                at $sanitisedTime. The details of this session are: $sanitisedDetails. I will seen you then!', '$loggedInCoachID', '$clientForSession', 0)";

                    // transaction adapted from online tutorial https://www.youtube.com/watch?v=CNt9HPqDIVc
                    $conn->autocommit(false);

                    $error = array();

                    // attempt to create the appointment
                    $a = $conn->query($pendingAppointment);
                    if ($a == false) {
                        $requestError = "There was creating the appointment.";
                        array_push($error, $requestError);
                    }
                    // attempt to send message to user
                    $b = $conn->query($sendMessage);
                    if ($b == false) {
                        $requestError = "There was an creating a message for the user.";
                        array_push($error, $requestError);
                    }

                    /**
                     * If error array is not empty, one of the queries in the transaction 
                     * has failed and it is rolled back. Else, commits the transaction.
                     */
                    if (!empty($error)) {
                        $conn->rollback();
                        $requestError = "There was an error..";
                    } else {
                        $conn->commit();
                        $requestSuccess = "Appointment successfully approved.";
                    }
                } else {
                    $requestError = "Sorry, your request has not been sent. Please check your input and try again.";
                }
            } else if (($groupForSession != 0) && ($clientForSession == 0)) {
                if (($dateFormatted != "") && ($time != "") && ($details != "")) {


                    $getGroupUsers = $conn->prepare("SELECT group_name, member_one, member_two, member_three, member_four
                 FROM webdev_groups WHERE id = ?");
                    $getGroupUsers->bind_param("i", $groupForSession);
                    $getGroupUsers->execute();
                    $getGroupUsers->store_result();
                    $getGroupUsers->bind_result($groupName, $memberOne, $memberTwo, $memberThree, $memberFour);
                    $getGroupUsers->fetch();

                    $pendingAppointment = "INSERT INTO webdev_appointments (coach_id, group_id, date, time, duration, details, confirmed)
                VALUES ('$loggedInCoachID', '$groupForSession', '$sanitisedDate', '$sanitisedTime', '$sanitisedLength', '$sanitisedDetails', 1);";

                    $sendMessage = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, group_id, hide) VALUES
                ($memberOne, '$loggedInCoachID', 'New session for $groupName - $sanitisedDate', 'Dear member of $groupName, there will be a new session for $groupName on $sanitisedDate
                at $sanitisedTime. The details for this session are: $sanitisedDetails. I hope to see you there.', $loggedInCoachID, $memberOne, $groupForSession, 0)";

                    $sendMessageTwo = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, group_id, hide) VALUES
                ($memberTwo, $loggedInCoachID, 'New session for $groupName - $sanitisedDate', 'Dear member of $groupName, there will be a new session for $groupName on $sanitisedDate
                at $sanitisedTime. The details for this session are: $sanitisedDetails. I hope to see you there.', $loggedInCoachID, $memberTwo, $groupForSession, 0)";

                    $sendMessageThree = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, group_id, hide) VALUES
                ($memberThree, $loggedInCoachID, 'New session for $groupName - $sanitisedDate', 'Dear member of $groupName, there will be a new session for $groupName on $sanitisedDate
                at $sanitisedTime. The details for this session are: $sanitisedDetails. I hope to see you there.', $loggedInCoachID, $memberThree, $groupForSession, 0)";

                    $sendMessageFour = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user,group_id, hide) VALUES
                ($memberFour, $loggedInCoachID, 'New session for $groupName - $sanitisedDate', 'Dear member of $groupName, there will be a new session for $groupName on $sanitisedDate
                at $sanitisedTime. The details for this session are: $sanitisedDetails. I hope to see you there.', $loggedInCoachID, $memberFour, $groupForSession, 0)";

                    $conn->autocommit(false);

                    $groupError = array();

                    // attempt to create the appointment
                    $a = $conn->query($pendingAppointment);
                    if ($a == false) {
                        $requestError = "There was creating the appointment.";
                        array_push($groupError, $requestError);
                    }
                    // attempt to create message for group member 1
                    $b = $conn->query($sendMessage);
                    if ($b == false) {
                        $requestError = "There was an error creating the message for the first group member";
                        array_push($groupError, $requestError);
                    }
                    // attempt to create message for group member 2
                    $c = $conn->query($sendMessageTwo);
                    if ($c == false) {
                        $requestError = "There was an error creating the message for the second group member";
                        array_push($groupError, $requestError);
                    }
                    // attempt to create message for group member 3
                    $d = $conn->query($sendMessageThree);
                    if ($d == false) {
                        $requestError = "There was an error creating the message for the third group member";
                        array_push($groupError, $requestError);
                    }
                    // attempt to create message for group member 4
                    $e = $conn->query($sendMessageFour);
                    if ($e == false) {
                        $requestError = "There was an error creating the message for the fourth group member";
                        array_push($groupError, $requestError);
                    }
                    /**
                     * If error array is not empty, one of the queries in the transaction 
                     * has failed and it is rolled back. Else, commits the transaction.
                     */
                    if (!empty($groupError)) {
                        $conn->rollback();
                    } else {
                        //commit here
                        $conn->commit();
                        $requestSuccess = "Group appointment successfully created.";
                    }
                } else { // if fields are empty for client session
                    $requestError = "Sorry, your request has not been sent. Please check your input and try again.";
                }
            } else { // if fields are empty for group session
                $requestError = "Sorry, your request has not been sent. Please check your input and try again.";
            }
        }
    } else { // if coach has an existing, confirmed, appointment at this time
        $requestError = "Sorry, your request has not been sent - coach is unavailable for this date and time. 
        Please try a different time and/or date.";
    }
}

/**
 * If the user clicks on the cancel appointment button, attempts to cancel the appointment 
 * by deleting it from the database
 */
if (isset($_POST['cancelAppts'])) {
    $trainingSessionToDelete = $_POST['sessionToDelete'];
    $getInfoOnSessionForMessage = $conn->prepare("SELECT user_id, group_id, date, time, coach_id, confirmed FROM webdev_appointments
    WHERE id = ?");
    $getInfoOnSessionForMessage->bind_param("i", $trainingSessionToDelete);
    $getInfoOnSessionForMessage->execute();
    $getInfoOnSessionForMessage->store_result();
    $getInfoOnSessionForMessage->bind_result($user_id_session_to_delete, $group_id_session_to_delete, $deleteDate, $deleteTime, $coachID, $confirmed);
    $getInfoOnSessionForMessage->fetch();


    /**
     * Creates the query depending on what role is logged in.
     * If a normal user, only allows deletion where user owns that appointment.
     * If a coach, can delete if they are the coach
     */
    if (isset($userid)) {
        $deleteAppointment = "DELETE FROM webdev_appointments WHERE id='$trainingSessionToDelete' AND user_id = '$userid'";
    } else if (isset($loggedInCoachID)) {
        $deleteAppointment = "DELETE FROM webdev_appointments WHERE id='$trainingSessionToDelete' AND coach_id = '$loggedInCoachID'";
    }

    /**
     * If deleting a group appointment, different process and some extra steps to do 
     *  Need to find the appt based on the group_id and then you 
     *  need to message all group members,
     */
    if ($group_id_session_to_delete != null) {
        $getGroupUsers = $conn->prepare("SELECT group_name, member_one, member_two, member_three, member_four
        FROM webdev_groups WHERE id = ?");
        $getGroupUsers->bind_param("i", $group_id_session_to_delete);
        $getGroupUsers->execute();
        $getGroupUsers->store_result();
        $getGroupUsers->bind_result($groupName, $memberOne, $memberTwo, $memberThree, $memberFour);
        $getGroupUsers->fetch();

        $sendMessage = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, group_id, hide) VALUES
       ($memberOne, $loggedInCoachID, 'Cancelled session for $groupName - $deleteDate', 'Dear member of $groupName, the session scheculed for $groupName on $deleteDate
       at $deleteTime has been cancelled. Keep an eye on your inbox/dashboard for further upcoming events of which I hope to see you at.  \n
       Kind regards, $nameOfCoach.', $loggedInCoachID, $memberOne, $group_id_session_to_delete, 0)";

        $sendMessageTwo = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, group_id, hide) VALUES
       ($memberTwo, $loggedInCoachID, 'Cancelled session for $groupName - $deleteDate', 'Dear member of $groupName, the session scheculed for $groupName on $deleteDate
       at $deleteTime has been cancelled. Keep an eye on your inbox/dashboard for further upcoming events of which I hope to see you at. \n
       Kind regards, $nameOfCoach.', $loggedInCoachID, $memberTwo, $group_id_session_to_delete, 0)";

        $sendMessageThree = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, group_id, hide) VALUES
       ($memberThree, $loggedInCoachID, 'Cancelled session for $groupName - $deleteDate', 'Dear member of $groupName, the session scheculed for $groupName on $deleteDate
       at $deleteTime has been cancelled. Keep an eye on your inbox/dashboard for further upcoming events of which I hope to see you at. \n
       Kind regards, $nameOfCoach.', $loggedInCoachID, $memberThree, $group_id_session_to_delete, 0)";

        $sendMessageFour = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user,group_id, hide) VALUES
       ($memberFour, $loggedInCoachID, 'Cancelled session for $groupName - $deleteDate', 'Dear member of $groupName, the session scheculed for $groupName on $deleteDate
       at $deleteTime has been cancelled. Keep an eye on your inbox/dashboard for further upcoming events of which I hope to see you at. \n
       Kind regards, $nameOfCoach.', $loggedInCoachID, $memberFour, $group_id_session_to_delete, 0)";

        $conn->autocommit(false);

        $error = array();

        $a = $conn->query($deleteAppointment);
        if ($a == false) {
            $deleteError = "There was an error deleting the appointment";
            array_push($error, $deleteError);
            echo $conn->error;
        }
        $b = $conn->query($sendMessage);
        if ($b == false) {
            $deleteError = "There was an error creating a message for the first group member";
            array_push($error, $deleteError);
            echo $conn->error;
        }

        $c = $conn->query($sendMessageTwo);
        if ($c == false) {
            $deleteError = "There was an error creating a message for the second group member";
            array_push($error, $deleteError);
            echo $conn->error;
        }

        $d = $conn->query($sendMessageThree);
        if ($d == false) {
            $deleteError = "There was an error creating a message for the third group member";
            array_push($error, $deleteError);
            echo $conn->error;
        }

        $e = $conn->query($sendMessageFour);
        if ($e == false) {
            $deleteError = "There was an error creating a message for the fourth group member";
            array_push($error, $deleteError);
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
            //commit
            $conn->commit();
            $successfulDelete = "Appointment successfully cancelled.";
        }
    } else {
        $doNotSend = 0; // variable to track if a message should be sent (if not a confirmed appt, no need to message)
        if (isset($loggedInCoachID)) {

            $sendMessage = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, hide) VALUES
        ($user_id_session_to_delete, '$loggedInCoachID', 'Cancelled session for $deleteDate', 'Dear client, the session scheculed for $deleteDate
        at $deleteTime has been cancelled. Please feel free to submit additional appointment requests.  \n
       Kind regards, $nameOfCoach.', $loggedInCoachID, $user_id_session_to_delete, 0)";
        } else if (isset($userid)) {
            if ($confirmed == 1) {

                $sendMessage = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, hide) VALUES
        ($coachID, $userid, 'Cancelled session for $deleteDate', 'I have cancelled my appointment on $deleteDate at $deleteTime. \n
        Apologies for any inconvenience.', $coachID, $userid, 0)";
            } else {
                $doNotSend = 1; // if not cancelling a confirmed appt there is no need to message coach.
            }
        }

        $conn->autocommit(false);

        $singleDeleteError = array();

        $a = $conn->query($deleteAppointment);
        if ($a == false) {
            array_push($singleDeleteError, 'Problem pushing to db');
            $deleteError = "There was an error..";
        }
        // if 0, sends message.
        if ($doNotSend == 0) {

            $b = $conn->query($sendMessage);
            if ($b == false) {
                array_push($singleDeleteError, 'Problem pushing to db');
                $deleteError = "There was an error..";
            }
        }
        /**
         * If error array is not empty, one of the queries in the transaction 
         * has failed and it is rolled back. Else, commits the transaction.
         */
        if (!empty($singleDeleteError)) {
            $conn->rollback();
            $deleteError = "There was an error..";
        } else {
            //commit
            $conn->commit();
            $successfulDelete = "Appointment successfully cancelled.";
        }
    }
}


/**
 *Grabs all confirmed appoints for the user to populate the calendar
 *
 * Calendar from https://github.com/edlynvillegas/evo-calendar
 */
if (isset($userid)) {


    $grabConfirmedAppointsments = "SELECT * FROM webdev_appointments WHERE user_id = '$userid' AND confirmed = '1';";

    $getConfirmedAppts = $conn->query($grabConfirmedAppointsments);

    if (!$getConfirmedAppts) {
        echo $conn->error;
    }


    echo "<script> myEvents = [

        ";

    while ($row = $getConfirmedAppts->fetch_assoc()) {

        $date = $row['date'];
        $time = $row['time'];
        $duration = $row['duration'];
        $details = $row['details'];

        echo "{ name: 'Session with $coachName at $time for $duration. <br>Details: $details', date: '$date', type: 'event', everyYear: false },
        ";
    }

    $getGroupAppointments = "SELECT date, time, group_id, duration, details, group_name
    FROM webdev_appointments 
    INNER JOIN webdev_groups
    ON webdev_appointments.group_id = webdev_groups.id
    WHERE webdev_groups.member_one = $userid OR webdev_groups.member_two = $userid
    OR webdev_groups.member_three = $userid OR webdev_groups.member_four = $userid 
    AND confirmed = '1'
    ;";

    $executeGetGroupAppointments = $conn->query($getGroupAppointments);

    if (!$executeGetGroupAppointments) {
        echo $conn->error;
    }



    while ($row = $executeGetGroupAppointments->fetch_assoc()) {

        $date = $row['date'];
        $time = $row['time'];
        $duration = $row['duration'];
        $details = $row['details'];
        $groupName = $row['group_name'];


        echo "{ name: 'Session with $groupName at $time for $duration. <br>Details: $details', date: '$date', type: 'event', everyYear: false },";
    }

    echo " ]
</script>";
} else if (isset($loggedInCoachID)) {
    $grabConfirmedAppointsments = "SELECT date, time, duration, details, name
    FROM webdev_appointments 
    INNER JOIN webdev_user_details
    ON webdev_appointments.user_id = webdev_user_details.user_id
    WHERE coach_id = '$loggedInCoachID' 
    AND confirmed = '1'
    ;";

    $getConfirmedAppts = $conn->query($grabConfirmedAppointsments);

    if (!$getConfirmedAppts) {
        echo $conn->error;
    }

    echo "<script> myEvents = [
    
            ";

    while ($row = $getConfirmedAppts->fetch_assoc()) {

        $date = $row['date'];
        $time = $row['time'];
        $duration = $row['duration'];
        $details = $row['details'];
        $clientName = $row['name'];


        echo "{ name: 'Session with $clientName at $time for $duration. <br>Details: $details', date: '$date', type: 'event', everyYear: false },";
    }



    $getGroupAppointments = "SELECT date, time, group_id, duration, details, group_name
          FROM webdev_appointments 
          INNER JOIN webdev_groups
          ON webdev_appointments.group_id = webdev_groups.id
          WHERE coach_id = '2' 
          AND confirmed = '1'
          ;";

    $executeGetGroupAppointments = $conn->query($getGroupAppointments);

    if (!$executeGetGroupAppointments) {
        echo $conn->error;
    }



    while ($row = $executeGetGroupAppointments->fetch_assoc()) {

        $date = $row['date'];
        $time = $row['time'];
        $duration = $row['duration'];
        $details = $row['details'];
        $groupName = $row['group_name'];


        echo "{ name: 'Session with $groupName at $time for $duration. <br>Details: $details', date: '$date', type: 'event', everyYear: false },";
    }

    echo " ]
      </script>";
}

/**
 * Allows the coach to edit a performance log of a client from a session. 
 * They can either edit what the user said, or add their own comments. 
 */
if (isset($_POST['editLog'])) {
    $logToEdit = $_POST['logToManage'];

    if ($logToEdit != null) {


        $getCurrentLog = $conn->prepare("SELECT appointment_id, user_comments, user_rating, coach_comments, coach_rating, date, time, name
     FROM webdev_appointments_logs 
     INNER JOIN webdev_appointments
     ON webdev_appointments_logs.appointment_id = webdev_appointments.id
     INNER JOIN webdev_user_details
     ON webdev_appointments.user_id = webdev_user_details.user_id
     WHERE webdev_appointments_logs.id = ? ");
        $getCurrentLog->bind_param("i", $logToEdit);
        $getCurrentLog->execute();
        $getCurrentLog->store_result();
        $getCurrentLog->bind_result($appointmentID, $userComment, $userRating, $coachComment, $coachRating, $date, $time, $userName);
        $getCurrentLog->fetch();

        echo "  <div class='modal is-active' id='editLog'>
        <div class='modal-background'></div>
        <div class='modal-card'>
          <header class='modal-card-head'>
            <p class='modal-card-title'>Edit User Log</p>
            <button class='delete cancelUpdate' aria-label='close' ></button>
          
          </header>
         

         
        
        
        <section class='modal-card-body'>
   
      <form action='appointments.php' method='POST' id='coachLog'>
        <div class='field'>
        <input type='hidden' id='logID' name='logID' value='$logToEdit'>
          <label class='label'>Coach comments: </label>
          <div class='control'>
          ";
?>
        <input class='input' type='text' value="<?php echo $coachComment ?>" id='coachComments' name='coachComments'>
        </div>
        <?php
        echo "
        <p class='help is-danger' id='coachCommentsWarn'> </p>
        </div>
        
        <div class='field'>

        <label class='label'>Coach rating (out of 5): </label>
        <div class='control'>
        
          <input class='input' type='number' value='$coachRating' id='coachRating'  name='coachRating'>
        </div>
        <p class='help is-danger' id='coachRatingWarn'> </p>
       </div>
        <div class='field'>

        <label class='label'>Client comments: </label>
        <div class='control'>";
        ?>
        <input class='input' type='text' value="<?php echo $userComment ?>" id='userComments' name='userComments'>
<?php
        echo "
        </div>
         
     <p class='help is-danger' id='userCommentsWarn'> </p>
      </div>

      <div class='field'>
        <label class='label'>Client rating (out of 5): </label>
        <div class='control'>
          <input class='input' type='number' value='$userRating' name='userRating' readonly>
        </div>
      </div>
      <div class='field'>

      <input type='hidden' name='currentUserComment' value='$userComment'>
      <input type='hidden' name='currentComment' value='$coachComment'>
      <input type='hidden' name='currentRating' value='$coachRating'>
      
      <footer class='modal-card-foot'>
      <input type='submit' class='button is-success logIfValid' id='logSubmitBut' value='Save changes' name='logSubmit'>

    </footer>


      </form>
    </section>
    </div>
    </div>";
    } else {
        $logError = "Log could not be deleted. Please try again.";
    }
}

/**
 * If the edit log form has been submitted, attempts to validate it.
 * If successfully validates, updates value in database with posted data.
 */
if (isset($_POST['logSubmit'])) {
    $logToUpdate = $_POST['logID'];
    $sanitisedlogToUpdate = $conn->real_escape_string(trim($logToUpdate));
    $userComments = $_POST['userComments'];
    $sanitisedUserComments = $conn->real_escape_string(trim($userComments));
    $coachRating = $_POST['coachRating'];
    $sanitisedCoachRating = $conn->real_escape_string(trim($coachRating));
    $coachComments = $_POST['coachComments'];
    $sanitisedCoachComments = $conn->real_escape_string(trim($coachComments));

    $currentCoachComment = $_POST['currentComment'];
    $currentCoachRating = $_POST['currentRating'];
    $currentUserComment = $_POST['currentUserComment'];

    //come here


    if (!ctype_digit($sanitisedCoachRating)) { // checks the rating is a number
        $logError = "Your log update has not been submitted - rating not numeric.";
    } else if ($sanitisedCoachRating < 1 || $sanitisedCoachRating > 5) { // checks it is within the range
        $logError = "Your log update has not been submitted - rating not within range (1-5).";
    } else if (($currentCoachComment == $coachComments) && ($currentCoachRating == $coachRating)
        && ($currentUserComment == $userComments)
    ) {
        $logError = "Your log update has not been submitted - no fields have been changed";
    } else if (strlen($_POST['userComments']) > 100) {
        $logError = "User comment too long - must be < 100 characters.";
    } else if (strlen($_POST['coachComments']) > 100) {
        $logError = "Coach comment too long - must be < 100 characters.";
    } else {
        if ($logToUpdate != null) { // checks the log update is not empty


            $updateLog = "UPDATE webdev_appointments_logs
            SET user_comments = '$sanitisedUserComments', coach_rating = '$sanitisedCoachRating', 
            coach_comments = '$sanitisedCoachComments'
            WHERE id = '$logToUpdate'";

            $executeUpdateLog = $conn->query($updateLog);
            if (!$executeUpdateLog) {
                echo $conn->error;
                $logError = "Log could not be updated. Please try again.";
            }

            $logSuccess = "Log successfully updated.";
        } else {
            $logError = "Log could not be updated. Please try again.";
        }
    }
}

/**
 * If the coach has selected a log to delete from the dropdown list,
 * attempts to process the deletion
 */
if (isset($_POST['deleteLog'])) {
    $logToDelete = $_POST['logToManage'];
    if ($logToDelete != null) { // ensures a vale has actually been sent


        $deleteLog = "DELETE FROM webdev_appointments_logs WHERE id='$logToDelete'";
        $executeDeleteLog = $conn->query($deleteLog);
        if (!$executeDeleteLog) {
            echo $conn->error;
            $logError = "Log could not be deleted. Please try again.";
        }

        $logSuccess = "Log successfully deleted.";
    } else {
        $logError = "Log could not be deleted. Please try again.";
    }
}
?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gymafi | Appointments</title>
    <link rel="stylesheet" href="styles/evo-calendar.css" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="styles/bulma.css" rel="stylesheet">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
    <link href="styles/gui.css" rel="stylesheet">
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="script/evo-calendar.js"></script>
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
                <a class='navbar-item has-text-white' href='dashboard.php'>
                    Dashboard
                </a>


                <a class='navbar-item has-background-dark has-text-white has-background-primary' href='appointments.php'>
                    Appointments
                </a>

                <a class='navbar-item has-background-dark has-text-white ' href='inbox.php'>
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
                <a class='navbar-item has-text-black ' href='dashboard.php'>
                    Dashboard
                </a>


                <a class='navbar-item has-text-black' href='admin/approvals.php'>
                    Approvals
                </a>

                <a class='navbar-item has-text-black has-background-warning' href='appointments.php'>
                    Appointments
                </a>


                <a class='navbar-item has-text-black ' href='admin/groups.php'>
                    Groups
                </a>

                <a class='navbar-item has-text-black  ' href='inbox.php'>
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





    /**
     * If user's account has not been approved by their desired coach, displays an error image/message and does not show the
     * webpage as an approved use would see.
     * User can click on the button to return to the dashboard.
     */

    if (isset($userid)) {

        $checkIfApproved = $conn->prepare("SELECT approved FROM webdev_users WHERE id = ? ");
        $checkIfApproved->bind_param("i", $userid);
        $checkIfApproved->execute();
        $checkIfApproved->store_result();
        $checkIfApproved->bind_result($isApproved);
        $checkIfApproved->fetch();
    }


    if ($isApproved == 1) {

        /*
        * This section to allow the user to book an appointment.
        * They will select a date and time from respective pickers, and can add notes as to what they want to cover in the session.
        * The date and time are read only to avoid tampering with, as well as the coach name.
        */
        echo "<div id='dashColumns'>
            <div class='columns'>
                <div class='column is-3'>
                    <article class='message is-link'>
                        <div class='message-header'>";
        if (isset($userid)) {
            echo "Request an appointment";
        } else if (isset($loggedInCoachID)) {
            echo "Create an appointment";
        }
    ?>


        </div>

        <div class='message-body'>
            <form action='appointments.php' method='POST' id='makeAppts'>
                <p> Date: <input class='input' type='text' id='datepicker' name='date' readonly></p>
                <p class='help is-danger' id='dateWarn'> </p>
                <p>Time: <input class='input' type='text' id='timepicker' name='time' readonly></p>
                <p class='help is-danger' id='timeWarn'> </p>
                <p> Duration: <div class='select'>
                        <select name='lengthOfSession'>
                            <option>30 Minutes</option>
                            <option>1 Hour</option>
                            <option>90 Minutes</option>
                            <option>2 hours</option>
                        </select>
                    </div>
                    <?php
                    /**
                     * If a normal client is logged in, simply shows their coach name for who the appointment will be with.
                     * If a coach is logged in, shows two dropdowns list of both clients and groups they can make appointments for.
                     * A coach cannot make an appointment for both an individual client and group at the same time - an error will be displayed
                     * and the appointment will not be sent to the database.
                     */
                    if (isset($userid)) {
                        echo " <p>Your Coach:<input class='input' type='text' id='coach' value='$coachName' name='coachName' readonly></p>";
                    } else if (isset($loggedInCoachID)) {
                        echo "<p>With client:</p>
                                    <div class='select'>
                                        <select name='clientForSession'>
                                            <option value='0'>None</option>";
                        $getClientsForCoach = "SELECT user_id, name FROM webdev_user_details WHERE coach = $loggedInCoachID
                                            ORDER BY name ASC";
                        $executeGetClientsForCoach = $conn->query($getClientsForCoach);
                        if (!$executeGetClientsForCoach) {
                            echo $conn->error;
                        }

                        while ($row = $executeGetClientsForCoach->fetch_assoc()) {
                            $clientID = $row['user_id'];
                            $clientName = $row['name'];
                            echo "<option value='$clientID'>", htmlentities($clientName, ENT_QUOTES), "</option>";
                        }
                        echo "
                                        </select>
                                    </div>

                                    <p> With Group:</p>
                                    <div class='select'>
                                        <select name='groupForSession'>
                                            <option value='0'>None</option>";
                        $getGroups = "SELECT id, group_name FROM webdev_groups WHERE coach = $loggedInCoachID
                                            ORDER BY group_name ASC";
                        $executeGetGroups = $conn->query($getGroups);
                        if (!$executeGetGroups) {
                            echo $conn->error;
                        }

                        while ($row = $executeGetGroups->fetch_assoc()) {
                            $groupID = $row['id'];
                            $groupName = $row['group_name'];
                            echo "<option value='$groupID'>", htmlentities($groupName, ENT_QUOTES), "</option>";
                        }
                        echo "
                                        </select>
                                    </div>";
                    }
                    echo "
                                    <p>Session details: : <input class='input' type='text' id='apptDesc' placeholder='Normal workout focusing on cardio' name='sessionDetails'></p>
                                    <p class='help is-danger' id='descWarn'> </p>
                                    ";

                    echo "<div id='ajaxResult'> </div>";
                    if (isset($requestError)) {
                        echo "<p class='displayError'>$requestError</p>";
                    } else if (isset($requestSuccess)) {
                        echo "<p class='displaySucc'>$requestSuccess</p>";
                    }
                    echo "<p class='apptButton'><input type='submit' class='button is-primary appointmentSubmit' value='Request Appointment' name='requestAppt'> </p>
                            ";
                    ?>
            </form>

        </div> <!-- end of message body-->

        </article>

        <!-- this section allows the user to delete any appointments they have submitted, whether they are confirmed or not by the coach -->
        <div class='columns'>
            <div class='column'>
                <article class='message is-danger' id='refreshCancel'>
                    <div class='message-header'>

                        Cancel an appointment

                    </div>

                    <div class='message-body' id='containsDeleteForm cancelAppts'>
                        <form action='appointments.php' method='POST'>
                            <p> Select an appointment to cancel: </p>
                            <div class='select'>
                                <select name='sessionToDelete'>
                                    <?php
                                    $todaysDate = date('Y-m-d'); // variable to ensure the user can only make a log for sessions which have already happened.
                                    /**
                                     * Grabs all of the appointments for the user, both pending and confirmed. Allows the user to unilaterally cancel an appointment.
                                     */
                                    if (isset($userid)) {


                                        $getAllAppointments = "SELECT * FROM webdev_appointments 
                                        WHERE user_id = '$userid' 
                                        AND webdev_appointments.date >= '$todaysDate'
                                        ORDER BY date ASC";
                                        $executeGetAllAppointments = $conn->query($getAllAppointments);
                                        if (!$executeGetAllAppointments) {
                                            echo $conn->error;
                                        } else {
                                            while ($row = $executeGetAllAppointments->fetch_assoc()) {
                                                $apptID = $row['id'];
                                                $dateForCancel = $row['date'];
                                                $newDateFormat = date('d M y', strtotime($dateForCancel));
                                                $time = $row['time'];
                                                $details = $row['details'];
                                                $confirmed = $row['confirmed'];
                                                if ($confirmed == true) {
                                                    $isConfirmed = "(Approved)";
                                                } else {
                                                    $isConfirmed = "(Pending)";
                                                }
                                                echo "<option value='$apptID'> ", htmlentities($newDateFormat, ENT_QUOTES), " | ", htmlentities($time, ENT_QUOTES), " 
                                                |  ", htmlentities($details, ENT_QUOTES), " ", htmlentities($isConfirmed, ENT_QUOTES), "</option>";
                                            }
                                        }
                                        /**
                                         * Displays all appointments for a coach. Allows them to cancel any appointment, including group appointments.
                                         */
                                    } else if (isset($loggedInCoachID)) {
                                        $getAllAppointments = "SELECT id, date, time, details, confirmed, group_id, user_id
                                    FROM webdev_appointments
                                    WHERE coach_id = $loggedInCoachID
                                    AND webdev_appointments.date >= '$todaysDate'
                                    ORDER BY date ASC";
                                        $executeGetAllAppointments = $conn->query($getAllAppointments);
                                        if (!$executeGetAllAppointments) {
                                            echo $conn->error;
                                        }

                                        while ($row = $executeGetAllAppointments->fetch_assoc()) {
                                            $apptID = $row['id'];
                                            $dateForCancel = $row['date'];
                                            $newDateFormat = date('d M y', strtotime($dateForCancel));
                                            $time = $row['time'];
                                            $details = $row['details'];
                                            $confirmed = $row['confirmed'];
                                            $userIDOfSession = $row['user_id'];
                                            $groupIDOfSession = $row['group_id'];
                                            if ($confirmed == true) {
                                                $isConfirmed = "(Approved)";
                                            } else {
                                                $isConfirmed = "(Pending)";
                                            }
                                            // for group appointments, shows name of group opposed to client name
                                            if ($groupIDOfSession != null) {
                                                $getGroupName = $conn->prepare("SELECT group_name
                                    FROM webdev_groups
                                    WHERE webdev_groups.id = ? ");
                                                $getGroupName->bind_param("i", $groupIDOfSession);
                                                $getGroupName->execute();
                                                $getGroupName->store_result();
                                                $getGroupName->bind_result($groupName);
                                                $getGroupName->fetch();

                                                echo "<option value='$apptID'> ", htmlentities($newDateFormat, ENT_QUOTES), " ", htmlentities($time, ENT_QUOTES), " 
                                                | ", htmlentities($groupName, ENT_QUOTES), "  ", htmlentities($isConfirmed, ENT_QUOTES), "</option>";
                                                // for individual client appointments
                                            } else if ($userIDOfSession != null) {
                                                $getUserName = $conn->prepare("SELECT name
                                    FROM webdev_user_details
                                    WHERE webdev_user_details.user_id = ? ");
                                                $getUserName->bind_param("i", $userIDOfSession);
                                                $getUserName->execute();
                                                $getUserName->store_result();
                                                $getUserName->bind_result($userName);
                                                $getUserName->fetch();

                                                echo "<option value='$apptID'> ", htmlentities($newDateFormat, ENT_QUOTES), " ", htmlentities($time, ENT_QUOTES), " 
                                                | ", htmlentities($userName, ENT_QUOTES), " ", htmlentities($isConfirmed, ENT_QUOTES), "</option>";
                                            }
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
                                    <p class='apptButton'><input type='submit' id='cancelApp' class='button is-danger appointmentCancel' value='Cancel Appointment' onclick="return confirm('Are you sure you wish to cancel this appointment?')" name='cancelAppts'> </p>
                        </form>

                    </div> <!-- end of message body-->
                </article>
            </div>


        </div>
        <?php
        if (isset($loggedInCoachID)) {

        ?>
            <!-- Allows the coach to edit a performance log submitted by a user,
can edit the user's comments or add their own and their own rating -->
            <div class='columns'>
                <div class='column'>
                    <article class='message is-warning' id='manageLog'>
                        <div class='message-header'>

                            Manage a Peformance Log

                        </div>

                        <div class='message-body' id='containsDeleteForm '>
                            <form action='appointments.php' method='POST'>
                                <p> Select a log to manage: </p>
                                <div class='select'>
                                    <select name='logToManage'>
                                        <?php
                                        /**
                                         * Allows the coach to edit the user's performance log, as well as add their own comments/rating that will be displayed to the user.
                                         */
                                        $getAllLogs = "SELECT webdev_appointments_logs.id, webdev_appointments_logs.appointment_id, webdev_appointments_logs.user_comments, 
                                        webdev_appointments_logs.user_rating, webdev_appointments.date, webdev_appointments.time, webdev_appointments.details,
                                        webdev_appointments.user_id, webdev_user_details.name
                                        FROM webdev_appointments_logs 
                                        INNER JOIN webdev_appointments 
                                        ON webdev_appointments_logs.appointment_id =  webdev_appointments.id
                                        INNER JOIN webdev_user_details 
                                        ON webdev_appointments.user_id = webdev_user_details.user_id
                                        WHERE  coach_id = '$loggedInCoachID'";
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
                                                $userName = $row['name'];

                                                echo "<option value='$logID'> ", htmlentities($userName, ENT_QUOTES), " 
                                                ( ", htmlentities($apptDate, ENT_QUOTES), " |", htmlentities($apptTime, ENT_QUOTES), ")</option>";
                                            }
                                        }

                                        echo "</select>";
                                        if (isset($logError)) {
                                            echo "<p class='displayError'>$logError</p>";
                                        } else if (isset($logSuccess)) {
                                            echo "<p class='displaySucc'>$logSuccess</p>";
                                        }

                                        ?>
                                        <p class='apptButton'>
                                            <input type='submit' id='editLog' class='button is-link logManage' value='Edit Log' name='editLog'>
                                            <input type='submit' id='deleteLog' class='button is-danger logManage' value='Delete Log' onclick="return confirm(' Are you sure you wish to delete this log?')" name='deleteLog'>
                                        </p>
                            </form>

                        </div> <!-- end of message body-->
                    </article>
                </div>
            </div>


        <?php
        }
        ?>

        </div><!-- end of column-->



        <!-- Displays a calendar with all of the user's upcoming confirmed events. 
    The user can click on the date to see a list of their upcoming events with a short description of the session 
    including date, time, who it is with and a small description they entered upon creating the session request-->
        <div class='column is-7' id='rightColumns'>
            <article class='message is-dark'>
                <div class='message-header'>
                    <p class='profileBadgeText'>
                        <h1 class='title calendarHead'>Your Schedule</h1>
                    </p>

                </div>
                <div class='message-body'>
                    <div id='evoCalendar'></div>

                </div> <!-- end of message body-->
            </article>



        </div><!-- end of columns-->
        </div><!-- end of columns-->


        </div>
        </div>
        </div>
        </div>
        </div>




        </div><!-- end of package info-->


    <?php

    } else {
        /**
         * Error symbol/message that is displayed to the user if they try to access the page without their account having been approved, 
         * rather than simply kicking them back to the dashboard without warning. 
         */
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

    </div><!-- end of columns-->


    </div>
    </div>

    <?php


    ?>
    <!--Page footer-->
    <div class='myFooter'>
        <footer class='footer has-background-dark alsoMyFooter'>
            <div class='content has-text-centered has-text-white'>
                <p>
                    <span id='boldFoot'>CSC7062 Project</span> by Jordan Brown (40282125).
                </p>
            </div>
        </footer>
    </div>


    <script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
</body>


</html>