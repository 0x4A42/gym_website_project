<?php
session_start();
include("conn.php");

?>


<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gymafi | Home</title>
  <link href="styles/bulma.css" rel="stylesheet">
  <link href="styles/gui.css" rel="stylesheet">
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

  <!-- Navigation bar-->
  <nav class="navbar is-dark" role="navigation" aria-label="main navigation">

    <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false">
      <span aria-hidden="true"></span>
      <span aria-hidden="true"></span>
      <span aria-hidden="true"></span>
    </a>
    </div>

    <div id="navbarBasicExample" class="navbar-menu has-background-dark">
      <div class="navbar-start myNavPublic">
        <a class="navbar-item has-background-primary has-text-white" href='index.php'>
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

  <div>

    <div id="indexImg">

      <div id=" imgHeader">
        <?php
        /**
         * Grabs the page content stored in the DB and prints it.
         */
        $getContentOfPage = $conn->prepare("SELECT title, content
         FROM webdev_page_content WHERE id = 5");
        $getContentOfPage->execute();
        $getContentOfPage->store_result();
        $getContentOfPage->bind_result($pageTitle, $contentOne);
        $getContentOfPage->fetch();
        // displays the image and the text on top of it
        echo "
        <p id='indexHeadTitle'>", htmlentities($pageTitle, ENT_QUOTES), "</p><br>
        <p id='indexHeadContent'>", htmlentities($contentOne, ENT_QUOTES), "</p>";
        ?>
        <!-- Buttons that sit on the image header to allow users to find out more/register upon clicking.-->
        <div id="indexButtonsHold">

          <a href='aboutus.php'><button class="button is-primary is-rounded is-large ">About Us</button></a>
          <p id='mobileSpace'> </p>
          <a href='signup.php'><button class="button is-link is-rounded is-large ">Register </button></a>

        </div>
      </div><!-- end of  imgHeader-->

    </div> <!-- end of indexImg-->

    <div id='packageInfo'>
      <div class='columns' id="indexInfo">

        <div class='column'>
          <article class="message is-link">
            <?php
            /**
             * Grabs the page content stored in the DB for the first area of training 
             * that is offered and prints it.
             */
            $getContentOfPage = $conn->prepare("SELECT title, content
             FROM webdev_page_content WHERE id = 6");
            $getContentOfPage->execute();
            $getContentOfPage->store_result();
            $getContentOfPage->bind_result($pageTitle, $contentOne);
            $getContentOfPage->fetch();
            echo "
            <div class='message-header'>",
              htmlentities($pageTitle, ENT_QUOTES),

              "</div>
            <div class='message-body'>",
              htmlentities($contentOne, ENT_QUOTES),
              "</div> <!-- end of message body-->
          </article>
        </div><!-- end of column-->";
            ?>

            <div class='column'>
              <article class='message is-info'>
                <?php
                /**
                 * Grabs the page content stored in the DB for the second area of training 
                 * that is offered and prints it.
                 */
                $getContentOfPage = $conn->prepare("SELECT title, content
             FROM webdev_page_content WHERE id = 7");
                $getContentOfPage->execute();
                $getContentOfPage->store_result();
                $getContentOfPage->bind_result($pageTitle, $contentOne);
                $getContentOfPage->fetch();
                echo "
            <div class='message-header'>",
                  htmlentities($pageTitle, ENT_QUOTES),

                  "</div>
            <div class='message-body'>",
                  htmlentities($contentOne, ENT_QUOTES),
                  "</div> <!-- end of message body-->
          </article>
        </div><!-- end of column-->
        ";
                ?>


                <div class='column'>
                  <article class='message is-success'>
                    <?php
                    /**
                     * Grabs the page content stored in the DB for the third area of training 
                     * that is offered and prints it.
                     */
                    $getContentOfPage = $conn->prepare("SELECT title, content
             FROM webdev_page_content WHERE id = 8");
                    $getContentOfPage->execute();
                    $getContentOfPage->store_result();
                    $getContentOfPage->bind_result($pageTitle, $contentOne);
                    $getContentOfPage->fetch();
                    echo "
                    <div class='message-header'>",
                      htmlentities($pageTitle, ENT_QUOTES),

                      "</div>


                    <div class='message-body'>",
                      htmlentities($contentOne, ENT_QUOTES),
                      "
                    </div> <!-- end of message body-->
                  </article>
                </div><!-- end of column-->
                ";
                    ?>
                    <div class='column'>
                      <article class='message is-warning'>
                        <?php
                        /**
                         * Grabs the page content stored in the DB for the fourth area of training 
                         * that is offered and prints it.
                         */
                        $getContentOfPage = $conn->prepare("SELECT title, content
                           FROM webdev_page_content WHERE id = 9");
                        $getContentOfPage->execute();
                        $getContentOfPage->store_result();
                        $getContentOfPage->bind_result($pageTitle, $contentOne);
                        $getContentOfPage->fetch();
                        echo "
                        <div class='message-header'>",
                          htmlentities($pageTitle, ENT_QUOTES),

                          "</div>
                        <div class='message-body'>",
                          htmlentities($contentOne, ENT_QUOTES),
                          "
                        </div> <!-- end of message body-->
                      </article>
                    </div><!-- end of column-->
                    <div class='column'> </div>
                    <div class='column'> </div>";
                        ?>
                    </div> <!-- end of columns-->

                </div><!-- end of package info-->

            </div> <!-- end of pseudocontainer-->

            <!-- Page footer-->
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