<?php
session_start();
include("../conn.php");

/**
 * Checks the logged in user is a coach
 * If not, kicks them to the login page.
 */
if (isset($_SESSION['gymafi_coachid'])) {
    $loggedInCoachId = $_SESSION['gymafi_coachid'];
} else if (isset($_SESSION['gymafi_superadmin'])) {
    header("location: superadmin.php");
} else {
    header("location: ../login.php");
}

/**
 * If the coach clicked on the edit client button, 
 * displays a modal to them to allow them to edit the client details.
 * 
 */
if (isset($_POST['editClientTrack'])) {
    $clientID = $_POST['userID'];
    $clientName = $_POST['client_name'];


    $getClientCurrentRegime = $conn->prepare("SELECT diet_plan, monday, tuesday, wednesday, thursday, friday, saturday, sunday FROM webdev_training_regime
WHERE user_id = ?");
    $getClientCurrentRegime->bind_param("i", $clientID);
    $getClientCurrentRegime->execute();
    $getClientCurrentRegime->store_result();
    $getClientCurrentRegime->bind_result($currentDiet, $currentMonday, $currentTuesday, $currentWednesday, $currentThursday, $currentFriday, $currentSatuday, $currentSunday);
    $getClientCurrentRegime->fetch();
    echo "
    <div class='modal is-active' id='editClientRegime'>
    <div class='modal-background '></div>
    <div class='modal-card'>
      <header class='modal-card-head'>
        <p class='modal-card-title'>Update Client Regime for $clientName</p>
        <button class='delete cancelUpdate' aria-label='close' ></button>
      
      </header>
     
      <section class='modal-card-body'>
        <form action='manageclients.php' method='POST' id='manageClient'>
        <input type='hidden' name='clientID' value='$clientID'>
        <input type='hidden' name='clientName' value='$clientName'>
          <div class='field'>
            <label class='label'>Diet plan: </label>
            
            <div class='control select'>
            <select name='dietPlan'> 
            ";

    $getAllDietPlans = "SELECT * FROM webdev_training_meals";
    $executeGetAllDietPlans = $conn->query($getAllDietPlans);
    if (!$executeGetAllDietPlans) {
        echo $conn->error;
    }
    /**
     * Displays possible options for diet plans. 
     * If the user's currently chosen diet plan is hit, selects that by default.
     */
    while ($row = $executeGetAllDietPlans->fetch_assoc()) {
        $dietID = $row['id'];
        $dietDesc = $row['meal_type'];
        if ($dietID == $currentDiet) {
            echo "<option selected value='$dietID'>$dietDesc</option>";
        } else {
            echo "<option value='$dietID'>$dietDesc</option>";
        }
    }

    echo "
            </select>
          </div>
  
          <div class='field'>
          <label class='label'>Monday: </label>
          
          <div class='control select'>
          <select name='mondayPlan'> 
          ";

    $getPlans = "SELECT * FROM webdev_training_plans";
    $executeGetPlansMon = $conn->query($getPlans);
    if (!$executeGetPlansMon) {
        echo $conn->error;
    }

    /**
     * Displays possible options for training. 
     * If the user's currently chosen regime plan is hit, selects that by default.
     */
    while ($row = $executeGetPlansMon->fetch_assoc()) {
        $planID = $row['id'];
        $planDesc = $row['plan'];
        if ($planID == $currentMonday) {
            echo "<option selected value='$planID'>$planDesc</option>";
        } else {
            echo "<option value='$planID'>$planDesc</option>";
        }
    }

    echo "
          </select>
        </div>
        
        <div class='field'>
        <label class='label'>Tuesday: </label>
        <div class='control select'>
        <select name='tuesdayPlan'> 
        ";


    $executeGetPlansTues = $conn->query($getPlans);
    if (!$executeGetPlansTues) {
        echo $conn->error;
    }

    /**
     * Displays possible options for training. 
     * If the user's currently chosen regime plan is hit, selects that by default.
     */
    while ($row = $executeGetPlansTues->fetch_assoc()) {
        $planID = $row['id'];
        $planDesc = $row['plan'];
        if ($planID == $currentTuesday) {
            echo "<option selected value='$planID'>$planDesc</option>";
        } else {
            echo "<option value='$planID'>$planDesc</option>";
        }
    }

    echo "
        </select>
      </div>
  
        <div class='field'>
        <label class='label'>Wednesday: </label>
        <div class='control select'>
          <select name='wednesdayPlan'> 
          ";


    $executeGetPlansWed = $conn->query($getPlans);
    if (!$executeGetPlansWed) {
        echo $conn->error;
    }

    /**
     * Displays possible options for training. 
     * If the user's currently chosen regime plan is hit, selects that by default.
     */
    while ($row = $executeGetPlansWed->fetch_assoc()) {
        $planID = $row['id'];
        $planDesc = $row['plan'];
        if ($planID == $currentWednesday) {
            echo "<option selected value='$planID'>$planDesc</option>";
        } else {
            echo "<option value='$planID'>$planDesc</option>";
        }
    }

    echo "
          </select>
        </div>
  
        <div class='field'>
        <label class='label'>Thursday: </label>
        <div class='control select'>
        <select name='thursdayPlan'> 
        ";

    $executeGetPlansThurs = $conn->query($getPlans);
    if (!$executeGetPlansThurs) {
        echo $conn->error;
    }

    /**
     * Displays possible options for training. 
     * If the user's currently chosen regime plan is hit, selects that by default.
     */
    while ($row = $executeGetPlansThurs->fetch_assoc()) {
        $planID = $row['id'];
        $planDesc = $row['plan'];
        if ($planID == $currentThursday) {
            echo "<option selected value='$planID'>$planDesc</option>";
        } else {
            echo "<option value='$planID'>$planDesc</option>";
        }
    }

    echo "
        </select>
      </div>
  
  
        <div class='field'>
        <label class='label'>Friday: </label>
         <div class='control select'>
          <select name='fridayPlan'> 
          ";

    $executeGetPlansFri = $conn->query($getPlans);
    if (!$executeGetPlansFri) {
        echo $conn->error;
    }
    while ($row = $executeGetPlansFri->fetch_assoc()) {
        $planID = $row['id'];
        $planDesc = $row['plan'];
        if ($planID == $currentFriday) {
            echo "<option selected value='$planID'>$planDesc</option>";
        } else {
            echo "<option value='$planID'>$planDesc</option>";
        }
    }

    echo "
          </select>
        </div>
  
  
        <div class='field'>
        <label class='label'>Saturday: </label>
        <div class='control select'>
        <select name='saturdayPlan'> ";


    $executeGetPlansSat = $conn->query($getPlans);
    if (!$executeGetPlansSat) {
        echo $conn->error;
    }
    while ($row = $executeGetPlansSat->fetch_assoc()) {
        $planID = $row['id'];
        $planDesc = $row['plan'];
        if ($planID == $currentSatuday) {
            echo "<option selected value='$planID'>$planDesc</option>";
        } else {
            echo "<option value='$planID'>$planDesc</option>";
        }
    }

    echo "
        </select>
      </div>
  
        <div class='field'>
        <label class='label'>Sunday: </label>
        <div class='control select'>
        <select name='sundayPlan'> 
        ";

    $executeGetPlansSun = $conn->query($getPlans);
    if (!$executeGetPlansSun) {
        echo $conn->error;
    }
    while ($row = $executeGetPlansSun->fetch_assoc()) {
        $planID = $row['id'];
        $planDesc = $row['plan'];
        if ($planID == $currentSunday) {
            echo "<option selected value='$planID'>$planDesc</option>";
        } else {
            echo "<option value='$planID'>$planDesc</option>";
        }
    }

    echo "
        </select>
      </div>
      </div>
        
        <footer class='modal-card-foot'>
        <input type='submit' class='button is-success' id='regimeSubmit' value='Save changes' name='clientRegimeSubmit'>
        <button class='button cancelUpdate'>Cancel</button>
      </footer>
  
  
        </form>
      </section>
     
    </div>
  </div>";
}

/**
 * If the edit client form has been submitted, attempts to validate it. 
 * If it passed validation checks, updates values in the database to the new, posted ones.
 */
if (isset($_POST['clientRegimeSubmit'])) {

    // store posted data in local variables
    $clientName = $_POST['clientName'];
    $clientID = $_POST['clientID'];
    $newDietPlan = $_POST['dietPlan'];
    $sanitisedDietPlan = $conn->real_escape_string(trim($newDietPlan));
    $newMondayPlan = $_POST['mondayPlan'];
    $sanitisedMondayPlan = $conn->real_escape_string(trim($newMondayPlan));
    $newTuesdayPlan = $_POST['tuesdayPlan'];
    $sanitisedTuesdayPlan = $conn->real_escape_string(trim($newTuesdayPlan));
    $newWednesdayPlan = $_POST['wednesdayPlan'];
    $sanitisedWednesdayPlan = $conn->real_escape_string(trim($newWednesdayPlan));
    $newThursdayPlan = $_POST['thursdayPlan'];
    $sanitisedThursdayPlan = $conn->real_escape_string(trim($newThursdayPlan));
    $newFridayPlan = $_POST['fridayPlan'];
    $sanitisedFridayPlan = $conn->real_escape_string(trim($newFridayPlan));
    $newSaturdayPlan = $_POST['saturdayPlan'];
    $sanitisedSaturdayPlan  = $conn->real_escape_string(trim($newSaturdayPlan));
    $newSundayPlan = $_POST['sundayPlan'];
    $sanitisedSundayPlan = $conn->real_escape_string(trim($newSundayPlan));

    // get info on current regime.
    $getClientCurrentRegime = $conn->prepare("SELECT diet_plan, monday, tuesday, wednesday, thursday, friday, saturday, sunday FROM webdev_training_regime
    WHERE user_id = ?");
    $getClientCurrentRegime->bind_param("i", $clientID);
    $getClientCurrentRegime->execute();
    $getClientCurrentRegime->store_result();
    $getClientCurrentRegime->bind_result($currentDiet, $currentMonday, $currentTuesday, $currentWednesday, $currentThursday, $currentFriday, $currentSatuday, $currentSunday);
    $getClientCurrentRegime->fetch();

    /**
     * Attempts to check if any changes have been made. If all fields are the same as the
     * values in the db, no changes have been made and the query is not sent.
     * If any changes, sends the query.
     */
    if (($sanitisedDietPlan == $currentDiet) && ($sanitisedMondayPlan == $currentMonday)
        &&  ($sanitisedTuesdayPlan == $currentTuesday) && ($sanitisedWednesdayPlan == $currentWednesday) &&
        ($sanitisedThursdayPlan == $currentThursday) &&
        ($sanitisedFridayPlan == $currentFriday) &&
        ($sanitisedSaturdayPlan == $currentSatuday)  && ($sanitisedSundayPlan == $currentSunday)
    ) {
        $updateFailed = "Did not update regime for $clientName - you have not changed any fields.";
    } else {
        $updatePlan = "UPDATE webdev_training_regime
SET webdev_training_regime.diet_plan = $sanitisedDietPlan, webdev_training_regime.monday = $sanitisedMondayPlan,
webdev_training_regime.tuesday = $sanitisedTuesdayPlan, webdev_training_regime.wednesday = $sanitisedWednesdayPlan,
webdev_training_regime.thursday = $sanitisedThursdayPlan, webdev_training_regime.friday = $sanitisedFridayPlan,
webdev_training_regime.saturday = $sanitisedSaturdayPlan, webdev_training_regime.sunday = $sanitisedSundayPlan
WHERE user_id = $clientID";
        echo "did I get here?";
        $executeUpdatePlan = $conn->query($updatePlan);
        if (!$executeUpdatePlan) {
            echo $conn->error;
        }
        $updateSuccessful = "Your have successfully updated the regime for $userName.";
    }
}


if (isset($_POST['editClientDetails'])) {
    $clientID = $_POST['userID'];
    $clientName = $_POST['client_name'];


    /**
     * Gets all of the details of the user who is currently logged in,
     * to display on their profile page as well as to fill the values of the edit profile modals.
     */
    $selectUser = $conn->prepare("SELECT  webdev_users.email, webdev_user_details.name, 
        webdev_user_details.address,  webdev_user_details.postcode,  webdev_user_details.city,  
        webdev_user_details.phone_number, webdev_user_details.coach
        FROM webdev_users 
        INNER JOIN webdev_user_details 
        ON webdev_users.id = webdev_user_details.user_id 
        WHERE webdev_users.id  = ?");
    $selectUser->bind_param("i", $clientID);
    $selectUser->execute();
    $selectUser->store_result();
    $selectUser->bind_result(
        $userEmail,
        $userActualName,
        $userAddress,
        $userPostcode,
        $userCity,
        $userPhoneNumber,
        $currentCoach
    );
    $selectUser->fetch();


?>

    <!-- modal for changing personal details-->

    <div class='modal is-active' id='editPersonalDetails'>
        <div class='modal-background '></div>
        <div class='modal-card'>
            <header class='modal-card-head'>
                <?php
                echo "
          <p class='modal-card-title'>Update Personal Details of $clientName    </p>";
                ?>
                <button class='delete cancelUpdate' aria-label='close'></button>

            </header>

            <section class='modal-card-body'>

                <form action='manageclients.php' method='POST' id='changeDetailsForm'>


                    <div class='field'>
                        <label class='label'>Client Name: </label>
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

                    <div class='field'>
                        <label class='label'>Telephone Number: </label>
                        <div class='control'>
                            <input class='input' type='text' id='phoneChange' value="<?php echo $userPhoneNumber ?>" name='newNumber'>
                        </div>
                        <p class='help is-danger' id='phoneChangeWarn'> </p>
                    </div>

                    <div class='field'>
                        <label class='label'>Address: </label>
                        <div class='control'>

                            <input class='input' type='text' id='addressChange' value="<?php echo $userAddress ?>" name='newAddressOne'>
                        </div>
                        <p class='help is-danger' id='addressChangeWarn'> </p>
                    </div>



                    <div class='field'>
                        <label class='label'>City: </label>
                        <div class='control'>
                            <?php
                            echo "
                <input class='input' type='text' id='cityChange' value=$userCity name='newCity'>";
                            ?>
                        </div>
                        <p class='help is-danger' id='cityChangeWarn'> </p>
                    </div>

                    <div class='field'>
                        <label class='label'>Postcode: </label>
                        <div class='control'>

                            <input class='input' type='text' id='postcodeChange' value="<?php echo $userPostcode ?>" name='newPostcode'>";

                        </div>
                        <p class='help is-danger' id='postcodeChangeWarn'> </p>
                    </div>


                    <?php
                    echo " <input type='hidden' name='userID' value='$clientID'>";
                    ?>


                    <div class="field">
                        <label class="label">Coach:</label>
                        <div class="control">
                            <div class='select'>
                                <select name='coachWanted'>
                                    <?php
                                    // grab all coaches
                                    $getcoaches = "SELECT * FROM webdev_coach;";
                                    $executeGetCoaches = $conn->query($getcoaches);

                                    if (!$executeGetCoaches) {
                                        echo $conn->error;
                                    }

                                    /**
                                     * Allows coach to change user coach. 
                                     * Displays all coaches on a dropdown list.
                                     * If one matches their current coach, automatically selects it.
                                     */
                                    while ($row = $executeGetCoaches->fetch_assoc()) {
                                        $name = $row['name'];
                                        $coachID = $row['id'];
                                        $area = $row['area'];
                                        if ($currentCoach == $coachID) {

                                            echo "<option selected value='$coachID'> $name ($area) </option>";
                                        } else {
                                            echo "<option value='$coachID'> $name ($area) </option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>



            </section>
            <footer class='modal-card-foot'>
                <p class='control'>
                    <input type='submit' class='button is-success detailsIfValid' id='detailsSubmit' value='Save changes' name='detailsSubmit'>
                    <button class='button cancelUpdate'>Cancel</button>
            </footer>
            </form>
            </section>

        </div>
    </div>
<?php
}

/**
 * If the coach posts the change details form, attempts to process it.
 */
if (isset($_POST['detailsSubmit'])) {
    $userid = $_POST['userID'];


    /**
     * Attempts to change the details of the user.
     * Captures and sanitises the posted data.
     * If all fields have been successfully posted, checks they meet the validation checks. 
     * Checks the values have been changes, so there is at least one change, and that they are numeric.
     * If it passes all checks, submits the change to the details.
     */
    if (isset($_POST['detailsSubmit'])) {

        /**
         * Gets all of the details of the user who is currently logged in,
         * to display on their profile page as well as to fill the values of the edit profile modals.
         */
        $selectUser = $conn->prepare("SELECT  webdev_users.email, webdev_user_details.name, 
        webdev_user_details.address,  webdev_user_details.postcode,  webdev_user_details.city,  
        webdev_user_details.phone_number, webdev_user_details.coach
        FROM webdev_users 
        INNER JOIN webdev_user_details 
        ON webdev_users.id = webdev_user_details.user_id 
        WHERE webdev_users.id  = ?");
        $selectUser->bind_param("i", $userid);
        $selectUser->execute();
        $selectUser->store_result();
        $selectUser->bind_result(

            $userEmail,
            $userActualName,
            $userAddress,
            $userPostcode,
            $userCity,
            $userPhoneNumber,
            $userCoach
        );
        $selectUser->fetch();




        $updatedName = $_POST['newName'];
        $sanitisedName = $conn->real_escape_string(trim($updatedName));
        $updatedEmail = $_POST['newEmail'];
        $sanitisedEmail = $conn->real_escape_string(trim($updatedEmail));
        $updatedPhoneNumber = $_POST['newNumber'];
        $sanitisedPhoneNumber = $conn->real_escape_string(trim($updatedPhoneNumber));
        $updatedAddress = $_POST['newAddressOne'];
        $sanitisedAddress = $conn->real_escape_string(trim($updatedAddress));
        $updatedCity = $_POST['newCity'];
        $sanitisedCity = $conn->real_escape_string(trim($updatedCity));
        $updatedPostcode = $_POST['newPostcode'];
        $sanitisedPostcode = $conn->real_escape_string(trim($updatedPostcode));
        $sanitisedCoach = $conn->real_escape_string(trim($_POST['coachWanted']));



        /**
         * Determines all fields have been changed. If all the same, does not send query and outputs error message.
         * 
         * If phone number is not numeric outputs error and does not send query
         * (phone number is stored as varchar in db - due to issues with it being an int, 
         *  such removing the first 0, value of a normal phone number > int value allowed).
         * 
         * If name and city fields are not wholly alphabetical, output error and does not send query.
         * 
         * Else, if all is okay it sends the query and a success message is sent.
         * 
         */
        if (($updatedEmail == $userEmail)
            &&  ($updatedName == $userActualName) && ($updatedAddress == $userAddress) &&

            ($updatedPostcode == $userPostcode) &&
            ($updatedPhoneNumber == $userPhoneNumber) &&
            ($updatedCity == $userCity) && ($userCoach == $sanitisedCoach)
        ) {
            $updateFailed = "Your details have not been updated - you have not changed any fields.";
        } else if (preg_match('~[0-9]~', $sanitisedName)) {
            $updateFailed = "Your details have not been updated - name must only contain letters.";
        } else if (!ctype_digit($sanitisedPhoneNumber)) {
            $updateFailed = "Your details have not been updated - phone number not numeric.";
        } else if (preg_match('~[0-9]~', $sanitisedCity)) {
            $updateFailed = "Your details have not been updated - city must contain letters.";
        } else if ((strlen($updatedPhoneNumber) < 10) || strlen($updatedPhoneNumber) > 13) {
            $updateFailed = "Error with sign up - telephone number out of range boundary. Must be between 10 - 13 digits.";
        } else if ((strlen($sanitisedName) > 35)) {
            $updateFailed = "Error with updating name - name out of range boundary. Must be under 35 characters.";
        } else if ((strlen($sanitisedEmail) > 35)) {
            $updateFailed = "Error with updating email - email out of range boundary. Must be under 55 characters.";
        } else if ((strlen($updatedPhoneNumber) < 10) || strlen($updatedPhoneNumber) > 13) {
            $updateFailed = "Error with sign up - telephone number out of range boundary. Must be between 10 - 13 digits.";
        } else if ((strlen($sanitisedCity) > 35)) {
            $updateFailed = "Error with sign up - city  out of range boundary. Must be under 35 characters.";
        } else if ((strlen($sanitisedPostcode) > 10)) {
            $updateFailed = "Error with sign up - postcode  out of range boundary. Must be under 10 characters.";
        } else if ((strlen($sanitisedAddress) > 100)) {
            $updateFailed = "Error with sign up - address  out of range boundary. Must be under 100 characters.";
        } else if (($sanitisedEmail == null)   || ($sanitisedName == null)
            || ($sanitisedAddress == null) || ($sanitisedPostcode == null) ||  ($sanitisedPhoneNumber == null)
            ||  ($sanitisedCity == null) || ($sanitisedCoach == null)

        ) {
            $updateFailed = "Your details have not been updated - null fields submitted.";
        } else {
            // transaction adapted from online tutorial <https://www.youtube.com/watch?v=CNt9HPqDIVc>
            $conn->autocommit(false);

            $error = array();

            $a = $conn->query("UPDATE webdev_users 
            SET webdev_users.email = '$sanitisedEmail'
              WHERE webdev_users.id = '$userid'");
            if ($a == false) {
                array_push($error, 'Problem updating user details');
                echo $conn->error;
            }
            $b = $conn->query("UPDATE webdev_user_details SET webdev_user_details.name = '$sanitisedName', 
            webdev_user_details.address = '$sanitisedAddress',  webdev_user_details.postcode = '$sanitisedPostcode',  
            webdev_user_details.city = '$sanitisedCity',   webdev_user_details.phone_number = '$sanitisedPhoneNumber',
            webdev_user_details.coach = '$sanitisedCoach'
            WHERE webdev_user_details.user_id = '$userid'");
            if ($b == false) {
                array_push($error, 'Problem updating user details.');
                echo $conn->error;
            }

            /**
             * If error array is not empty, error occured and this need to rollback.
             */
            if (!empty($error)) {
                $conn->rollback();
            } else {

                //commit if all ok
                $conn->commit();
                $updateSuccessful = "Your details have successfully been updated.";
            }
        }
    } // end of checking if the update for personal details is set

}
/**
 * If the coach clicks this, resets the password of the user and emails it to them.
 */
if (isset($_POST['resetClientPassword'])) {

    $userID = $_POST['userID'];

    // get user email 
    $getClientEmail = $conn->prepare("SELECT email, username FROM webdev_users
    WHERE id = ?");
    $getClientEmail->bind_param("i", $userID);
    $getClientEmail->execute();
    $getClientEmail->store_result();
    $getClientEmail->bind_result($userEmail, $userName);
    $getClientEmail->fetch();
    /**
     * Random password generator developed from a mix of the following:
     * https://stackoverflow.com/questions/6101956/generating-a-random-password-in-php
     * https://defuse.ca/generating-random-passwords.htm
     */
    $password_charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!$^*';

    function create_random_password($charset, $pass_size)
    {
        $random_character_number = strlen($charset);
        $pass_gen = '';
        for ($pass_length = 0; $pass_length < $pass_size; $pass_length++) { // set up loop to run for pass size as determined in params
            $random_char = $charset[mt_rand(0, $random_character_number  - 1)]; // generate a random character
            $pass_gen .= $random_char; // concatenate new password
        }
        return $pass_gen;
    }

    $newpassword =  create_random_password($password_charset, 20);
    $generatedPass = $conn->real_escape_string(trim($newpassword)); //sanitise due to possible special characters


    $updatePass = "UPDATE webdev_users SET password = AES_Encrypt('$generatedPass', '09UYO2ELHJ290OYEH22098H9ty')
    WHERE id='$userID'";

    $executeUpdatePass = $conn->query($updatePass);

    if (!$executeUpdatePass) {
        echo $conn->error;
    }


    // Generate message to send to user
    $message = "Dear $userName, \n
    This is an automated message, please do not reply. \n
    You are receiving this email because a request to generate a new password was sent. \n
    Your new, temporary, password is: $newpassword. \n
    Please change this upon your next login. \n

    Kind regards, \n
    Gymafi";



    // Send to the user's email
    mail($userEmail, 'Gymafi - Password Reset', $message);
    // if entered info on system, email changed and new password emailed to them.
    $updateSuccessful = "Password successfully reset. Please check your email.";
}


if (isset($_POST['deleteClient'])) {
    $userID = $_POST['userID'];

    //get user's email to email them if successful.
    $grabEmail = $conn->prepare("SELECT email from webdev_users  WHERE id = ?");
    $grabEmail->bind_param("i", $userID);
    $grabEmail->execute();
    $grabEmail->store_result();
    $grabEmail->bind_result($userEmail);
    $grabEmail->fetch();


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

    $a = $conn->query("DELETE FROM webdev_appointments WHERE user_id = '$userID'");
    if ($a == false) {
        array_push($deleteError, 'Problem deleting appointments from db');
        echo $conn->error;
    }
    $b = $conn->query("UPDATE webdev_groups SET member_one = '0' WHERE member_one = '$userID'");
    if ($b == false) {
        array_push($deleteError, 'Problem removing user as member of group.');
        echo $conn->error;
    }

    $c = $conn->query("UPDATE webdev_groups SET member_two = '0' WHERE member_two = '$userID'");
    if ($c == false) {
        array_push($deleteError, 'Problem removing user as member of group.');
        echo $conn->error;
    }


    $d = $conn->query("UPDATE webdev_groups SET member_three = '0' WHERE member_three = '$userID'");
    if ($d == false) {
        array_push($deleteError, 'Problem removing user as member of group.');
        echo $conn->error;
    }


    $e = $conn->query("UPDATE webdev_groups SET member_four = '0' WHERE member_four = '$userID'");
    if ($e == false) {
        array_push($deleteError, 'Problem removing user as member of group.');
        echo $conn->error;
    }


    $f = $conn->query("DELETE FROM webdev_inbox WHERE recipient = '$userID'");
    if ($f == false) {
        array_push($deleteError, 'Problem deleting inbox from db');
        echo $conn->error;
    }


    $g = $conn->query("DELETE FROM webdev_training_regime WHERE user_id = '$userID'");
    if ($g == false) {
        array_push($deleteError, 'Problem deleting regime from db');
        echo $conn->error;
    }

    $h = $conn->query("DELETE FROM webdev_user_details WHERE user_id = '$userID'");
    if ($h == false) {
        array_push($deleteError, 'Problem deleting details from db');
        echo $conn->error;
    }

    $i = $conn->query("DELETE FROM webdev_user_stats WHERE user_id = '$userID'");
    if ($i == false) {
        array_push($deleteError, 'Problem deleting user stats from db');
        echo $conn->error;
    }

    $j = $conn->query("DELETE FROM webdev_inbox WHERE sender = '$userID'");
    if ($j == false) {
        array_push($deleteError, 'Problem deleting inbox from db');
        echo $conn->error;
    }
    $k = $conn->query("DELETE FROM webdev_testimonials WHERE user_id = '$userID'");
    if ($k == false) {
        array_push($deleteError, 'Problem deleting user from db');
        echo $conn->error;
    }

    $l = $conn->query("DELETE FROM webdev_users WHERE id = '$userID'");
    if ($l == false) {
        array_push($deleteError, 'Problem deleting user from db');
        echo $conn->error;
    }


    /**
     * If array error not empty, error occured and rollback occurs.
     */
    if (!empty($deleteError)) {
        foreach ($deleteError as $thing) {
            echo $thing;
        }
        $conn->rollback();
        $updateFailed = "Client could not be deleted.";
    } else {

        //commit if all ok
        $conn->commit();
        // message user to confirm acc deletion
        $message = "Dear user, \n This is an automated message, please do not reply. 
      \n This is an email to confirm that your account has been deleted. We are sorry to see you go. 
      \n You are welcome to sign up again at any time. We hope to see you again. 
      \n Kind regards,
      \n Gymafi.";
        mail($userEmail, 'Gymafi - Account Deletion', $message);
        $updateSuccessful = "Client successfully deleted.";
    }
}


?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gymafi | Clients </title>
    <link href="../styles/bulma.css" rel="stylesheet">
    <link href="../styles/lightbox.css" rel="stylesheet">
    <link href="../styles/gui.css" rel="stylesheet">
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
    <script src="../script/myScript.js"></script>
    <script src="../script/lightbox.js"></script>

</head>

<!-- Displays log out button-->

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
                        <a class='navbar-item has-text-black has-background-warning' href='manageclients.php'>
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


    <!--Displays all of the current clients linked to the logged in coach-->
    <div id='dashColumns'>

        <div class='column is-9' id='editContentColumn'>
            <article class='message is-dark'>
                <div class='message-header'>
                    <p>
                        <h1 class='title titleHeader'>Update Client Info</h1>
                    </p>

                </div>
                <div class='message-body'>
                    Upon selecting a client, you will be able to perform the following functions:

                    <ul id='clientInfo'>
                        <li>Update their training programme </li>
                        <li> Add comments to, or edit, a session log </li>
                        <li>Update select user data </li>
                        <li>Reset their password, which will be emailed to them </li>
                    </ul>

                    <?php


                    if (isset($updateFailed)) {
                        echo "<p class='displayError'> $updateFailed</p>";
                    } else if (isset($updateSuccessful)) {
                        echo "<p class='displaySucc'> $updateSuccessful</p>";
                    }

                    $getAllClients = "SELECT user_id, name, address, postcode, city, phone_number
                    FROM webdev_user_details
                    WHERE coach = $loggedInCoachId";
                    $executeGetAllClients = $conn->query($getAllClients);

                    if (!$executeGetAllClients) {
                        echo $conn->error;
                    }
                    echo "<div class='columns'>";
                    /*
                    * variable to track pages edit articles printed out,
                    *  when gets to 2 ends current columns row and starts new one
                    */
                    $pageCounter = 0;
                    while ($row = $executeGetAllClients->fetch_assoc()) {
                        $userName = $row['name'];
                        $userID = $row['user_id'];
                        $userAddress = $row['address'];
                        $userPostcode = $row['postcode'];
                        $userCity = $row['city'];
                        $phoneNumber = $row['phone_number'];


                        echo "<div class='column'> 
                        <article class='message is-dark'>
                        <form action='manageclients.php' method='POST'>
                        <div class='message-header'>
                            <p>
                            ", htmlentities($userName, ENT_QUOTES), "  ", htmlentities($phoneNumber, ENT_QUOTES), " 
                                                
                            </p>
        
                        </div>
                        <div class='message-body'>
                        Address: ", htmlentities($userAddress, ENT_QUOTES), "  <br>
                        ", htmlentities($userPostcode, ENT_QUOTES), " <br>
                        ", htmlentities($userCity, ENT_QUOTES), "  <br>
                       
                        <input type='hidden' name='userID' value='$userID'>
                        <input type='hidden' name='client_name' value='$userName'>
                        
    
                        </div>
                       

                        
                        <input type='submit' value='Edit Track' class='button is-success editClientButton' name='editClientTrack'></a>
                        <input type='submit' value='Edit Details' class='button is-info editClientButton' name='editClientDetails'></a>
                        ";
                    ?>
                        <input type='submit' value='Reset Password' class='button is-warning editClientButton' onclick="return confirm(' Are you sure you wish to reset the password of this user?')" name='resetClientPassword'></a>
                        <input type='submit' value='Delete Client' class='button is-danger editClientButton' onclick="return confirm(' Are you sure you wish to delete this user? This CANNOT be undone.')" name='deleteClient'></a>
                        </form>
            </article>
        </div>
    <?php
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


    <!-- page footer-->
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