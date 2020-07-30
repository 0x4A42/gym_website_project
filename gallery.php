<?php
session_start();
include("conn.php");


if (isset($_SESSION['gymafi_coachid'])) {
  header("location: dashboard.php");
} else if (
  !(isset($_SESSION['gymafi_coachid'])) && !(isset($_SESSION['gymafi_superadmin']))
  && !(isset($_SESSION['gymafi_userid']))
) {
  header("location: login.php");
}
if (isset($_SESSION['gymafi_userid'])) {
  $userid = $_SESSION['gymafi_userid'];
} else if (isset($_SESSION['gymafi_superadmin'])) {
  $userid = $_SESSION['gymafi_superadmin'];
}


/**
 * If the user submits an upload photo request, attempts to process it.
 * Puts the file through validation checks such as ensuring file type is correct and a file has actually been 
 * uploaded to /temp, as possible for file name to get posted but file not to have been uploaded.
 * If passes all checks, moves file to /uploaded file and writes record to db.
 */
if (isset($_POST['uploadPhoto'])) {


  $user = $userid;
  $filename = $_FILES['uploadImage']['name'];
  $sanitisedFileName = $conn->real_escape_string(trim($filename));

  $filetemp = $_FILES['uploadImage']['tmp_name'];


  $description = $_POST['imgDescription'];
  $sanitisedDescription = $conn->real_escape_string(trim($description));



  // check if file type of uploaded file is one of those that are permitted.
  if ($filetemp != null) {
    $fileExt = pathinfo($sanitisedFileName);
    if (!(strpos($filetemp, "."))) {
      if (
        $fileExt["extension"] == "png" || $fileExt["extension"] == "Png"
        || $fileExt["extension"] == "PNg" || $fileExt["extension"] == "PNG"
        || $fileExt["extension"] == "pNg" || $fileExt["extension"] == "pnG"
        || $fileExt["extension"] == "pNG" || $fileExt["extension"] == "PnG"
        ||  $fileExt["extension"] == "jpg" || $fileExt["extension"] == "jPg"
        || $fileExt["extension"] == "jpG" || $fileExt["extension"] == "jPG"
        || $fileExt["extension"] == "JPG" || $fileExt["extension"] == "JPg"
        || $fileExt["extension"] == "Jpg" || $fileExt["extension"] == "JpG"
        ||  $fileExt["extension"] == "gif" || $fileExt["extension"] == "gIf"
        || $fileExt["extension"] == "giF" || $fileExt["extension"] == "gIF"
        || $fileExt["extension"] == "Gif" || $fileExt["extension"] == "GIf"
        || $fileExt["extension"] == "GiF" || $fileExt["extension"] == "GIF"
        || $fileExt["extension"] == "jpeg"
      ) {

        /**
         * Checks if file with this name already exists on the server, if so incrementally changes the name with an 
         * increasing int until it finds a name that is not taken.
         * Then, processes the upload/transfer to db.
         * Adapted from: https://stackoverflow.com/questions/16136519/php-rename-file-name-if-exists-append-number-to-end
         */



        $actual_name = pathinfo($sanitisedFileName, PATHINFO_FILENAME);
        $original_name = $actual_name;
        $extension = pathinfo($sanitisedFileName, PATHINFO_EXTENSION);

        $i = 1;
        while (file_exists('images/uploaded/' . $actual_name . "." . $extension)) {
          $actual_name = (string) $original_name . $i;
          $filename = $actual_name . "." . $extension;
          $i++;
        }


        /**
         * If gotten here, passed all checks and file name is unique - therefore,
         * write to database and move file
         */
        $uploadImageToDB = "INSERT INTO webdev_images (description, path, user_id) 
        VALUES ('$sanitisedDescription', '$filename', '$user')";

        $result =  $conn->query($uploadImageToDB);

        if (!$result) {
          echo $conn->error;
        }

        move_uploaded_file($filetemp, "images/uploaded/$filename");
      } else {
        $fileUploadError = "File type not supported. Please try a different image.";
      }
    } else {
      $fileUploadError = "File error - check file has been selected and is <2mb. Please try a different image.";
    }
  }
} //closing of posted data


/**
 * If a user clicks on the button to edit a photo, allows them to edit the description of it. 
 */
