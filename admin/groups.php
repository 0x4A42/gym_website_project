<?php
session_start();
include("../conn.php");

/**
 * Checks the logged in user is a coach
 * If a normal user logged in, kicks them to dashboard.
 * If not logged in at all, kicks them to the login page.
 */
if (isset($_SESSION['gymafi_coachid'])) {
    $loggedInCoachId = $_SESSION['gymafi_coachid'];
} else if (isset($_SESSION['gymafi_userid'])) {
    header("location: ../dashboard.php");
} else if (isset($_SESSION['gymafi_superadmin'])) {
    header("location: superadmin.php");
} else {
    header("location: ../login.php");
}

/**
 * If the coach clicks on the create button, displays a modal 
 * that allows them to create a group.
 * They can enter a group name, 4 group members and three group sessions 
 * which represent three group activities that would occur.
 */
if (isset($_POST['createGrp'])) {
    echo "
        <!-- modal for createing a new group-->
  
    <div class='modal is-active' id='createNewGrp'>
    <div class='modal-background '></div>
    <div class='modal-card'>
      <header class='modal-card-head'>
        <p class='modal-card-title'>Create A New Group</p>
        <button class='delete cancelUpdate' aria-label='close' ></button>
      
      </header>
     
      <section class='modal-card-body'>
        
        <form action='groups.php' method='POST' id='newGroupForm'>
          <div class='field'>
            <label class='label'>Group Name</label>
            <div class='control'>
              <input class='input' id='newGroupName' type='text' placeholder='Cardio Club' name='newGroupName'>
            </div>
            <p class='help is-danger' id='newGroupNameWarn'> </p>
          </div>
  
          <div class='field'>
          <label class='label'>Group description: </label>
          <div class='control'>
            <input class='input' id='newGroupDesc' type='text' placeholder='A group to do cardio exercises' name='newGroupDesc'>
          </div>
          <p class='help is-danger' id='newGroupDescWarn'> </p>
        </div>
  
<!-- Displays all current members for this coach to create the group out of -->
    <div class='field'>
     <label class='label'>Select group members: </label>
     <div class='is-grouped control select'>
       <select name='selectGroupMemberOne'> 
       <option value='0'> None </option>
       ";
    $getUsers = "SELECT user_id, name FROM webdev_user_details
       WHERE coach = '$loggedInCoachId'
       ORDER BY name ASC;";
    $executeGetUsers = $conn->query($getUsers);
    if (!$executeGetUsers) {
        echo $conn->error;
    }
    while ($row = $executeGetUsers->fetch_assoc()) {

        $userName = $row['name'];
        $userid = $row['user_id'];

        echo "<option value='$userid'>$userName</option>";
    }


    echo "
        </select>
      </div>



     <div class='is-grouped control select'>
       <select name='selectGroupMemberTwo'> 
       <option value='0'> None </option>
       ";
    $getUsers = "SELECT user_id, name FROM webdev_user_details
       WHERE coach = '$loggedInCoachId'
       ORDER BY name ASC;";
    $executeGetUsers = $conn->query($getUsers);
    if (!$executeGetUsers) {
        echo $conn->error;
    }
    while ($row = $executeGetUsers->fetch_assoc()) {

        $userName = $row['name'];
        $userid = $row['user_id'];

        echo "<option value='$userid'>$userName</option>";
    }


    echo "
        </select>
      </div>


     <div class='is-grouped control select'>
       <select name='selectGroupMemberThree'> 
       <option value='0'> None </option>
       ";
    $getUsers = "SELECT user_id, name FROM webdev_user_details
       WHERE coach = '$loggedInCoachId'
       ORDER BY name ASC;";
    $executeGetUsers = $conn->query($getUsers);
    if (!$executeGetUsers) {
        echo $conn->error;
    }
    while ($row = $executeGetUsers->fetch_assoc()) {

        $userName = $row['name'];
        $userid = $row['user_id'];

        echo "<option value='$userid'>$userName</option>";
    }


    echo "
        </select>
      </div>
     
     <div class='is-grouped control select'>
       <select name='selectGroupMemberFour'>
       <option value='0'> None </option>

       ";
    $getUsers = "SELECT user_id, name FROM webdev_user_details
       WHERE coach = '$loggedInCoachId'
       ORDER BY name ASC;";
    $executeGetUsers = $conn->query($getUsers);
    if (!$executeGetUsers) {
        echo $conn->error;
    }
    while ($row = $executeGetUsers->fetch_assoc()) {

        $userName = $row['name'];
        $userid = $row['user_id'];

        echo "<option value='$userid'>$userName</option>";
    }


    echo "
        </select>
      </div>
      </div>

      <div class='field'>
      <label class='label'>Session One:</label>
      <div class='control'>
        <input class='input' id='newGroupSessionOne' type='text' placeholder='Details of first group session' name='firstGroupSession'>
      </div>
      <p class='help is-danger' id='newGroupSessionOneWarn'> </p>
    </div>


    
    <div class='field'>
    <label class='label'>Session Two:</label>
    <div class='control'>
      <input class='input' id='newGroupSessionTwo' type='text' placeholder='Details of second group session' name='secondGroupSession'>
    </div>
    <p class='help is-danger' id='newGroupSessionTwoWarn'> </p>
  </div>

  
  <div class='field'>
  <label class='label'>Session Three:</label>
  <div class='control'>
    <input class='input' id='newGroupSessionThree' type='text' placeholder='Details of third group session' name='thirdGroupSession'>
  </div>
  <p class='help is-danger' id='newGroupSessionThreeWarn'> </p>
</div>


  <footer class='modal-card-foot'>
      <p class='control'>
      <input type='submit' class='button is-success detailsIfValid' id='detailsSubmit' value='Submit Group' name='groupSubmit'> 
        <button class='button cancelUpdate'>Cancel</button>
      </footer>
        </form>
      </section>
      
    </div>
  </div>
  
";
}

