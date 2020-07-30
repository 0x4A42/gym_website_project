<?php
session_start();

include("conn.php");

/**
 * If the contact form is submitted, attempts to validate it and then send the mail.
 */
if (isset($_POST['submitContact'])) {
  $contactName = $_POST['contactName'];
  $sanitisedName = $conn->real_escape_string(trim($contactName));
  $contactEmail = $_POST['contactEmail'];
  $sanitisedEmail = $conn->real_escape_string(trim($contactEmail));
  $contactArea = $_POST['contactArea'];
  $sanitisedArea = $conn->real_escape_string(trim($contactArea));
  $contactSubject = $_POST['contactSubject'];
  $sanitisedSubject = $conn->real_escape_string(trim($contactSubject));
  $contactMsg = $_POST['contactMsg'];
  $sanitisedMsg = $conn->real_escape_string(trim($contactMsg));
  $mailMe = "jbrown88@qub.ac.uk";

  // checks that all fields have been filled in.
  if (($contactName == "") || ($contactEmail == "") || ($contactArea == "")
    || ($contactSubject == "") || ($contactMsg == "")
  ) {
    $messageFailed = "Sorry, your message could not be sent - all relevant fields not filled in.";
  } else if (strlen($sanitisedSubject) > 75) {
    $messageFailed = "Sorry, your message could not be sent - subject length is above 75 characters.";
  } else if (strlen($sanitisedMsg) > 1000) {
    $messageFailed = "Sorry, your message could not be sent - question length is above 1000 characters.";
  } else {


    $msgToSend = "Message from: $contactName.\n
    Message area: $contactArea \n
    Message: $sanitisedMsg";
    // send contact mail to email address for gymafi, my student email for this build. If deployed, would be changed to business' email.
    mail($mailMe, $sanitisedSubject, $msgToSend);

    $thanksSubject = "Thank you for your email";
    $thanksMsg = "Thank you for your email about " . $sanitisedSubject . ". \n 
    We hope to get back to you ASAP.\n
    Kind regards, \n
    Gymafi";

    // email to thank the user for their message.
    mail($sanitisedEmail, $thanksSubject, $thanksMsg);
    $messageSuccess = "Thank you, your message has been sent";
  }
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gymafi | Contact Us</title>
  <link href="styles/bulma.css" rel="stylesheet">
  <link href="styles/gui.css" rel="stylesheet">
  <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
  <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
  <script src="script/myScript.js"></script>


</head>

<body class="has-background-grey-lighter">
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

        <a class="navbar-item has-text-white has-background-primary" href='contact.php'>
          Contact
        </a>

        <a class="navbar-item has-text-white" href='testimonials.php'>
          Testimonials
        </a>
      </div>


    </div>
  </nav>


  <div class="container " id="contactTitle">

    <?php


    $getContentOfPage = $conn->prepare("SELECT  webdev_page_content.title,  webdev_page_content.content, 
    webdev_page_content.content_2
   FROM webdev_page_content
   WHERE webdev_page_content.id = 13");
    $getContentOfPage->execute();
    $getContentOfPage->store_result();
    $getContentOfPage->bind_result($contentTitle, $contentOne, $contentTwo);
    $getContentOfPage->fetch();
    echo "
           <h2 class='title'>", htmlentities($contentTitle, ENT_QUOTES), "</h2>
    <h3 class='subtitle is-6'>
    $contentOne
    </h3>
    
    <h3 class='subtitle is-6'>", htmlentities($contentTwo, ENT_QUOTES), "</h3>";
    ?>

  </div>

  <!-- Contact form-->
  <form action='contact.php' method='POST' id='actualContactForm'>
    <div id="myForm">
      <div class="container" id="contactForm">
        <form action='contact.php' method='post'>
          <!-- Contact user's name-->
          <div class="field is-horizontal">
            <div class="field-label is-normal">
              <label class="label">From</label>
            </div>
            <div class="field-body">
              <div class="field">

                <p class="control is-expanded has-icons-left">

                  <input class="input" id="nameContact" type="text" placeholder="Name" name='contactName'>

                  <span class="icon is-left">
                    <i class="fas fa-user"></i>
                  </span>
                </p>
              </div>
              <p class="help is-danger" id="nameContactWarn">
              </p>

              <!-- Email of the person contacting, used to send email to confirm sending of email to, as well as for busines
            to eventually respond to -->
              <div class="field">
                <p class="control is-expanded has-icons-left has-icons-right">
                  <input class="input" id="emailContact" type="email" placeholder="Email" name='contactEmail'>
                  <span class="icon is-left">
                    <i class="fas fa-envelope"></i>
                  </span>
                </p>
              </div>
              <p class="help is-danger" id="emailContactWarn">

              </p>
            </div>
          </div>

          <!-- Drop down to select query area-->
          <div class="field is-horizontal">
            <div class="field-label is-normal">
              <label class="label">Query Area</label>
            </div>
            <div class="field-body">
              <div class="field is-narrow">
                <div class="control">
                  <div class="select is-fullwidth">
                    <select name='contactArea'>
                      <option>General</option>
                      <option>Availability</option>
                      <option>Pricing</option>
                      <option>Other</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Subject title of the query-->
          <div class="field is-horizontal">
            <div class="field-label is-normal">
              <label class="label subjectlab">Subject</label>
            </div>
            <div class="field-body">
              <div class="field">
                <div class="control">
                  <input class="input" id="subjectContact" type="text" placeholder="e.g. Pricing Query" name='contactSubject'>
                </div>
                <p class="help is-danger" id="subjectContactWarn">

                </p>
              </div>
            </div>
          </div>
          <!-- Actual query body-->
          <div class="field is-horizontal">
            <div class="field-label is-normal">
              <label class="label">Question</label>
            </div>
            <div class="field-body">
              <div class="field">
                <div class="control">
                  <textarea class="textarea" id="questionContact" placeholder="Explain how we can help you" name='contactMsg'></textarea>
                </div>
                <p class="help is-danger" id="questionContactWarn">

                </p>
              </div>
            </div>
          </div>

          <div class="field is-horizontal">
            <div class="field-label">
              <!-- Left empty for spacing -->
            </div>
            <div class="field-body">
              <div class="field">
                <div class="control">
                  <input type='submit' class='button is-success contactIfValid' id='submitContact' value='Submit' name='submitContact'>
                </div>
                <?php
                if (isset($messageFailed)) {
                  echo "<p id='conError'> $messageFailed</p>";
                } else if (isset($messageSuccess)) {
                  echo "<p id='conSucc'> $messageSuccess</p>";
                }
                ?>
              </div>
            </div>
          </div>
        </form>
      </div> <!-- end of container-->
    </div> <!-- end of myForm-->
  </form>


  <!-- Page footer-->
  <div class="myFooter" id='contactFooter'>
    <footer class="footer has-background-dark alsoMyFooter" id='contactFoot'>
      <div class="content has-text-centered has-text-white">
        <p>
          <span id="boldFoot">CSC7062 Project</span> by Jordan Brown (40282125).
        </p>
      </div>
    </footer>
  </div>
</body>

</html>