if (isset($_POST['editPhoto'])) {
  $photoToEdit = $_POST['photoToEdit'];


  //get current description to compare
  $getDescription = $conn->prepare("SELECT description FROM webdev_images
   WHERE id = ?");
  $getDescription->bind_param("i", $photoToEdit);
  $getDescription->execute();
  $getDescription->store_result();
  $getDescription->bind_result($currentDescription);
  $getDescription->fetch();

  echo "
  <div class='modal is-active' id='updateDesc'>
  <div class='modal-background'></div>
  <div class='modal-card'>
    <header class='modal-card-head'>
      <p class='modal-card-title'>Update Description</p>
      <button class='delete cancelUpdate' aria-label='close' ></button>
    
    </header>
  
 <section class='modal-card-body'>

<form action='gallery.php' method='POST' id='attemptUpdateDesc'>
  <div class='field'>
  


    <div class='field'>
    <label class='label'>Please enter your updated photo description </label>
    <div class='control'>";


  /** 
   * if current description exists, prints input area with that as current value,
   * else, displays empty input text box.
   */
  if ($currentDescription != null) {
    echo " <input class='input' type='text' name='description' value='$currentDescription'>";
  } else {
    echo " <input class='input' type='text' name='description' placeholder='Week 10 - 5kg down'>";
  }
  echo " 
  </div>
  
  <input type='hidden' id='apptID' name='photoToEdit' value='$photoToEdit'>


<footer class='modal-card-foot'>
<input type='submit' class='button is-success'  value='Update description' name='editPhotoSubmit'>

</footer>


</form>
</section>
</div>
</div>";
}

/**
 * If a user submits the edit photo form, attempts to validate and process it
 * to update the value in the DB
 */
if (isset($_POST['editPhotoSubmit'])) {
  $photoToEdit = $_POST['photoToEdit'];
  $description = $_POST['description'];
  $sanitisedDescription = $conn->real_escape_string(trim($description));

  //get current description to compare
  $getDescription = $conn->prepare("SELECT description FROM webdev_images
  WHERE id = ?");
  $getDescription->bind_param("i", $photoToEdit);
  $getDescription->execute();
  $getDescription->store_result();
  $getDescription->bind_result($currentDescription);
  $getDescription->fetch();

  if ($description == $currentDescription) {
    $editError = "Edit not processed - description is the same as current.";
  } else {
    $updateDescription = "UPDATE webdev_images 
  SET description = '$sanitisedDescription'
  WHERE id = $photoToEdit";
    $executeUpdateDescription = $conn->query($updateDescription);
    if (!$executeUpdateDescription) {
      echo $conn->error;
    }
    $editSuccess = "Your photo description has successfully been updated.";
  }
}




/**
 * If the user clicks the button to delete a photo, attempts to process it.
 */
if (isset($_POST['deletePhoto'])) {
  $imgID = $_POST['photoToDelete'];


  // get path of the photo to delete
  if (isset($_SESSION['gymafi_userid'])) {
    $selectPhotoToDelete = $conn->prepare("SELECT path FROM webdev_images WHERE id= ? AND user_id =? ");
    $selectPhotoToDelete->bind_param("ii", $imgID, $userid);
  } else if (isset($_SESSION['gymafi_superadmin'])) {  // if admin, no need to confirm you own photo
    $selectPhotoToDelete = $conn->prepare("SELECT path FROM webdev_images WHERE id= ? ");
    $selectPhotoToDelete->bind_param("i", $imgID);
  }


  $selectPhotoToDelete->execute();
  $selectPhotoToDelete->store_result();
  $selectPhotoToDelete->bind_result(
    $pathToDel
  );
  $selectPhotoToDelete->fetch();

  $file_to_delete = "images/uploaded/$pathToDel";

  unlink($file_to_delete); // deletes file from webserver 


  if (isset($_SESSION['gymafi_userid'])) {
    $deletePhoto = "DELETE FROM webdev_images WHERE id='$imgID' AND user_id ='$userid';";
  } else if (isset($_SESSION['gymafi_superadmin'])) {
    $deletePhoto = "DELETE FROM webdev_images WHERE id='$imgID'"; // if admin, no need to confirm you own photo
  }
 

  $executeDeletePhoto = $conn->query($deletePhoto);


  if (!$executeDeletePhoto) {
    echo $conn->error;
    $deleteError = "Sorry, your photo could not be deleted.";
  } else {
    $successfulDelete = "Your photo has been be deleted.";
  }
}



?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gymafi | Gallery</title>
  <link href="styles/bulma.css" rel="stylesheet">
  <link href="styles/lightbox.css" rel="stylesheet">
  <link href="styles/gui.css" rel="stylesheet">
  <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
  <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
  <script src="script/lightbox.js"></script>
  <script src="script/myScript.js"></script>




</head>

<body class="has-background-grey-lighter">

  <!--If not logged in, displays login/sign up buttons. If logged in ,displays log out button. -->
  <?php
  if (isset($_SESSION['gymafi_userid'])) {

    echo "
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

        <a class='navbar-item has-background-dark has-text-white' href='performance.php'>
          Performance Log
        </a>
        <a class='navbar-item has-text-white has-background-primary'>
          Your Gallery
        </a>




      </div> <!-- end of navbarBasicExample-->


    </div>
  </nav>";
  } else if (isset($_SESSION['gymafi_superadmin'])) {
    echo "
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
<div class='hero-body has-background-success'>
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

<nav class='navbar is-success' role='navigation' aria-label='main navigation'>


<a role='button' class='navbar-burger' aria-label='menu' aria-expanded='false'>
    <span aria-hidden='true'></span>
    <span aria-hidden='true'></span>
    <span aria-hidden='true'></span>
</a>
</div>

<div id='navbarBasicExample' class='navbar-menu has-background-success'>
    <div class='navbar-start myNav'>
        <a class='navbar-item has-text-black ' href='admin/superadmin.php'>
            Admin Panel
        </a>


        <a class='navbar-item has-text-black has-background-warning' href='gallery.php'>
            Gallery
        </a>



    </div> <!-- end of nav-bar item-->
</div> <!-- end of navbarBasicExample-->


</div>
</nav>

";
  }


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

    <!-- Allows the user to upload a photo, alongside a small caption. -->
    <div id='dashColumns'>
      <div class='columns'>
        <div class='column is-3'>
          <article class='message is-link'>
            <div class='message-header'>

              Upload a photo
            </div>

            <div class='message-body'>
              <form action='gallery.php' method='POST' enctype='multipart/form-data'>

                <div id='fileUploader' class='file has-name'>
                  <label class='file-label'>
                    <!-- Restricts the files shown when the user tries to select a file -->
                    <input class='file-input' type='file' name='uploadImage' accept='image/png, image/gif, image/jpeg'>
                    <span class='file-cta'>
                      <span class='icon is-small is-left'>
                        <i class='fas fa-upload'></i>
                      </span>
                      <span class='file-label'>
                        Choose a file…
                      </span>
                    </span>
                    <span class='file-name'>
                      placeholder_file.png
                    </span>
                  </label>
                </div>
                <p>Accepted formats: png, gif, jpeg.</p>
                <p>Description: <input class='input' type='text' name='imgDescription'></p>
                <p class='imageButtons'><input type='submit' class='button is-primary' value='Upload' name='uploadPhoto'> </p>
                <?php
                if (isset($fileUploadError)) {
                  echo "<p class='displayError'>$fileUploadError</p>";
                }
                ?>
              </form>
            </div> <!-- end of message body-->
          </article>

          <!-- Changes the file name shown on the upload section to be whatever file the user selects
          Code from https://bulma.io/documentation/form/file/ -->
          <script>
            const fileInput = document.querySelector('#fileUploader input[type=file]');
            fileInput.onchange = () => {
              if (fileInput.files.length > 0) {
                const fileName = document.querySelector('#fileUploader .file-name');
                fileName.textContent = fileInput.files[0].name;
              }
            }
          </script>

          <!-- Displays a list of the existing photos the user has uploaded, 
          allows them to edit the description caption of the photo-->
          <div class='columns'>
            <div class='column'>
              <article class='message is-warning'>
                <div class='message-header'>

                  Edit Photo Description

                </div>

                <div class='message-body'>
                  <form action='gallery.php' method='POST' id='editPhotoDesc'>
                    <p> Select a photo to edit the description of: <div class='select'>
                        <select name='photoToEdit'>
                          <?php
                          if (isset($_SESSION['gymafi_userid'])) {
                            $getAllFiles = "SELECT * FROM webdev_images WHERE user_id = '$userid'";
                          } else if (isset($_SESSION['gymafi_superadmin'])) {
                            $getAllFiles = "SELECT * FROM webdev_images ";
                          }
                         
                          $executeGetAllImages = $conn->query($getAllFiles);
                          if (!$executeGetAllImages) {
                            echo $conn->error;
                          } else {
                            while ($row = $executeGetAllImages->fetch_assoc()) {
                              $imgID = $row['id'];
                              $description = $row['description'];
                              $path = $row['path'];

                              echo "<option value='$imgID'>", htmlentities($description, ENT_QUOTES), "(", htmlentities($path, ENT_QUOTES), ")</option>";
                            }
                          }

                          echo "</select>
  </div>
  ";
                          if (isset($editError)) {
                            echo "<p class='displayError'> $editError</p>";
                          } else if (isset($editSuccess)) {
                            echo "<p class='displaySucc'> $editSuccess</p>";
                          }
                          ?>
                          <p class='deletePhoto imageButtons'><input type='submit' class='button is-warning editPhoto' value='Edit Photo' name='editPhoto'> </p>
                  </form>
                </div> <!-- end of message body-->
              </article>
            </div>
          </div>

          <!-- Displays a list of the existing photos the user has uploaded, 
          allows them to delete the photo-->
          <div class='columns'>
            <div class='column'>
              <article class='message is-danger'>
                <div class='message-header'>

                  Delete a Photo

                </div>

                <div class='message-body'>
                  <form action='gallery.php' method='POST' id='deletePhotos'>
                    <p> Select a photo to delete: <div class='select'>
                        <select name='photoToDelete'>
                          <?php
                          if (isset($_SESSION['gymafi_userid'])) {
                            $getAllFiles = "SELECT * FROM webdev_images WHERE user_id = '$userid'";
                          } else if (isset($_SESSION['gymafi_superadmin'])) {
                            $getAllFiles = "SELECT * FROM webdev_images";
                          }
                          $executeGetAllImages = $conn->query($getAllFiles);
                          if (!$executeGetAllImages) {
                            echo $conn->error;
                          } else {
                            while ($row = $executeGetAllImages->fetch_assoc()) {
                              $imgID = $row['id'];
                              $description = $row['description'];
                              $path = $row['path'];

                              echo "<option value='$imgID'>", htmlentities($description, ENT_QUOTES), "(", htmlentities($path, ENT_QUOTES), ")</option>";
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
                          <!-- Button to allow deletion, has an on screen confirmation box to ensure user meant to delete -->
                          <p class='deletePhoto imageButtons'><input type='submit' class='button is-danger deletePhoto' onclick="return confirm('Are you sure you wish to delete this photo?')" value='Delete Photo' name='deletePhoto'> </p>
                  </form>
                </div> <!-- end of message body-->
              </article>
            </div>
          </div>

        </div><!-- end of column-->



        <!-- Displays all of the user's currently uploaded photos. 
      Clicking on them will open a lightbox which displays their description 
      as a caption below it.-->
        <div class='column is-7 ' id='rightColumns'>
          <article class='message is-dark '>
            <div class='message-header '>
              <p class='profileBadgeText '>
                <h1 class='title' id='photoHead'>All Uploaded Photos</h1>
              </p>

            </div>
            <div class='message-body'>



              <?php
              if (isset($_SESSION['gymafi_userid'])) {
                $grabAllPhotos = "SELECT * FROM webdev_images WHERE user_id='$userid';";
              } else if (isset($_SESSION['gymafi_superadmin'])) {
                $grabAllPhotos = "SELECT * FROM webdev_images";
              }

              $executeGrabAllPhotos = $conn->query($grabAllPhotos);
              $pictureCount = 0;
              echo "<div class='columns'>";
              while ($row = $executeGrabAllPhotos->fetch_assoc()) {
                $path = $row['path'];
                $description = $row['description'];

                if ($pictureCount % 4 == 0) {
                  echo "</div>
          <div class='columns'>";
                }
                echo "
         <div class='column'> 
         <a href='images/uploaded/$path' data-lightbox='userphotos' data-title='", htmlentities($description, ENT_QUOTES), "'><img src='images/uploaded/$path'></a>
         </div>";

                $pictureCount++;
              }

              ?>
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


  <!-- page footer-->
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