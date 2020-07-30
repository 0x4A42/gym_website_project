<?php
session_start();
include("conn.php");
// kicks user to home page if logged in
if (isset($_SESSION['gymafi_userid'])) {

    header("location: index.php");
}

/**
 * Generates a random code to send to the user's email to confirm their identity 
 *  developed from a mix of the following:
 * https://stackoverflow.com/questions/6101956/generating-a-random-password-in-php
 * https://defuse.ca/generating-random-passwords.htm
 */

if (isset($_POST['generateCode'])) {
    $sanitisedUser = $conn->real_escape_string(trim($_POST['name']));
    $sanitisedEmail = $conn->real_escape_string(trim($_POST['email']));

    if (($sanitisedUser != null) && ($sanitisedEmail != null)) {

        $checkIfUser = "SELECT * FROM webdev_users WHERE username = '$sanitisedUser' AND email ='$sanitisedEmail'";
        $executeCheckIfUser = $conn->query($checkIfUser);
        $numUser = $executeCheckIfUser->num_rows;
        if ($numUser > 0) {


            $random_code_charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!$^*';

            function create_random_password($charset, $code_size)
            {
                $random_character_number = strlen($charset);
                $code_gen = '';
                for ($code_length = 0; $code_length < $code_size; $code_length++) { // set up loop to run for pass size as determined in params
                    $random_char = $charset[mt_rand(0, $random_character_number  - 1)]; // generate a random character
                    $code_gen .= $random_char; // concatenate new password
                }
                return $code_gen;
            }

            $new_code =  create_random_password($random_code_charset, 7);

            $message = "Dear $sanitisedUser, \n
            This is an automated message, please do not reply. \n
            You are receiving this email because a request to generate a new password was sent. \n
            Your new verification code is: $new_code. \n
            Please enter this on the reset password form. \n

            Kind regards, \n
            Gymafi";
            mail($sanitisedEmail, 'Gymafi - Password Reset Verification Code', $message);

            $updateResetCode = "UPDATE webdev_users SET reset_code = AES_Encrypt('$new_code', 'QWOHJ219302HO0962')
    WHERE username = '$sanitisedUser' AND email = '$sanitisedEmail'";
            $executeUpdateResetCode = $conn->query($updateResetCode);
            if (!$executeUpdateResetCode) {
                echo $conn->error;
            }


            echo "
    <div class='modal is-active' id='generateCode'>
        <div class='modal-background'></div>
        <div class='modal-card'>
          <header class='modal-card-head'>
            <p class='modal-card-title'>Reset Password</p>
            <button class='delete cancelUpdate' aria-label='close' ></button>
          
          </header>
         
       <section class='modal-card-body'>
   
      <form action='resetpassword.php' method='POST' id='attemptReset'>
        <div class='field'>
        Please check your email for a code to reset your password.
          </div>
          <input type='hidden' id='logID' name='name' value='$sanitisedUser'>
          <input type='hidden' id='logID' name='email' value='$sanitisedEmail'>
 
      <footer class='modal-card-foot'>
      <input type='submit' class='button is-success'  value='Attempt Reset' name='attemptValidate'>

    </footer>


      </form>
    </section>
    </div>
    </div>";
        } else {
            $resetFail = "Email or username do not match.";
        }
    } else {
        $resetFail = "Email or username blank.";
    }
}

/**
 * Brings up a modal to allow the user to enter the randomly generated code sent to their email
 */
if (isset($_POST['attemptValidate'])) {

    $userName = $_POST['name'];
    $sanitisedUser = $conn->real_escape_string(trim($userName));
    $email = $_POST['email'];
    $sanitisedEmail = $conn->real_escape_string(trim($email));

    if (($userName != null) && ($email != null)) {

        $checkIfUser = "SELECT * FROM webdev_users WHERE username = '$sanitisedUser' AND email ='$sanitisedEmail'";
        $executeCheckIfUser = $conn->query($checkIfUser);
        $numUser = $executeCheckIfUser->num_rows;
        if ($numUser > 0) {
            echo "<div class='modal is-active' id='attemptReset'>
    <div class='modal-background'></div>
    <div class='modal-card'>
      <header class='modal-card-head'>
        <p class='modal-card-title'>Enter Reset Code</p>
        <button class='delete cancelUpdate' aria-label='close' ></button>
      
      </header>
     
    <section class='modal-card-body'>

  <form action='resetpassword.php' method='POST' id='attemptReset'>
    <div class='field'>
      <label class='label'>Enter the reset code that was emailed to you: </label>
      <div class='control'>
        <input class='input' type='text' placeholder='ABC123' name='resetCode'>
      </div>
      
<p class='help is-danger' id='resetCodeWarn'> </p>
    </div>
    


  <input type='hidden' id='logID' name='name' value='$userName'>
  <input type='hidden' id='logID' name='email' value='$email'>
  

  <footer class='modal-card-foot'>
  <input type='submit' class='button is-success' value='Save changes' name='resetPass'>

</footer>


  </form>
</section>
</div>
</div>";
        } else {
            $resetFail = "Email or username do not match.";
        }
    } else {
        $resetFail = "Email or username blank.";
    }
}