/**
 * If the group creation form has been submitted, attemps to validate it.
 * If it successfully validates, writes to the database and creates an inbox message for the users input.
 */
if (isset($_POST['groupSubmit'])) {
    // grabs all the posted data, stores it in local variables.
    $sanitisedGroupName = $conn->real_escape_string(trim($_POST['newGroupName']));
    $sanitisedNewGroupDesc = $conn->real_escape_string(trim($_POST['newGroupDesc']));
    $sanitisedSelectGroupMemberOne = $conn->real_escape_string(trim($_POST['selectGroupMemberOne']));
    $sanitisedSelectGroupMemberTwo = $conn->real_escape_string(trim($_POST['selectGroupMemberTwo']));
    $sanitisedSelectGroupMemberThree = $conn->real_escape_string(trim($_POST['selectGroupMemberThree']));
    $sanitisedSelectGroupMemberFour = $conn->real_escape_string(trim($_POST['selectGroupMemberFour']));
    $sanitisedFirstGroupSession = $conn->real_escape_string(trim($_POST['firstGroupSession']));
    $sanitisedSecondGroupSession = $conn->real_escape_string(trim($_POST['secondGroupSession']));
    $sanitisedThirdGroupSession = $conn->real_escape_string(trim($_POST['thirdGroupSession']));
    /**
     * Checks that the fields have been filled in, otherwise 
     * displays an error and does not submit a query
     */
    if (($sanitisedGroupName == "") || ($sanitisedNewGroupDesc == "") ||
        ($sanitisedFirstGroupSession == "") ||  ($sanitisedSecondGroupSession == "")
        || ($sanitisedThirdGroupSession == "")
    ) {
        $groupCreationError = "Error - cannot create group with empty fields";
        /**
         * The following validation checks that the same client has not been entered multiple times.
         */
    } else if ((($sanitisedSelectGroupMemberThree == $sanitisedSelectGroupMemberOne) && ($sanitisedSelectGroupMemberThree != 0 && $sanitisedSelectGroupMemberOne != 0))
        || (($sanitisedSelectGroupMemberThree == $sanitisedSelectGroupMemberTwo) && ($sanitisedSelectGroupMemberThree != 0 && $sanitisedSelectGroupMemberTwo != 0))
        || (($sanitisedSelectGroupMemberThree == $sanitisedSelectGroupMemberFour) && ($sanitisedSelectGroupMemberThree != 0 && $sanitisedSelectGroupMemberFour != 0))
    ) {
        $groupCreationError = "Error - cannot enter same person multiple times within group";
    } else if ((($sanitisedSelectGroupMemberFour == $sanitisedSelectGroupMemberOne)  && ($sanitisedSelectGroupMemberFour != 0 && $sanitisedSelectGroupMemberOne != 0))
        || (($sanitisedSelectGroupMemberFour == $sanitisedSelectGroupMemberTwo) && ($sanitisedSelectGroupMemberFour != 0 && $sanitisedSelectGroupMemberTwo != 0))
    ) {
        $groupCreationError = "Error - cannot enter same person multiple times within group";
    } else if (($sanitisedSelectGroupMemberOne == $sanitisedSelectGroupMemberTwo) && ($sanitisedSelectGroupMemberOne != 0 && $sanitisedSelectGroupMemberTwo != 0)) {
        $groupCreationError = "Error - cannot enter same person multiple times within group";
        /**
         * The following validation checks that the group has four members. 
         */
    } else if (($sanitisedSelectGroupMemberOne == 0) && ($sanitisedSelectGroupMemberTwo == 0) &&
        ($sanitisedSelectGroupMemberThree == 0)
    ) {
        $groupCreationError = "Error - must be four members in a group. Please try again.";
    } else if (($sanitisedSelectGroupMemberOne == 0) && ($sanitisedSelectGroupMemberTwo == 0)
        &&  ($sanitisedSelectGroupMemberFour == 0)
    ) {
        $groupCreationError = "Error - must be four members in a group. Please try again.";
    } else if (($sanitisedSelectGroupMemberOne == 0) && ($sanitisedSelectGroupMemberThree == 0)
        &&  ($sanitisedSelectGroupMemberFour == 0)
    ) {
        $groupCreationError = "Error - must be four members in a group. Please try again.";
    } else if (($sanitisedSelectGroupMemberTwo == 0) && ($sanitisedSelectGroupMemberThree == 0)
        &&  ($sanitisedSelectGroupMemberFour == 0)
    ) {
        $groupCreationError = "Error - must be four members in a group. Please try again.";
    } else if (($sanitisedSelectGroupMemberOne == 0)) {
        $groupCreationError = "Error - must be four members in a group. Please try again.";
    } else if (($sanitisedSelectGroupMemberTwo == 0)) {
        $groupCreationError = "Error - must be four members in a group. Please try again.";
    } else if (($sanitisedSelectGroupMemberThree == 0)) {
        $groupCreationError = "Error - must be four members in a group. Please try again.";
    } else if (($sanitisedSelectGroupMemberFour == 0)) {
        $groupCreationError = "Error - must be four members in a group. Please try again.";
    } else {
        /**
         * Else, successful and creates group.
         * Then messages all users who are inserted into the group.
         */
        $createNewGroup = "INSERT INTO webdev_groups (group_name, description, coach, member_one, member_two, member_three, member_four,
    first_session, second_session, third_session)
VALUES ('$sanitisedGroupName', '$sanitisedNewGroupDesc', $loggedInCoachId, $sanitisedSelectGroupMemberOne, 
$sanitisedSelectGroupMemberTwo, $sanitisedSelectGroupMemberThree, $sanitisedSelectGroupMemberFour,
'$sanitisedFirstGroupSession', '$sanitisedSecondGroupSession', '$sanitisedThirdGroupSession');";

        /**
         * The below sends messages to all of the users who are being put into the group.
         */
        $sendMessage = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user,hide) VALUES
        ('$sanitisedSelectGroupMemberOne', '$loggedInCoachId', 'Creation of new group - $sanitisedGroupName', 'Dear client, I have added you to the group $sanitisedGroupName.
        Please keep an eye on your dashboard for upcoming events. You can find out specifics of the upcoming sessiosn in your calendar!', '$loggedInCoachId', '$sanitisedSelectGroupMemberOne', 0)";

        $sendMessageTwo = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user,hide) VALUES
        ('$sanitisedSelectGroupMemberTwo', '$loggedInCoachId', 'Creation of new group - $sanitisedGroupName', 'Dear client, I have added you to the group $sanitisedGroupName.
        Please keep an eye on your dashboard for upcoming events. You can find out specifics of the upcoming sessiosn in your calendar!', '$loggedInCoachId', '$sanitisedSelectGroupMemberTwo', 0)";

        $sendMessageThree = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user,hide) VALUES
        ('$sanitisedSelectGroupMemberThree', '$loggedInCoachId', 'Creation of new group - $sanitisedGroupName', 'Dear client, I have added you to the group $sanitisedGroupName.
        Please keep an eye on your dashboard for upcoming events. You can find out specifics of the upcoming sessiosn in your calendar!', '$loggedInCoachId', '$sanitisedSelectGroupMemberThree', 0)";

        $sendMessageFour = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user,hide) VALUES
        ('$sanitisedSelectGroupMemberFour', '$loggedInCoachId', 'Creation of new group - $sanitisedGroupName', 'Dear client, I have added you to the group $sanitisedGroupName.
        Please keep an eye on your dashboard for upcoming events. You can find out specifics of the upcoming sessiosn in your calendar!', '$loggedInCoachId', '$sanitisedSelectGroupMemberFour', 0)";

        // transaction adapted from online tutorial https://www.youtube.com/watch?v=CNt9HPqDIVc
        $conn->autocommit(false);

        $error = array();

        $a = $conn->query($createNewGroup);
        if ($a == false) {
            $groupCreationError = "There was an error creating the group.";
            array_push($error, $groupCreationError);
        }

        $b = $conn->query($sendMessage);
        if ($b == false) {
            $groupCreationError = "There was an error sending the first message.";
            array_push($error, $groupCreationError);
        }

        $c = $conn->query($sendMessageTwo);
        if ($c == false) {
            $groupCreationError = "There was an error sending the second message.";
            array_push($error, $groupCreationError);
        }

        $d = $conn->query($sendMessageThree);
        if ($d == false) {
            $groupCreationError = "There was an error sending the third message.";
            array_push($error,  $groupCreationError);
        }

        $e = $conn->query($sendMessageFour);
        if ($e == false) {
            $groupCreationError = "There was an error sending the fourth message.";
            array_push($error,  $groupCreationError);
        }

        /**
         * If error array is not empty, one of the queries in the transaction 
         * has failed and it is rolled back. Else, commits the transaction.
         */

        if (!empty($error)) {
            $conn->rollback();
            $groupCreationError = "There was an error..";
        } else {
            //commit
            $conn->commit();
            $groupCreationSuccess = "Group successfully created.";
        }
    }
}

