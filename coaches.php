<?php
session_start();
include("conn.php");
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gymafi | Coaches </title>
  <link href="styles/bulma.css" rel="stylesheet">
  <link href="styles/lightbox.css" rel="stylesheet">
  <link href="styles/gui.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
  <script src="script/myScript.js"></script>
  <script src="script/lightbox.js"></script>


</head>

<body class="has-background-grey-lighter" id='coachBody'>
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
              } else if (isset($_SESSION['gymafi_superadmin'])){
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

        <a class="navbar-item has-background-primary has-text-white" href='coaches.php'>
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


  <div class="tile is-ancestor">

    <?php

    // grabs the info of all coaches in the database and displays them to the user
    $coachinfo = "SELECT id, image, img_description FROM webdev_coach";

    $result = $conn->query($coachinfo);

    if (!$result) {
      echo $conn->error;
    }
    $contentID = 10;
    
    while ($row = $result->fetch_assoc()) {
      $imgpath = "images/uploaded/";
      $coachID = $row['id'];
      $imgpath .= $row['image'];
      $imgdesc = $row['img_description'];

      $getContentOfPage = $conn->prepare("SELECT title, content, content_2
      FROM webdev_page_content WHERE id = ?");
      $getContentOfPage->bind_param("i", $contentID);
      $getContentOfPage->execute();
      $getContentOfPage->store_result();
      $getContentOfPage->bind_result($pageTitle, $contentOne, $contentTwo);
      $getContentOfPage->fetch();
      $contentID++;

      echo "<div class='tile is-vertical is-parent coach'>
    <div class='tile is-child box has-background-light'>
      <div class='columns'>
        <div class='column'>
          <p class='title'>", htmlentities($pageTitle, ENT_QUOTES), "</p>
          <div class='column'>
         <a href='$imgpath' data-lightbox='coaches' data-title='", htmlentities($imgdesc, ENT_QUOTES), "'><img src='$imgpath'></a>
        </div> <!-- end of column-->
        <p>", htmlentities($contentOne, ENT_QUOTES), "</p>
        <p>", htmlentities($contentTwo, ENT_QUOTES), "</p>
        </div> <!-- end of column-->
        
      </div> <!-- end of columns-->
      
      <div class='testiBut'> <a class='button is-primary' href='testimonials.php?coachID=$coachID'>
          <strong>Testimonials</strong>
        </a>
      </div> <!-- end of testiBut-->
    </div> <!-- end of is-child box-->
  </div> <!-- end of tile is-4 is-vertical-->";
    }


    ?>


  </div>

  <!-- Page footer-->
  <div class="myFooter" id="myFootCoach">
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