if (isset($_POST['resetPass'])) {     // if not kicked out, tries to authenticate their password reset

    /**
     * Once user inputs their username, generates a new password randomly, sets it and emails it to them. 
     */
    $resetCodeEntered = $_POST['resetCode'];
    $sanitisedResetCodeEntered = $conn->real_escape_string(trim($resetCodeEntered));
    $userName = $_POST['name'];
    $email = $_POST['email'];

    // checks user details in database, decrypts password and compared with string input. 
    $checkuser = "SELECT * FROM webdev_users WHERE username = '$userName' OR email = '$email' AND AES_DECRYPT(reset_code, 'QWOHJ219302HO0962') = '$sanitisedResetCodeEntered'";

    $result = $conn->query($checkuser);

    if (!$result) {
        echo $conn->error;
    }

    $num = $result->num_rows;
    // if returns any results, generates a session and logs them in. 
    if ($num > 0) {


        if (($user != null) && ($email != null)) {


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
    WHERE username = '$userName' AND email = '$email'";

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
            mail($email, 'Gymafi - Password Reset', $message);
            // if entered info on system, email changed and new password emailed to them.
            $resetSuccess = "Password successfully reset. Please check your email.";
        } else {
            $resetFail = "Email or username blank.";
        }
    } else {
        $resetFail = "Incorrect verification code.";
    }
}
?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gymafi | Reset Password</title>
    <link href="styles/bulma.css" rel="stylesheet">
    <link href="styles/gui.css" rel="stylesheet">
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
    <script src="script/myScript.js"></script>


</head>


<body class="has-background-grey-lighter">
    <!--If not logged in, displays login/sign up buttons. If logged in ,displays log out button. -->
    <?php
    if (!isset($_SESSION['gymafi_userid'])) {
    ?>
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
    <?php
    } 
    ?>

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

                <a class="navbar-item has-background-dark has-text-white" href='coaches.php'>
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
    WHERE webdev_page_content.id = 14");
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

    <form action='resetpassword.php' method='POST'>
        <div id="outerContain">
            <div id="loginBorder">
                <div id="innerContain ">


                    <div class="columns">
                        <div class="column"> </div>
                        <div class="column loginCol">
                            <div class="field loginCol">
                                <p class="control has-icons-left">
                                    <?php
                                    if ((isset($userName)) && ($userName != "") && ($userName != null)) {
                                        echo "<input class='input' type='text' id='usernInput' value='$userName' name='name'>";
                                    } else {
                                        echo "<input class='input' type='text' id='usernInput' placeholder='Username' name='name'>";
                                    }
                                    ?>
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-user"></i>
                                    </span>

                                </p>
                            </div> <!-- end of column-->
                            <p class="help is-danger" id="userWarn">
                            </p>
                        </div> <!-- end of column-->


                        <div class="column">
                        </div> <!-- end of column-->
                    </div> <!-- end of columns-->
                    <div class="columns">
                        <div class="column"> </div>
                        <div class="column">
                            <div class="field">
                                <p class="control has-icons-left">
                                    <?php
                                    if ((isset($email)) && ($email != "") && ($email != null)) {
                                        echo "<input class='input' type='email' id='emailInputLogin' value='$email' name='email'>";
                                    } else {
                                        echo "<input class='input' type='email' id='emailInputLogin' placeholder='Example@email.com' name='email'>";
                                    }
                                    ?>

                                    <span class="icon is-small is-left">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                </p>
                            </div>
                            <p class="help is-danger" id="emailWarn">
                            </p>

                        </div> <!-- end of column-->

                        <div class="column">
                        </div> <!-- end of column-->
                    </div> <!-- end of columns-->

                </div> <!-- end of field-->

                <div class="columns">


                    <div class="column">
                        <div class="field">
                            <p class="control">
                                <input type='submit' class='button is-danger' id='formButtonsForgot' value='Generate Code' name='generateCode'>
                            </p>
                            <p class="control">
                                <input type='submit' class='button is-info' id='attemptValidate' value='Already Have Code' name='attemptValidate'>
                            </p>
                        </div> <!-- end of field-->
                    </div> <!-- end of column-->





                </div> <!-- end of columns-->
    </form>

    <!-- if wrong login details, displays error-->
    <?php
    if (isset($resetFail)) {
        echo "<p class='regError'>$resetFail</p>";
    } else if (isset($resetSuccess)) {
        echo "<p id='resSuc'>$resetSuccess</p>";
    }

    ?>



    </div>
    </div> <!-- end of innerContainer-->
    </div> <!-- end of outerContainer-->



    <div class="myFooter" id='myFooterForgot'>
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