/**
 * Allows the coach to edit an existing group.
 * Allows them to change all users in a group, or change the group name and/or description, alongside the 
 * group sessions. 
 */
if (isset($_POST['editGroup'])) {

    $groupToEdit = $_POST['groupID'];

    /**
     * Get current info of group stored in database
     */
    $getInfoOfGroup = $conn->prepare("SELECT group_name, description, member_one, member_two, member_three, member_four, 
    first_session, second_session, third_session
    FROM webdev_groups
    WHERE webdev_groups.id = ? ");
    $getInfoOfGroup->bind_param("i", $groupToEdit);
    $getInfoOfGroup->execute();
    $getInfoOfGroup->store_result();
    $getInfoOfGroup->bind_result(
        $groupName,
        $groupDesc,
        $memberOne,
        $memberTwo,
        $memberThree,
        $memberFour,
        $firstSession,
        $secondSession,
        $thirdSession
    );
    $getInfoOfGroup->fetch();
?>
    <!-- modal for editing a group-->

    <div class='modal is-active' id='createNewGrp'>
        <div class='modal-background '></div>
        <div class='modal-card'>
            <header class='modal-card-head'>
                <?php
                echo "
        <p class='modal-card-title'>Editing $groupName</p>
        ";
                ?>
                <button class='delete cancelUpdate' aria-label='close'></button>

            </header>

            <section class='modal-card-body'>

                <form action='groups.php' method='POST' id='newGroupForm'>
                    <div class='field'>
                        <label class='label'>Group Name</label>
                        <div class='control'>
                            <input class='input' id='newGroupName' type='text' value="<?php echo $groupName ?>" name='newGroupName'>
                        </div>
                        <p class='help is-danger' id='newGroupNameWarn'> </p>
                    </div>
                    <?php
                    echo "
                    <input type='hidden' name='groupID' value='$groupToEdit'>";
                    ?>

                    <div class='field'>
                        <label class='label'>Group description: </label>
                        <div class='control'>
                            <input class='input' id='newGroupDesc' type='text' value="<?php echo $groupDesc ?>" name='newGroupDesc'>
                        </div>
                        <p class='help is-danger' id='newGroupDescWarn'> </p>
                    </div>


                    <div class='field'>
                        <label class='label'>Select first group member: </label>
                        <div class='control select'>
                            <select name='selectGroupMemberOne'>
                                <option value='0'> None </option>

                                <?php
                                $getUsers = "SELECT user_id, name FROM webdev_user_details
       WHERE coach = '$loggedInCoachId'
       ORDER BY name ASC;";
                                $executeGetUsers = $conn->query($getUsers);
                                if (!$executeGetUsers) {
                                    echo $conn->error;
                                }
                                while ($row = $executeGetUsers->fetch_assoc()) {

                                    /**
                                     * Displays group members. 
                                     * If a member was assigned, defaults to them, otherwise by alphabetical.
                                     */
                                    $userName = $row['name'];
                                    $userid = $row['user_id'];
                                    if ($userid == $memberOne) {
                                        echo "<option value='$userid' selected>$userName</option>";
                                    } else {
                                        echo "<option value='$userid'>$userName</option>";
                                    }
                                }


                                echo "
        </select>
      </div>
      </div>

      <div class='field'>
     <label class='label'>Select second group member: </label>
     <div class='control select'>
       <select name='selectGroupMemberTwo'> 
       <option value='0'> None </option>

       ";
                                $getUsers = "SELECT user_id, name FROM webdev_user_details
       WHERE coach = '$loggedInCoachId'
       ORDER BY name ASC;";
                                $executeGetUsers = $conn->query($getUsers);
                                if (!$executeGetUsers) {
                                    echo $conn->error;
                                }
                                while ($row = $executeGetUsers->fetch_assoc()) {
                                    /**
                                     * Displays group members. 
                                     * If a member was assigned, defaults to them, otherwise by alphabetical.
                                     */
                                    $userName = $row['name'];
                                    $userid = $row['user_id'];

                                    if ($userid == $memberTwo) {
                                        echo "<option value='$userid' selected>$userName</option>";
                                    } else {
                                        echo "<option value='$userid'>$userName</option>";
                                    }
                                }


                                echo "
        </select>
      </div>
      </div>

      <div class='field'>
     <label class='label'>Select third group member: </label>
     <div class='control select'>
       <select name='selectGroupMemberThree'> 
       <option value='0'> None </option>

       ";
                                $getUsers = "SELECT user_id, name FROM webdev_user_details
       WHERE coach = '$loggedInCoachId'
       ORDER BY name ASC;";
                                $executeGetUsers = $conn->query($getUsers);
                                if (!$executeGetUsers) {
                                    echo $conn->error;
                                }
                                while ($row = $executeGetUsers->fetch_assoc()) {
                                    /**
                                     * Displays group members. 
                                     * If a member was assigned, defaults to them, otherwise by alphabetical.
                                     */
                                    $userName = $row['name'];
                                    $userid = $row['user_id'];

                                    if ($userid == $memberThree) {
                                        echo "<option value='$userid' selected>$userName</option>";
                                    } else {
                                        echo "<option value='$userid'>$userName</option>";
                                    }
                                }


                                echo "
        </select>
      </div>
      </div>

      <div class='field'>
     <label class='label'>Select fourth group member: </label>
     <div class='control select'>
       <select name='selectGroupMemberFour'>
       <option value='0'> None </option>

       ";
                                $getUsers = "SELECT user_id, name FROM webdev_user_details
       WHERE coach = '$loggedInCoachId'
       ORDER BY name ASC;";
                                $executeGetUsers = $conn->query($getUsers);
                                if (!$executeGetUsers) {
                                    echo $conn->error;
                                }
                                while ($row = $executeGetUsers->fetch_assoc()) {
                                    /**
                                     * Displays group members. 
                                     * If a member was assigned, defaults to them, otherwise by alphabetical.
                                     */
                                    $userName = $row['name'];
                                    $userid = $row['user_id'];

                                    if ($userid == $memberFour) {
                                        echo "<option value='$userid' selected>$userName</option>";
                                    } else {
                                        echo "<option value='$userid'>$userName</option>";
                                    }
                                }


                                ?>
                            </select>
                        </div>
                    </div>



                    <div class='field'>
                        <label class='label'>Session One:</label>
                        <div class='control'>
                            <input class='input' id='newGroupSessionOne' type='text' value="<?php echo $firstSession ?>" name='firstGroupSession'>
                        </div>
                        <p class='help is-danger' id='newGroupSessionOneWarn'> </p>
                    </div>



                    <div class='field'>
                        <label class='label'>Session Two:</label>
                        <div class='control'>
                            <input class='input' id='newGroupSessionTwo' type='text' value="<?php echo $secondSession ?>" name='secondGroupSession'>
                        </div>
                        <p class='help is-danger' id='newGroupSessionTwoWarn'> </p>
                    </div>


                    <div class='field'>
                        <label class='label'>Session Three:</label>
                        <div class='control'>
                            <input class='input' id='newGroupSessionThree' type='text' value="<?php echo $thirdSession ?>" name='thirdGroupSession'>
                        </div>
                        <p class='help is-danger' id='newGroupSessionThreeWarn'> </p>
                    </div>

                    <footer class='modal-card-foot'>
                        <p class='control'>
                            <input type='submit' class='button is-success detailsIfValid' id='detailsSubmit' value='Submit Update' name='submitEdit'>
                            <button class='button cancelUpdate'>Cancel</button>
                    </footer>
                </form>
            </section>

        </div>
    </div>
<?php
}

/**
 * If the edit group form is posted, attempts to validate the data that is posted.
 * If it passes the validaiton checks, the values in the database are updated.
 */
if (isset($_POST['submitEdit'])) {
    $groupToEdit = $_POST['groupID'];
    // store posted data in local variables. 
    $groupName = $_POST['newGroupName'];
    $sanitisedGroupName = $conn->real_escape_string(trim($groupName));
    $groupDesc = $_POST['newGroupDesc'];
    $sanitisedNewGroupDesc = $conn->real_escape_string(trim($groupDesc));
    $sanitisedSelectGroupMemberOne = $conn->real_escape_string(trim($_POST['selectGroupMemberOne']));
    $sanitisedSelectGroupMemberTwo = $conn->real_escape_string(trim($_POST['selectGroupMemberTwo']));
    $sanitisedSelectGroupMemberThree = $conn->real_escape_string(trim($_POST['selectGroupMemberThree']));
    $sanitisedSelectGroupMemberFour = $conn->real_escape_string(trim($_POST['selectGroupMemberFour']));
    $firstGroupSession = $_POST['firstGroupSession'];
    $sanitisedFirstGroupSession = $conn->real_escape_string(trim($firstGroupSession));
    $secondGroupSession = $_POST['secondGroupSession'];
    $sanitisedSecondGroupSession = $conn->real_escape_string(trim($secondGroupSession));
    $thirdGroupSession = $_POST['thirdGroupSession'];
    $sanitisedThirdGroupSession = $conn->real_escape_string(trim($thirdGroupSession));



    /**
     * Get current info of group stored in database to compare and ensure all 
     */
    $getInfoOfGroup = $conn->prepare("SELECT group_name, description, member_one, member_two, member_three, member_four, 
    first_session, second_session, third_session
    FROM webdev_groups
    WHERE webdev_groups.id = ? ");
    $getInfoOfGroup->bind_param("i", $groupToEdit);
    $getInfoOfGroup->execute();
    $getInfoOfGroup->store_result();
    $getInfoOfGroup->bind_result(
        $currentGroupName,
        $currentGroupDesc,
        $currentMemberOne,
        $currentMemberTwo,
        $currentMemberThree,
        $currentMemberFour,
        $currentFirstSession,
        $currentSecondSession,
        $currentThirdSession
    );
    $getInfoOfGroup->fetch();


    if (($groupName == $currentGroupName) && ($groupDesc == $currentGroupDesc) &&
        ($sanitisedSelectGroupMemberOne == $currentMemberOne) &&  ($sanitisedSelectGroupMemberTwo == $currentMemberTwo) &&
        ($sanitisedSelectGroupMemberThree == $currentMemberThree) && ($sanitisedSelectGroupMemberFour == $currentMemberFour) &&
        ($firstGroupSession == $currentFirstSession) && ($secondGroupSession == $currentSecondSession) &&
        ($thirdGroupSession == $currentThirdSession)
    ) {

        $groupCreationError = "Error - cannot update group. No fields were updated.";
    } else if ((($sanitisedSelectGroupMemberThree == $sanitisedSelectGroupMemberOne) && ($sanitisedSelectGroupMemberThree != 0 && $sanitisedSelectGroupMemberOne != 0))
        || (($sanitisedSelectGroupMemberThree == $sanitisedSelectGroupMemberTwo) && ($sanitisedSelectGroupMemberThree != 0 && $sanitisedSelectGroupMemberTwo != 0))
        || (($sanitisedSelectGroupMemberThree == $sanitisedSelectGroupMemberFour) && ($sanitisedSelectGroupMemberThree != 0 && $sanitisedSelectGroupMemberFour != 0))
    ) {
        $groupCreationError = "Error - cannot enter same person multiple times within group";
    } else if ((($sanitisedSelectGroupMemberFour == $sanitisedSelectGroupMemberOne)  && ($sanitisedSelectGroupMemberFour != 0 && $sanitisedSelectGroupMemberOne != 0))
        || (($sanitisedSelectGroupMemberFour == $sanitisedSelectGroupMemberTwo) && ($sanitisedSelectGroupMemberFour != 0 && $sanitisedSelectGroupMemberTwo != 0))
    ) {
        $groupCreationError = "Error - cannot enter same person multiple times within group";
    } else if (($sanitisedSelectGroupMemberOne == $sanitisedSelectGroupMemberTwo) && ($sanitisedSelectGroupMemberOne != 0 && $sanitisedSelectGroupMemberTwo != 0)) {
        $groupCreationError = "Error - cannot enter same person multiple times within group";
    } else if (($sanitisedSelectGroupMemberOne == 0) && ($sanitisedSelectGroupMemberTwo == 0) &&
        ($sanitisedSelectGroupMemberThree == 0)
    ) {
        $groupCreationError = "Error - must be four members in a group. Please try again.";
    } else if (($sanitisedSelectGroupMemberOne == 0) && ($sanitisedSelectGroupMemberTwo == 0)
        &&  ($sanitisedSelectGroupMemberFour == 0)
    ) {
        $groupCreationError = "Error - must be four members in a group. Please try again.";
    } else if (($sanitisedSelectGroupMemberOne == 0) && ($sanitisedSelectGroupMemberThree == 0)
        &&  ($sanitisedSelectGroupMemberFour == 0)
    ) {
        $groupCreationError = "Error - must be four members in a group. Please try again.";
    } else if (($sanitisedSelectGroupMemberTwo == 0) && ($sanitisedSelectGroupMemberThree == 0)
        &&  ($sanitisedSelectGroupMemberFour == 0)
    ) {
        $groupCreationError = "Error - must be four members in a group. Please try again.";
    } else if (($sanitisedSelectGroupMemberOne == 0)) {
        $groupCreationError = "Error - must be four members in a group. Please try again.";
    } else if (($sanitisedSelectGroupMemberTwo == 0)) {
        $groupCreationError = "Error - must be four members in a group. Please try again.";
    } else if (($sanitisedSelectGroupMemberThree == 0)) {
        $groupCreationError = "Error - must be four members in a group. Please try again.";
    } else if (($sanitisedSelectGroupMemberFour == 0)) {
        $groupCreationError = "Error - must be four members in a group. Please try again.";
    } else {


        $editGroup = "UPDATE webdev_groups 
        SET group_name = '$sanitisedGroupName', 
        description = '$sanitisedNewGroupDesc', member_one = '$sanitisedSelectGroupMemberOne', 
        member_two = '$sanitisedSelectGroupMemberTwo', member_three =  '$sanitisedSelectGroupMemberThree', 
        member_four =  '$sanitisedSelectGroupMemberFour', first_session = '$sanitisedFirstGroupSession', 
        second_session = '$sanitisedSecondGroupSession', third_session = '$sanitisedThirdGroupSession'
        WHERE webdev_groups.id = $groupToEdit";


        $executeEditGroup = $conn->query($editGroup);

        if (!$executeEditGroup) {
            echo $conn->error;
        }
    }
}

/**
 * Allows the coach to delete a group.
 * Asks the coach for their password to confirm, and asks them to click a pop up confirmation. 
 */
if (isset($_POST['deleteGroupButton'])) {
    $groupID = $_POST['groupID'];
    echo "
    <div class='modal is-active' id='deleteGroup'>
    <div class='modal-background '></div>
    <div class='modal-card'>
      <header class='modal-card-head'>
        <p class='modal-card-title'>Delete Group</p>

        <button class='delete cancelUpdate' aria-label='close'></button>

      </header> 

      <section class='modal-card-body'>
      

        <form action='groups.php' method='POST' id='deleteGroupForm'>
          <p> Please note that deleting groups is final - it CANNOT be undone. </p>
          <div class='field'>
            <label class='label'>Enter password: </label>
            <div class='control'>
              <input class='input' type='password' id='delCurrentPassword' name='delCurrentPassword'>
            </div>
            <p class='help is-danger' id='delCurrentPasswordWarn'> </p>
          </div>

          <input type='hidden' name='groupToDelete' value='$groupID'>
          <div class='field'>

            <label class='label'>Confirm password: </label>

            <div class='control'>
              <input class='input' id='delConfirmPassword' type='password' name='delConfirmPassword'>
            </div>
            <p class='help is-danger' id='delConfirmPasswordWarn'> </p>
          </div>



          <footer class='modal-card-foot'>
            <p class='control'>

              <input type='submit' class='button is-danger deleteAccIfValid' onclick=\"return confirm('Are you sure you wish to delete this group? THIS CANNOT BE UNDONE.')\" 
              id='deleteGroupSubmit' value='Delete Group' name='deleteGroupSubmit'>
              <button class='button cancelUpdate'>Cancel</button>
          </footer>
        </form>
      </section>

    </div>
  </div>
";
}

/**
 * If the delete group form has been submitted, attempts to validate the coach's login to proces it.
 * If validation fails, does not delete.
 * Will delete all entries in the database which contain the group due to use of foreign keys:
 * Deletes all inbox entries, appointments, and the group itself. 
 */
if (isset($_POST['deleteGroupSubmit'])) {
    $delCurrentPassword = $conn->real_escape_string(trim($_POST['delCurrentPassword']));
    $delConfirmPassword = $conn->real_escape_string(trim($_POST['delConfirmPassword']));
    $groupToDelete = $_POST['groupToDelete'];


    $grabCurrentPass = $conn->prepare("SELECT AES_DECRYPT(password, '09UYO2ELHJ290OYEH22098H9ty') AS password from webdev_coach
    INNER JOIN webdev_users 
    ON webdev_coach.user_id = webdev_users.id
    WHERE webdev_coach.id = ?");
    $grabCurrentPass->bind_param("i", $loggedInCoachId);
    $grabCurrentPass->execute();
    $grabCurrentPass->store_result();
    $grabCurrentPass->bind_result($currentPass);
    $grabCurrentPass->fetch();

    if (($delCurrentPassword == $currentPass) && ($delConfirmPassword == $currentPass)) {
        /**
         * transaction adapted from online tutorial <https://www.youtube.com/watch?v=CNt9HPqDIVc>
         * 
         * This transaction deleted all entries within the database which contain the user who is being deleted.
         * As there are foreign key constraints, cannot just delete the user - must delete all of their relevant data,
         * such as inbox, stats, etc before actually deleting the user from the table. 
         * This will free up the username to be taken again. 
         */
        $conn->autocommit(false);

        $deleteError = array();
        // attempts to delete all appointments for the group
        $a = $conn->query("DELETE FROM webdev_appointments WHERE group_id = '$groupToDelete'");
        if ($a == false) {
            $groupCreationError = "Problem deleting appointments for group.";
            array_push($deleteError, $groupCreationError);
            echo $conn->error;
        }
        // attempts to delete all the messages for the group
        $b = $conn->query("DELETE FROM webdev_inbox WHERE group_id = '$groupToDelete'");
        if ($b == false) {
            $groupCreationError = "Problem deleting inbox for group.";
            array_push($deleteError, $groupCreationError);
            echo $conn->error;
        }
        // attempts to delete the group itself
        $c = $conn->query("DELETE FROM webdev_groups WHERE id = '$groupToDelete'");
        if ($c == false) {
            $groupCreationError = "Problem deleting group.";
            array_push($deleteError,  $groupCreationError);
            echo $conn->error;
        }
        /**
         * If error array is not empty, one of the queries in the transaction 
         * has failed and it is rolled back. Else, commits the transaction.
         */
        if (!empty($deleteError)) {
            $conn->rollback();
        } else {
            //commit if all ok
            $conn->commit();
            $groupCreationSuccess = "Group has successfully been deleted.";
        }
    } else {
        $groupCreationError = "Passwords do not match your actual password. Please try again.";
    }
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gymafi | Groups </title>
    <link href="../styles/bulma.css" rel="stylesheet">
    <link href="../styles/lightbox.css" rel="stylesheet">
    <link href="../styles/gui.css" rel="stylesheet">
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
    <script src="../script/myScript.js"></script>
    <script src="../script/lightbox.js"></script>

</head>

<!-- displays log out button-->

<body class="has-background-grey-lighter">
    <nav class='navbar is-dark' role='navigation' aria-label='main navigation'>
        <div class='navbar-end'>
            <div class='navbar-item'>
                <div class='buttons'>
                    <a class='button is-danger' href='../logout.php'>
                        Logout
                    </a>
                </div>
            </div>
        </div>
        </div>
    </nav>

    <!-- Website header/hero-->
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

    <!-- navigation bar-->
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


                <a class='navbar-item has-text-black' href='approvals.php'>
                    Approvals
                </a>

                <a class='navbar-item has-text-black' href='../appointments.php'>
                    Appointments
                </a>


                <a class='navbar-item has-text-black  has-background-warning' href='groups.php'>
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


    <!-- allows the user to attempt to create a group-->
    <div id='dashColumns'>
        <div class='columns'>
            <div class='column is-3'>
                <article class='message is-link'>
                    <div class='message-header'>
                        Create Group
                    </div>

                    <div class='message-body'>
                        Click the button below to create a new group of four members.
                        <br>


                        <form action='groups.php' method='POST'>
                            <div class='control'>
                                <input type='submit' class='button is-primary' id='createGroupButton' value='Create Group' name='createGrp'>
                            </div>


                        </form>
                    </div>
                </article>

            </div>


            <!-- allows the user to edit an existing group-->
            <div class='column is-7' id='editContentColumn'>
                <article class='message is-dark'>
                    <div class='message-header'>
                        <p>
                            <h1 class='title titleHeader'>Group Editor</h1>
                        </p>

                    </div>
                    <div class='message-body'>

                        <?php

                        if (isset($groupCreationError)) {
                            echo "<p class='displayError'> $groupCreationError</p>";
                        } else if (isset($groupCreationSuccess)) {
                            echo "<p class='displaySucc'> $groupCreationSuccess</p>";
                        }
                        /**
                         * Displays all of the groups tied to this coach.
                         */
                        $getAllGroups = "SELECT webdev_groups.id, webdev_groups.group_name FROM webdev_groups
                    
                    WHERE coach = '$loggedInCoachId'";
                        $executeGetAllGroups = $conn->query($getAllGroups);

                        if (!$executeGetAllGroups) {
                            echo $conn->error;
                        }
                        echo "<div class='columns'>";
                        /*
                    * variable to track pages edit articles printed out,
                    *  when gets to 2, ends current columns row and starts new one
                    */
                        $pageCounter = 0;
                        while ($row = $executeGetAllGroups->fetch_assoc()) {
                            $groupID = $row['id'];
                            $groupName = $row['group_name'];



                            echo "<div class='column'> 
                        <article class='message is-dark'>
                        <form action='groups.php' method='POST'>
                        <div class='message-header'>
                            <p>
                            ", htmlentities($groupName, ENT_QUOTES), " 
                            </p>
        
                        </div>
                        <div class='message-body'>
                        <input type='hidden' name='groupID' value='$groupID'>
                       
                        </div>
                       

                        <input type='submit' value='Edit ", htmlentities($groupName, ENT_QUOTES), " 'class='button is-success' name='editGroup'>
                        <input type='submit' value='Delete ", htmlentities($groupName, ENT_QUOTES), " 'class='button is-danger' name='deleteGroupButton'> 
                        </form>
                        </article>
                        </div>
                        ";
                            $pageCounter++;
                            if ($pageCounter % 2 == 0) {
                                echo "</div>
                            <div class='columns'>";
                            }
                        }

                        echo "
                    <div class='column'> 
                    </div>
                    </div>
                        </div>";
                        ?>


                    </div>
            </div>
        </div>

        <!-- Page footer-->

        <div class='myFooter'>
            <footer class='footer has-background-dark alsoMyFooter' id='myFootInbox'>
                <div class='content has-text-centered has-text-white'>
                    <p>
                        <span id='boldFoot'>CSC7062 Project</span> by Jordan Brown (40282125).
                    </p>
                </div>
            </footer>
        </div>
</body>


</html>