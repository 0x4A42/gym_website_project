<?php
session_start();
include("conn.php");

/**
 * Checks if user is either logged in as a coach or a client.
 * If not, kicks them to the login page.
 */
if (isset($_SESSION['gymafi_userid'])) {
  $userid = $_SESSION['gymafi_userid'];
  $getNumberOfMessages = "SELECT * FROM webdev_inbox
  WHERE recipient = $userid
  AND hide = 0";
} else if (isset($_SESSION['gymafi_coachid'])) {
  $loggedInCoachId = $_SESSION['gymafi_coachid'];
  $getNumberOfMessages = "SELECT * FROM webdev_inbox
WHERE recipient = $loggedInCoachId
AND hide = 0";
} else if (isset($_SESSION['gymafi_superadmin'])) {
  header("location: admin/superadmin.php");
} else {
  header("location: login.php");
}

// variable to display how many unread messages user has in tab header
$executeGetNumberOfMessages = $conn->query($getNumberOfMessages);
$numOfMessages = $executeGetNumberOfMessages->num_rows;


/**
 * If a client (normal user) has clicked the button to message their coach, displays this modal
 * Will allow them to send a message, subject and attachment. 
 */

if (isset($_POST['msgCoach'])) {
  if (!isset($_POST['msgCoach'])) {
    $messageError = "Cannot compose message - empty entry selected.";
  }
  // finds their coach's ID to store in the DB.
  $coachToMsg = $_POST['messageCoach'];
  $coachToMessage = intval($coachToMsg);

  //gets id and name of coach to display
  $getCoachID = $conn->prepare("SELECT name, id FROM webdev_coach WHERE id = ? ");
  $getCoachID->bind_param("i", $coachToMessage);
  $getCoachID->execute();
  $getCoachID->store_result();
  $getCoachID->bind_result($coachName, $coachID);
  $getCoachID->fetch();

  echo "<div class='modal is-active' id='addLog'>
  <div class='modal-background'></div>
  <div class='modal-card'>
    <header class='modal-card-head'>
      <p class='modal-card-title'>Compose New Message</p>
      <button class='delete cancelUpdate' aria-label='close' ></button>
    
    </header>
   
    <section class='modal-card-body'>
   
      <form action='inbox.php' method='POST' enctype='multipart/form-data' id='userMsgCoach'>

        <div class='field'>
          <label class='label'>Recipient: </label>
          <div class='control'>
            <input class='input' type='text' value='$coachName' name='coachName' readonly>
          </div>
        </div>

        <input type='hidden' id='coachID'  value='$coachID' name='messageRecipient' readonly>

        <div class='field'>
        <label class='label'>Subject: </label>
        <div class='control'>
          <input class='input' type='text' id='subjectInput' placeholder='Question about nutrition' name='messageSubject'>
        </div>
        <p class='subjectWarn help is-danger'></p>
      </div>
      <input type='hidden' id='userID' value='$userid' name='messageSender' readonly>


      <div class='field'>
      <label class='label'>Message: </label>
      <div class='control'>
        <textarea class='textarea' id='messageBody' placeholder= 'Put your message here' name='messageText'></textarea>
      </div>
      <p class='messageWarn help is-danger'></p>
    </div>


    <div id='fileUploader' class='file has-name field' >
    <label class='label' >Attachment: </label>
    
    <label class='file-label' id='attachLabel'> 
    <!-- Allowed file types adapted from https://stackoverflow.com/questions/52047925/uploading-docx-files-using-php-->
      <input class='file-input' type='file' name='msgAttachment' accept='image/png, image/gif, image/jpeg, 
      text/plain, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document'>
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
  <p>Accepted formats: png, gif, jpeg, pdf, .txt, .docx.</p>
  <p> File must be <2mb.</p>


      
      <footer class='modal-card-foot'>
      <input type='submit' class='button is-success userMessageCoachButton' id='coachMsgSubmit' value='Submit Message' name='coachMsgSubmit'>

    </footer>


      </form>
    </section>
   
  </div>
</div>
<!-- Changes the file name shown on the upload section to be whatever file the user selects
From https://bulma.io/documentation/form/file/ -->
<script>const fileInput = document.querySelector('#fileUploader input[type=file]');
fileInput.onchange = () => {
  if (fileInput.files.length > 0) {
    const fileName = document.querySelector('#fileUploader .file-name');
    fileName.textContent = fileInput.files[0].name;
  }
}
</script>";
}



/**
 * This is the process for the form when a client (normal user) clicks on compose message and then sends it.
 * Sanitises all input from the user to protect against SQL injections.
 * Performs validation to ensure data types (e.g if sender ID is not numeric, which is hidden from the user, 
 * will print out an error)
 * Then determines if  an attachment has been set. Performs a check to see if a file has actually been uploaded, 
 * possibility of file name being sent but file not being uploaded (over limit), 
 * so only performs the 'attachment query' if file exists in temp folder.
 * If a file has been posted, then checks the file extensions. If not compatible, message is not sent and error 
 * is displayed.
 * If no attachment, attempt to write to DB without putting anything in the attachment row.
 */


if (isset($_POST['coachMsgSubmit'])) {
  $coachName = $_POST['coachName'];
  $messageRecipient = $_POST['messageRecipient'];
  $sanitisedRecipient = $conn->real_escape_string(trim($messageRecipient));
  $messageSubject = $_POST['messageSubject'];
  $sanitisedSubject = $conn->real_escape_string(trim($messageSubject));
  $messageSender = $_POST['messageSender'];
  $sanitisedSender = $conn->real_escape_string(trim($messageSender));
  $messageText = $_POST['messageText'];
  $sanitisedText = $conn->real_escape_string(trim($messageText));
  $messageAttachment = $_FILES['msgAttachment']['name'];

  /**
   * Checks if an attachment has been sent, if so checks file type.
   * If no attachment, sends message normally. 
   */

  if ($messageAttachment != null) {
    $filename = $_FILES['msgAttachment']['name'];
    $sanitisedFileName = $conn->real_escape_string(trim($filename));
    $filetemp = $_FILES['msgAttachment']['tmp_name'];
    // check if file type supported. 
    $fileExt = pathinfo($sanitisedFileName);

    /**
     *checks file was actually uploaded, as it is possible if the file is too big that the 
     * name is uploaded but img is not put into temp
     */

    if ($filetemp != null) {
      // checks file extensions, if not permitted does not send db query.
      if (strlen($messageSubject) > 65) {
        $messageError = "Subject too long, must be below 65 characters.";
      } else if ((!$sanitisedSubject == null) && (!$sanitisedText == null)) {
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
          || $fileExt["extension"] == "jpeg" ||  $fileExt["extension"] == "pdf"
          || $fileExt["extension"] == "pDf" || $fileExt["extension"] == "pdF"
          || $fileExt["extension"] == "PdF" || $fileExt["extension"] == "Pdf"
          || $fileExt["extension"] == "PDf" || $fileExt["extension"] == "pDF"
          || $fileExt["extension"] == "PDF" ||  $fileExt["extension"] == "txt"
          || $fileExt["extension"] == "Txt" || $fileExt["extension"] == "tXt"
          || $fileExt["extension"] == "txT" || $fileExt["extension"] == "tXT"
          || $fileExt["extension"] == "TxT" || $fileExt["extension"] == "TXt"
          || $fileExt["extension"] == "TXT" ||  $fileExt["extension"] == "docx"
          || $fileExt["extension"] == "dOcx" || $fileExt["extension"] == "doCx"
          || $fileExt["extension"] == "docX" || $fileExt["extension"] == "Docx"
          || $fileExt["extension"] == "DOcx" || $fileExt["extension"] == "DOcX"
          || $fileExt["extension"] == "DOCX" || $fileExt["extension"] == "DoCx"
          || $fileExt["extension"] == "DoCX" || $fileExt["extension"] == "DocX"
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
          while (file_exists('images/attachments/' . $actual_name . "." . $extension)) {

            $actual_name = (string) $original_name . $i;
            $filename = $actual_name . "." . $extension;
            $i++;
          }


          $sendMessage = "INSERT INTO webdev_inbox (recipient, sender, subject, message, attachment, coach, user, hide) 
    VALUES ('$sanitisedRecipient', '$sanitisedSender', '$sanitisedSubject', '$sanitisedText', '$filename', 
    $messageRecipient, $userid, 0)";
          $result =  $conn->query($sendMessage);
          move_uploaded_file($filetemp, "images/attachments/$filename");
          $messageSuccess = "Message successfully sent.";
        } else {
          $messageError = "File type not supported. Please try a different file.";
        }
      }
    } else {
      $messageError = "File error - check file has been selected and is <2mb. Please try a different image.";
    }
  } else {
    if (strlen($messageSubject) > 65) {
      $messageError = "Subject too long, must be below 65 characters.";
    } else if ((!$sanitisedSubject == null) && (!$sanitisedText == null)) { // checks both subject and message aren't empty
      $sendMessage = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, hide) 
            VALUES ('$sanitisedRecipient', '$sanitisedSender', '$sanitisedSubject', 
            '$sanitisedText', '$sanitisedRecipient', '$userid', 0)";
      $result =  $conn->query($sendMessage);
      if (!$result) {
        $messageError = "Cannot send message - please check input and try again.";
      } else {
        $messageSuccess = "Message successfully sent.";
      }
    } else { // if either field is empty
      $messageError = "Message not sent - subject or message empty.";
    }
  }
}

/**
 * If either a client or coach click on 'mark as read', updates db value of 'hide' 
 * column to be 1 so that it will no longer display on the 'unread messages' tab.
 */

if (isset($_POST['markAsRead'])) {
  $messageToMarkRead = $_POST['msgID'];
  $markAsRead = "UPDATE webdev_inbox
  SET hide = 1
  WHERE id = $messageToMarkRead";
  $executeMarkAsRead = $conn->query($markAsRead);
  if (!$executeMarkAsRead) {
    echo $conn->error;
  }
  $numOfMessages--; // decrease count shown in tab header by 1 since full refresh is needed to otherwise update value.
}

/**
 * If a client clicks on 'reply' on either their unread messages or when opening a message modal, 
 * then activates this modal which allows them to reply.
 * Auto fills with data grabbed from the original, such as name of the person 
 * they are replying to, and the subject of the initial message.
 */

if (isset($_POST['replyToMsg'])) {
  if (!isset($_POST['msgID'])) {
    $replyError = "Cannot reply - empty entry selected.";
  }
  $messageToReplyTo = $_POST['msgID'];

  $coachName = $_POST['coachName'];


  $getReplyToMsgInfo = $conn->prepare("SELECT sender, subject, coach FROM webdev_inbox 
  WHERE id = ? ");
  $getReplyToMsgInfo->bind_param("i", $messageToReplyTo);
  $getReplyToMsgInfo->execute();
  $getReplyToMsgInfo->store_result();
  $getReplyToMsgInfo->bind_result($recipient, $subject, $coachID);
  $getReplyToMsgInfo->fetch();



  echo "  <div class='modal is-active' id='replyMsg'>
        <div class='modal-background'></div>
        <div class='modal-card'>
          <header class='modal-card-head'>
            <p class='modal-card-title'>Reply to '$subject'</p>
            <button class='delete cancelUpdate' aria-label='close' ></button>
          
          </header>
         
          <section class='modal-card-body'>
         
            <form action='inbox.php' method='POST' enctype='multipart/form-data' id='clientReplyToMsg'>
      
              <div class='field'>
                <label class='label'>Recipient: </label>
                <div class='control'>
                  <input class='input' type='text' value='$coachName' name='replyRecipient' readonly>
                </div>
              </div>

      
              <input type='hidden' id='coachID'  value='$coachID' name='replyCoach' readonly>
              <input type='hidden' value='$messageToReplyTo' name='initialMsg' readonly>
      
              <div class='field'>
              <label class='label'>Subject: </label>
              <div class='control'>
              ";
?>

  <input class='input' id='subjectInput' type='text' value="<?php echo $subject ?>" name='replySubject' readonly>
  </div>
  <p class='subjectWarn help is-danger'></p>
  </div>
<?php
  echo "
            <input type='hidden' id='userID' value='$userid' name='replySender' readonly>
      
      
            <div class='field'>
            <label class='label'>Message: </label>
            <div class='control'>
              <textarea class='textarea' id='messageBody' placeholder = 'Put your response here' name='replyMessageText'></textarea>
            </div>
            <p class='messageWarn help is-danger'></p>
          </div>
      
      
          <div id='fileUploader' class='file has-name field' >
          <label class='label' >Attachment: </label>
          
          <label class='file-label' id='attachLabel'> 
          <!-- Allowed file types adapted from https://stackoverflow.com/questions/52047925/uploading-docx-files-using-php-->
            <input class='file-input' type='file' name='replyMsgAttachment' accept='image/png, image/gif, image/jpeg, 
            text/plain, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document'>
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
        <p>Accepted formats: png, gif, jpeg, pdf, .txt, .docx.</p>
        <p> File must be <2mb.</p>
      
      
            
            <footer class='modal-card-foot'>
            <input type='submit' class='button is-success clientReplyToMsgButton' id='replyMsgSubmit' value='Send Reply' name='replyMessage'>
      
          </footer>
      
      
            </form>
          </section>
   
  </div>
</div>
<!-- Changes the file name shown on the upload section to be whatever file the user selects
From https://bulma.io/documentation/form/file/ -->
<script>const fileInput = document.querySelector('#fileUploader input[type=file]');
fileInput.onchange = () => {
  if (fileInput.files.length > 0) {
    const fileName = document.querySelector('#fileUploader .file-name');
    fileName.textContent = fileInput.files[0].name;
  }
}
</script>";
}



/**
 * If a reply to a message has been sent, from clicking the reply button, this attempts to process it.
 * Sanitises all input from the user to protect against SQL injections.
 * Performs validation to ensure data types (e.g if sender ID is not numeric, which is hidden from the user, 
 * will print out an error)
 * Then determines if  an attachment has been set. Performs a check to see if a file has actually been uploaded, 
 * possibility of file name being sent but file not being uploaded (over limit), 
 * so only performs the 'attachment query' if file exists in temp folder.
 * If a file has been posted, then checks the file extensions. If not compatible, message is not sent and error 
 * is displayed.
 * If no attachment, attempt to write to DB without putting anything in the attachment row.
 */

if (isset($_POST['replyMessage'])) {

  $initialMessage = $_POST['initialMsg'];
  $replyCoach = $_POST['replyCoach'];
  $sanitisedReplyCoach = $conn->real_escape_string(trim($replyCoach));
  $replySubject = $_POST['replySubject'];
  $sanitisedReplySubject = $conn->real_escape_string(trim($replySubject));
  $replySender = $_POST['replySender'];
  $sanitisedReplySender = $conn->real_escape_string(trim($replySender));
  $replyMessage = $_POST['replyMessageText'];
  $sanitisedReplyMessage = $conn->real_escape_string(trim($replyMessage));
  $replyAttachment = $_FILES['replyMsgAttachment']['name'];
  $sanitisedReplyAttachment = $conn->real_escape_string(trim($replyAttachment));

  $filetemp = $_FILES['replyMsgAttachment']['tmp_name'];


  if (!ctype_digit($replySender)) { // checks sender's id s numeric
    $replyError = "Error - your reply has not been sent.";
  } else if (!ctype_digit($sanitisedReplyCoach)) { // checks coach's id s numeric
    $replyError = "Error - your reply has not been sent.";
  } else {
    if ($sanitisedReplyAttachment != null) { // if attachment is not null, has a process to follow
      if ($filetemp != null) { // check file has actually been uploaded


        $filename = $_FILES['replyMsgAttachment']['name'];
        $sanitisedFileName = $conn->real_escape_string(trim($filename));

        // check if file type supported. 
        $fileExt = pathinfo($sanitisedFileName);
        // ensures uploaded file's type is accepted
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
          || $fileExt["extension"] == "jpeg" ||  $fileExt["extension"] == "pdf"
          || $fileExt["extension"] == "pDf" || $fileExt["extension"] == "pdF"
          || $fileExt["extension"] == "PdF" || $fileExt["extension"] == "Pdf"
          || $fileExt["extension"] == "PDf" || $fileExt["extension"] == "pDF"
          || $fileExt["extension"] == "PDF" ||  $fileExt["extension"] == "txt"
          || $fileExt["extension"] == "Txt" || $fileExt["extension"] == "tXt"
          || $fileExt["extension"] == "txT" || $fileExt["extension"] == "tXT"
          || $fileExt["extension"] == "TxT" || $fileExt["extension"] == "TXt"
          || $fileExt["extension"] == "TXT" ||  $fileExt["extension"] == "docx"
          || $fileExt["extension"] == "dOcx" || $fileExt["extension"] == "doCx"
          || $fileExt["extension"] == "docX" || $fileExt["extension"] == "Docx"
          || $fileExt["extension"] == "DOcx" || $fileExt["extension"] == "DOcX"
          || $fileExt["extension"] == "DOCX" || $fileExt["extension"] == "DoCx"
          || $fileExt["extension"] == "DoCX" || $fileExt["extension"] == "DocX"
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
          while (file_exists('images/attachments/' . $actual_name . "." . $extension)) {
            $actual_name = (string) $original_name . $i;
            $filename = $actual_name . "." . $extension;
            $i++;
          }

          $sendReply = "INSERT INTO webdev_inbox (recipient, sender, subject, message, attachment, coach, user, hide) 
                VALUES ('$sanitisedReplyCoach', '$replySender', '$sanitisedReplySubject', '$sanitisedReplyMessage', 
                '$filename', $sanitisedReplyCoach, $userid, 0)";

          $hideInitial = "UPDATE webdev_inbox SET webdev_inbox.hide = 1 WHERE webdev_inbox.id = $initialMessage";
          // transaction adapted from online tutorial <https://www.youtube.com/watch?v=CNt9HPqDIVc>
          $conn->autocommit(false);

          $error = array();

          $a = $conn->query($sendReply); // attempts to send the reply
          if ($a == false) {
            $replyError = "Error sending reply.";
            array_push($error, $replyError);
          }

          $b =  $conn->query($hideInitial);
          if ($b == false) {
            $replyError = "Error hiding initial message.";
            array_push($error,  $replyError);
          }
          /**
           * If error array is not empty, one of the queries in the transaction 
           * has failed and it is rolled back. Else, commits the transaction.
           */
          if (!empty($error)) {
            $conn->rollback();
            echo $conn->error;
          } else {
            //commit if all ok
            $conn->commit();
            // move to temp file if all is ok. 
            move_uploaded_file($filetemp, "images/attachments/$filename");
            $replySuccess = "Reply successfully sent.";
          }
        } else {
          $replyError = "Reply not sent - file type not supported. Please try a different file.";
        }
      } else {
        $replyError = "Reply not sent - error with file. Check size and type.";
      }
    } else { // else if no attachment, different process to send message.
      if ((!$sanitisedReplySubject == null) && (!$sanitisedReplyMessage == null)) { // ensures fields are filled in
        $sendReplyMessage = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, hide) 
        VALUES ('$sanitisedReplyCoach', '$replySender', '$sanitisedReplySubject', '$sanitisedReplyMessage', $sanitisedReplyCoach, $userid, 0)";
        $hideInitial = "UPDATE webdev_inbox SET webdev_inbox.hide = 1 WHERE webdev_inbox.id = $initialMessage";

        // transaction adapted from online tutorial <https://www.youtube.com/watch?v=CNt9HPqDIVc>
        $conn->autocommit(false);
        $error = array();
        $a = $conn->query($sendReplyMessage);
        if ($a == false) {
          $replyError = "Error sending reply.";
          array_push($error, $replyError);
        }
        $b =  $conn->query($hideInitial);
        if ($b == false) {
          $replyError = "Error hiding initial message.";
          array_push($error, $replyError);
        }

        /**
         * If error array is not empty, one of the queries in the transaction 
         * has failed and it is rolled back. Else, commits the transaction.
         */
        if (!empty($error)) {
          $conn->rollback();
          echo $conn->error;
        } else {

          //commit if all ok
          $conn->commit();
          $replySuccess = "Reply successfully sent.";
        }
      } else { // if fields are empty
        $replyError = "Cannot send reply - please check input and try again.";
      }
    }
  }
}



/**
 * If a coach clicks on the 'compose message' button for a client, displays this modal.
 * Different UI depending on if the coach selected 'All Clients' or a specific client.
 * If a specific client, has the option of 3 additional clients to send the message to. 
 */
if (isset($_POST['msgUser'])) {
  if (!isset($_POST['messageUser'])) { // if no user data sent, error shown
    $logCreationFailed = "Cannot compose message - empty entry selected.";
  }
  $userToMessage = $_POST['messageUser'];
  if ($userToMessage != 001) {


    $getUser = $conn->prepare("SELECT name, user_id FROM webdev_user_details WHERE user_id = ? ");
    $getUser->bind_param("i", $userToMessage);
    $getUser->execute();
    $getUser->store_result();
    $getUser->bind_result($userName, $mainRecipientUserID);
    $getUser->fetch();


    echo "<div class='modal is-active' id='addLog'>
  <div class='modal-background'></div>
  <div class='modal-card'>
    <header class='modal-card-head'>
      <p class='modal-card-title'>Compose New Message</p>
      <button class='delete cancelUpdate' aria-label='close' ></button>
    
    </header>
   
    <section class='modal-card-body'>
   
      <form action='inbox.php' method='POST' enctype='multipart/form-data' id='coachMessageUser'>
  <div class='field'>
  <label class='label'>Recipient: </label>
  <div class='control'>
    <input class='input' type='text' value='$userName' name='userName' readonly>
  </div>
</div>
<div class='field'>
<label class='label'>Additional Recipient(s): </label>
<div class='control is-grouped select'>
<select name='additionalMessageUserOne'>
<option value='none'>None</option>";

    // select all users registered to the coach logged in 
    $selectUsersForCoach = "SELECT webdev_users.id, webdev_user_details.name FROM webdev_coach 
INNER JOIN webdev_user_details
ON webdev_coach.id = webdev_user_details.coach
INNER JOIN webdev_users
ON webdev_user_details.user_id = webdev_users.id
WHERE webdev_coach.id = $loggedInCoachId
ORDER BY webdev_user_details.name ASC";
    $executeSelectUsersForCoachAdditional = $conn->query($selectUsersForCoach);

    $num = $executeSelectUsersForCoach->num_rows;

    while ($row = $executeSelectUsersForCoachAdditional->fetch_assoc()) {
      $firstAdditionalUserID = $row['id'];
      $firstAdditionalUserName = $row['name'];

      // adds a new option to the drop down list for all approved users to the logged in coach
      echo "<option value='$firstAdditionalUserID'> $firstAdditionalUserName</option>";
    }
    echo "
</select>
</div>
<div class='is-grouped control select'>
<select name='additionalMessageUserTwo'>
<option value='none'>None</option>";

    // select all users registered to the coach logged in 
    $selectUsersForCoach = "SELECT webdev_users.id, webdev_user_details.name FROM webdev_coach 
INNER JOIN webdev_user_details
ON webdev_coach.id = webdev_user_details.coach
INNER JOIN webdev_users
ON webdev_user_details.user_id = webdev_users.id
WHERE webdev_coach.id = $loggedInCoachId
ORDER BY webdev_user_details.name ASC";
    $executeSelectUsersForCoachAdditional = $conn->query($selectUsersForCoach);

    $num = $executeSelectUsersForCoach->num_rows;

    while ($row = $executeSelectUsersForCoachAdditional->fetch_assoc()) {
      $secondAdditionalUserID = $row['id'];
      $secondAdditionalUserName = $row['name'];

      // adds a new option to the drop down list for all approved users to the logged in coach
      echo "<option value='$secondAdditionalUserID'> $secondAdditionalUserName</option>";
    }
    echo "
</select>

</div>
<div class='is-grouped control select'>
<select name='additionalMessageUserThree'>
<option value='none'>None</option>";

    // select all users registered to the coach logged in 
    $selectUsersForCoach = "SELECT webdev_users.id, webdev_user_details.name FROM webdev_coach 
INNER JOIN webdev_user_details
ON webdev_coach.id = webdev_user_details.coach
INNER JOIN webdev_users
ON webdev_user_details.user_id = webdev_users.id
WHERE webdev_coach.id = $loggedInCoachId
ORDER BY webdev_user_details.name ASC";
    $executeSelectUsersForCoachAdditional = $conn->query($selectUsersForCoach);

    $num = $executeSelectUsersForCoach->num_rows;

    while ($row = $executeSelectUsersForCoachAdditional->fetch_assoc()) {
      $thirdAdditionalUserID = $row['id'];
      $thirdAdditionalUserName = $row['name'];

      // adds a new option to the drop down list for all approved users to the logged in coach
      echo "<option value='$thirdAdditionalUserID'> $thirdAdditionalUserName</option>";
    }
    echo "
</select>
</div>
</div>
<input type='hidden' id='userID' value='$mainRecipientUserID' name='messageRecipient' readonly>


        <input type='hidden' id='coachID'  value='$loggedInCoachId' name='messageSender' readonly>

        <div class='field'>
        <label class='label'>Subject: </label>
        <div class='control'>
          <input class='input' id='subjectInput' type='text' placeholder='Question about nutrition' name='messageSubject'>
        </div>
        <p class='subjectWarn help is-danger'></p>
      </div>
      


      <div class='field'>
      <label class='label'>Message: </label>
      <div class='control'>
        <textarea class='textarea' id='messageBody' placeholder = 'Put your message here' name='messageText'></textarea>
      </div>
    <p class='messageWarn help is-danger'></p>
    </div>


    <div id='fileUploader' class='file has-name field' >
    <label class='label' >Attachment: </label>
    
    <label class='file-label' id='attachLabel'> 
    <!-- Allowed file types adapted from https://stackoverflow.com/questions/52047925/uploading-docx-files-using-php-->
      <input class='file-input' type='file' name='msgAttachment' accept='image/png, image/gif, image/jpeg, 
      text/plain, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document>
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
  <p>Accepted formats: png, gif, jpeg, pdf, .txt, .docx.</p>
  <p> File must be <2mb.</p>


      
      <footer class='modal-card-foot'>
      <input type='submit' class='button is-success coachMessageUserButton' id='coachMsgSubmit' value='Submit Message' name='userMsgSubmit'>

    </footer>


      </form>
    </section>
   
  </div>
</div>
<!-- Changes the file name shown on the upload section to be whatever file the user selects
From https://bulma.io/documentation/form/file/ -->
<script>const fileInput = document.querySelector('#fileUploader input[type=file]');
fileInput.onchange = () => {
  if (fileInput.files.length > 0) {
    const fileName = document.querySelector('#fileUploader .file-name');
    fileName.textContent = fileInput.files[0].name;
  }
}
</script>";
    /**
     * Else if sending to all clients, no option to select who it will be sent to. 
     */
  } else {
    echo "<div class='modal is-active' id='addLog'>
  <div class='modal-background'></div>
  <div class='modal-card'>
    <header class='modal-card-head'>
      <p class='modal-card-title'>Compose New Message</p>
      <button class='delete cancelUpdate' aria-label='close' ></button>
    
    </header>
   
    <section class='modal-card-body'>
   
      <form action='inbox.php' method='POST' enctype='multipart/form-data' id='coachMsgAllUser'>
  <div class='field'>
  <label class='label'>Recipient: </label>
  <div class='control'>
    <input class='input' type='text' value='All Clients' name='userName' readonly>
  </div>
</div>
<input type='hidden' value='001' name='messageRecipient' readonly>



        <input type='hidden' id='coachID'  value='$loggedInCoachId' name='messageSender' readonly>

        <div class='field'>
        <label class='label'>Subject: </label>
        <div class='control'>
          <input class='input' id='subjectInput' type='text' placeholder='Question about nutrition' name='messageSubject'>
        </div>
        <p class='subjectWarn help is-danger'></p>
      </div>
      


      <div class='field'>
      <label class='label'>Message: </label>
      <div class='control'>
        <textarea class='textarea' id='messageBody' placeholder = 'Put your message here' name='messageText'></textarea>
      </div>
      <p class='messageWarn help is-danger'></p>
    </div>


    <div id='fileUploader' class='file has-name field' >
    <label class='label' >Attachment: </label>
    
    <label class='file-label' id='attachLabel'> 
    <!-- Allowed file types adapted from https://stackoverflow.com/questions/52047925/uploading-docx-files-using-php-->
      <input class='file-input' type='file' name='msgAttachment' accept='image/png, image/gif, image/jpeg, 
      text/plain, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document'>
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
  <p>Accepted formats: png, gif, jpeg, pdf, .txt, .docx.</p>
  <p> File must be <2mb.</p>


      
      <footer class='modal-card-foot'>
      <input type='submit' class='button is-success coachMsgAllUserButton' id='coachMsgSubmit' value='Submit Message' name='userMsgSubmit'>

    </footer>


      </form>
    </section>
   
  </div>
</div>
<!-- Changes the file name shown on the upload section to be whatever file the user selects
From https://bulma.io/documentation/form/file/ -->
<script>const fileInput = document.querySelector('#fileUploader input[type=file]');
fileInput.onchange = () => {
  if (fileInput.files.length > 0) {
    const fileName = document.querySelector('#fileUploader .file-name');
    fileName.textContent = fileInput.files[0].name;
  }
}
</script>";
  }
}

/**
 * If the coach submits their message:
 *  First checks if an attachment has been set, if so will write to DB after sanitising information
 *  Performs a check to see if a file has actually been uploaded, possibility of file name being sent but file not being 
 * uploaded (over limit), so only performs this if file exists in temp folder.
 * If a file has been posted, then checks the file extensions. If not compatible, message is not sent and error is displayed.0
 *  If no attachment, write to DB normally.
 *  If a message to more than one user, checks that they have been set and then checks they are not the same as the original user.
 * If so, only sends message to any duplicate entries once. 
 */

if (isset($_POST['userMsgSubmit'])) {
  $messageRecipient = $_POST['messageRecipient'];
  $sanitisedRecipient = $conn->real_escape_string(trim($messageRecipient));
  $messageSubject = $_POST['messageSubject'];
  $sanitisedSubject = $conn->real_escape_string(trim($messageSubject));
  $messageSender = $_POST['messageSender'];
  $sanitisedSender = $conn->real_escape_string(trim($messageSender));
  $messageText = $_POST['messageText'];
  $sanitisedText = $conn->real_escape_string(trim($messageText));
  $messageAttachment = $_FILES['msgAttachment']['name'];
  $filetemp = $_FILES['msgAttachment']['tmp_name'];


  // Message recipient 001 = all users, if set will send a message to all users otherwise only to the ones selected.
  if ($messageRecipient != 001) {
    /**
     * Checks if an attachment has been sent, if so checks file type.
     * If no attachment, sends message normally. 
     */

    if ($messageAttachment != null) {
      if ($filetemp != null) { // checks file actually exists


        $filename = $_FILES['msgAttachment']['name'];
        $sanitisedFileName = $conn->real_escape_string(trim($filename));
        // check if file type supported. 
        $fileExt = pathinfo($sanitisedFileName);
        /*
        *checks file was actually uploaded, as it is possible if the file is too big that the 
        * name is uploaded but img is not put into temp
        */
        if ($filetemp != null) {
          if (strlen($messageSubject) > 65) {
            $messageError = "Subject too long, must be below 65 characters.";
          } else if ((!$sanitisedSubject == null) && (!$sanitisedText == null)) { // if fields not null
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
              || $fileExt["extension"] == "jpeg" ||  $fileExt["extension"] == "pdf"
              || $fileExt["extension"] == "pDf" || $fileExt["extension"] == "pdF"
              || $fileExt["extension"] == "PdF" || $fileExt["extension"] == "Pdf"
              || $fileExt["extension"] == "PDf" || $fileExt["extension"] == "pDF"
              || $fileExt["extension"] == "PDF" ||  $fileExt["extension"] == "txt"
              || $fileExt["extension"] == "Txt" || $fileExt["extension"] == "tXt"
              || $fileExt["extension"] == "txT" || $fileExt["extension"] == "tXT"
              || $fileExt["extension"] == "TxT" || $fileExt["extension"] == "TXt"
              || $fileExt["extension"] == "TXT" ||  $fileExt["extension"] == "docx"
              || $fileExt["extension"] == "dOcx" || $fileExt["extension"] == "doCx"
              || $fileExt["extension"] == "docX" || $fileExt["extension"] == "Docx"
              || $fileExt["extension"] == "DOcx" || $fileExt["extension"] == "DOcX"
              || $fileExt["extension"] == "DOCX" || $fileExt["extension"] == "DoCx"
              || $fileExt["extension"] == "DoCX" || $fileExt["extension"] == "DocX"
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
              while (file_exists('images/attachments/' . $actual_name . "." . $extension)) {
                $actual_name = (string) $original_name . $i;
                $filename = $actual_name . "." . $extension;
                $i++;
              }
              $sendMessage = "INSERT INTO webdev_inbox (recipient, sender, subject, message, attachment, coach, user, hide) 
    VALUES ('$sanitisedRecipient', '$sanitisedSender', '$sanitisedSubject', '$sanitisedText', '$filename', 
    $loggedInCoachId, $messageRecipient, 0)";
              $result =  $conn->query($sendMessage);
              move_uploaded_file($filetemp, "images/attachments/$filename");
              $messageSuccess = "Message successfully sent.";

              if (
                isset($_POST['additionalMessageUserOne']) || isset($_POST['additionalMessageUserTwo']) ||
                isset($_POST['additionalMessageUserThree'])
              ) {
                $additionalMessageUserOne = $_POST['additionalMessageUserOne'];
                $sanitisedMessageUserOne = $conn->real_escape_string(trim($additionalMessageUserOne));
                $additionalMessageUserTwo = $_POST['additionalMessageUserTwo'];
                $sanitisedMessageUserTwo = $conn->real_escape_string(trim($additionalMessageUserTwo));
                $additionalMessageUserThree = $_POST['additionalMessageUserThree'];
                $sanitisedMessageUserThree = $conn->real_escape_string(trim($additionalMessageUserThree));

                /**
                 * Checks if the first additional recipient has been set, compares again the original recipient 
                 * If the same as original recipient ignored. 
                 * If equals 'none', the default on additional recipients, does not send
                 * */
                if ((isset($sanitisedMessageUserOne)) && ($additionalMessageUserOne != $messageRecipient)
                  && ($additionalMessageUserOne != $messageRecipient) && ($additionalMessageUserOne != 'none')
                ) {

                  $sendSecondMessage = "INSERT INTO webdev_inbox (recipient, sender, subject, message, attachment, coach, user, hide) 
              VALUES ('$sanitisedMessageUserOne', '$sanitisedSender', '$sanitisedSubject', '$sanitisedText', '$filename', 
              $loggedInCoachId, $sanitisedMessageUserOne, 0)";
                  $secondResult =  $conn->query($sendSecondMessage);
                  if (!$secondResult) {
                    echo $conn->error;
                  }
                }

                /**
                 * Checks if the second additional recipient has been set, compares again the original recipient and first additional option
                 * If the same as original recipient or first additional recipients, it is not sent. . 
                 * If equals 'none', the default on additional recipients, does not send.
                 * */
                if ((isset($sanitisedMessageUserTwo)) && ($additionalMessageUserTwo != $messageRecipient)
                  && ($additionalMessageUserTwo != $sanitisedMessageUserOne) && ($additionalMessageUserTwo != 'none')
                ) {
                  $sendThirdMessage = "INSERT INTO webdev_inbox (recipient, sender, subject, message, attachment, coach, user, hide) 
              VALUES ('$sanitisedMessageUserTwo', '$sanitisedSender', '$sanitisedSubject', '$sanitisedText', '$filename', 
              $loggedInCoachId, $sanitisedMessageUserTwo, 0)";
                  $thirdResult =  $conn->query($sendThirdMessage);
                  if (!$thirdResult) {
                    echo $conn->error;
                  }
                }
                /**
                 * Checks if the third additional recipient has been set, compares again the original recipient, first and second additional option
                 * If the same as original recipient or first or second additional recipients, it is not sent. 
                 * If equals 'none', the default on additional recipients, does not send
                 * */
                if ((isset($sanitisedMessageUserThree)) && ($sanitisedMessageUserThree != $messageRecipient)
                  && ($additionalMessageUserThree != $sanitisedMessageUserOne) && ($additionalMessageUserThree != $sanitisedMessageUserTwo)
                  && ($additionalMessageUserThree != 'none')
                ) {
                  $sendFourthMessage = "INSERT INTO webdev_inbox (recipient, sender, subject, message, attachment, coach, user, hide) 
                VALUES ('$sanitisedMessageUserThree', '$sanitisedSender', '$sanitisedSubject', '$sanitisedText', '$filename', 
                $loggedInCoachId, $sanitisedMessageUserThree, 0)";
                  $fourthResult =  $conn->query($sendFourthMessage);
                  if (!$fourthResult) {
                    echo $conn->error;
                  }
                }
              }
            } else { // if file type not supported
              $messageError = "File type not supported. Please try a different file.";
            }
          } else { // if file not actually uploaded
            $messageError = "File error - check file has been selected and is <2mb. Please try a different image.";
          }
        } else {
          $messageError = "File error - check file has been selected and is <2mb. Please try a different image.";
        }
      }
    } else { // if no attachment
      if (strlen($messageSubject) > 65) {
        $messageError = "Subject too long, must be below 65 characters.";
      } else if ((!$sanitisedSubject == null) && (!$sanitisedText == null)) { // if fields not null
        $sendMessage = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, hide) 
            VALUES ('$sanitisedRecipient', '$sanitisedSender', '$sanitisedSubject', 
            '$sanitisedText', '$loggedInCoachId', '$messageRecipient', 0)";
        $result =  $conn->query($sendMessage);

        if (
          isset($_POST['additionalMessageUserOne']) || isset($_POST['additionalMessageUserTwo']) ||
          isset($_POST['additionalMessageUserThree'])
        ) {
          $additionalMessageUserOne = $_POST['additionalMessageUserOne'];
          $sanitisedMessageUserOne = $conn->real_escape_string(trim($additionalMessageUserOne));
          $additionalMessageUserTwo = $_POST['additionalMessageUserTwo'];
          $sanitisedMessageUserTwo = $conn->real_escape_string(trim($additionalMessageUserTwo));
          $additionalMessageUserThree = $_POST['additionalMessageUserThree'];
          $sanitisedMessageUserThree = $conn->real_escape_string(trim($additionalMessageUserThree));

          /**
           * Checks if the first additional recipient has been set, compares again the original recipient 
           * If the same as original recipient ignored. 
           * If equals 'none', the default on additional recipients, does not send
           * */
          if (
            isset($sanitisedMessageUserOne) && ($additionalMessageUserOne != $messageRecipient)
            && ($additionalMessageUserOne != 'none')
          ) {
            $sendSecondMessage = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, hide) 
            VALUES ('$sanitisedMessageUserOne', '$sanitisedSender', '$sanitisedSubject', '$sanitisedText',  
            $loggedInCoachId, $sanitisedMessageUserOne, 0)";
            $secondResult =  $conn->query($sendSecondMessage);
            if (!$secondResult) {
              echo $conn->error;
            }
          }
          /**
           * Checks if the second additional recipient has been set, compares again the original recipient and first additional option
           * If the same as original recipient or first additional recipients, it is not sent. . 
           * If equals 'none', the default on additional recipients, does not send.
           * */
          if (
            isset($sanitisedMessageUserTwo) && ($additionalMessageUserTwo != $messageRecipient)
            && ($additionalMessageUserTwo != $sanitisedMessageUserOne) && ($additionalMessageUserTwo != 'none')
          ) {
            $sendThirdMessage = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, hide) 
            VALUES ('$sanitisedMessageUserTwo', '$sanitisedSender', '$sanitisedSubject', '$sanitisedText', 
            $loggedInCoachId, $sanitisedMessageUserTwo, 0)";
            $thirdResult =  $conn->query($sendThirdMessage);
            if (!$thirdResult) {
              echo $conn->error;
            }
          }
          /**
           * Checks if the third additional recipient has been set, compares again the original recipient, first and second additional option
           * If the same as original recipient or first or second additional recipients, it is not sent. 
           * If equals 'none', the default on additional recipients, does not send
           * */
          if (
            isset($sanitisedMessageUserThree) && ($additionalMessageUserThree != $messageRecipient)
            && ($additionalMessageUserThree != $sanitisedMessageUserOne) && ($additionalMessageUserThree != $sanitisedMessageUserTwo)
            && ($additionalMessageUserThree != 'none')
          ) {
            $sendFourthMessage = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, hide) 
              VALUES ('$sanitisedMessageUserThree', '$sanitisedSender', '$sanitisedSubject', '$sanitisedText', 
              $loggedInCoachId, $sanitisedMessageUserThree, 0)";
            $fourthResult =  $conn->query($sendFourthMessage);
            if (!$fourthResult) {
              echo $conn->error;
            }
          }
        }
        if (!$result) {
          $messageError = "Cannot send message - please check input and try again.";
        } else {
          $messageSuccess = "Message successfully sent.";
        }
      } else {
        $messageError = "Message not sent - subject or message empty.";
      }
    }
    /**
     * If 001 is posted from 'All Clients', grabs all clients for this specific coach
     * Then writes an entry to the database for each user. 
     */
  } else if ($messageRecipient == 001) {
    $getAllClientsForCoach = "SELECT * FROM webdev_user_details WHERE coach = $loggedInCoachId";
    $executeGetAllClientsForCoach = $conn->query($getAllClientsForCoach);
    if (!$executeGetAllClientsForCoach) {
      echo $conn->error;
    }
    while ($row = $executeGetAllClientsForCoach->fetch_assoc()) {
      $allUsers = $row['user_id'];

      /**
       * Checks if an attachment has been sent, if so checks file type.
       * If no attachment, sends message normally. 
       */

      if ($messageAttachment != null) {
        $filename = $_FILES['msgAttachment']['name'];
        $sanitisedFileName = $conn->real_escape_string(trim($filename));
        $filetemp = $_FILES['msgAttachment']['tmp_name'];
        // check if file type supported. 
        $fileExt = pathinfo($sanitisedFileName);
        /*
        *checks file was actually uploaded, as it is possible if the file is too big that the 
        * name is uploaded but img is not put into temp
        */
        if (strlen($messageSubject) > 65) {
          $messageError = "Subject too long, must be below 65 characters.";
        } else if ((!$sanitisedSubject == null) && (!$sanitisedText == null)) { // if fields not null
          if ($filetemp != null) {
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
              || $fileExt["extension"] == "jpeg" ||  $fileExt["extension"] == "pdf"
              || $fileExt["extension"] == "pDf" || $fileExt["extension"] == "pdF"
              || $fileExt["extension"] == "PdF" || $fileExt["extension"] == "Pdf"
              || $fileExt["extension"] == "PDf" || $fileExt["extension"] == "pDF"
              || $fileExt["extension"] == "PDF" ||  $fileExt["extension"] == "txt"
              || $fileExt["extension"] == "Txt" || $fileExt["extension"] == "tXt"
              || $fileExt["extension"] == "txT" || $fileExt["extension"] == "tXT"
              || $fileExt["extension"] == "TxT" || $fileExt["extension"] == "TXt"
              || $fileExt["extension"] == "TXT" ||  $fileExt["extension"] == "docx"
              || $fileExt["extension"] == "dOcx" || $fileExt["extension"] == "doCx"
              || $fileExt["extension"] == "docX" || $fileExt["extension"] == "Docx"
              || $fileExt["extension"] == "DOcx" || $fileExt["extension"] == "DOcX"
              || $fileExt["extension"] == "DOCX" || $fileExt["extension"] == "DoCx"
              || $fileExt["extension"] == "DoCX" || $fileExt["extension"] == "DocX"
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
              while (file_exists('images/attachments/' . $actual_name . "." . $extension)) {
                $actual_name = (string) $original_name . $i;
                $filename = $actual_name . "." . $extension;
                $i++;
              }
              $sendMessage = "INSERT INTO webdev_inbox (recipient, sender, subject, message, attachment, coach, user, hide) 
            VALUES ('$allUsers', '$sanitisedSender', '$sanitisedSubject', '$sanitisedText', '$filename', 
            $loggedInCoachId, $allUsers, 0)";
              $result =  $conn->query($sendMessage);

              move_uploaded_file($filetemp, "images/attachments/$filename");
              $messageSuccess = "Message successfully sent.";
            } else {
              $messageError = "File type not supported. Please try a different file.";
            }
          }
        } else {
          $messageError = "File error - check file has been selected and is <2mb. Please try a different image.";
        }
      } else { // if no attachment
        if (strlen($messageSubject) > 65) {
          $messageError = "Subject too long, must be below 65 characters.";
        } else if ((!$sanitisedSubject == null) && (!$sanitisedText == null)) {

          $sendMessage = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, hide) 
          VALUES ($allUsers, $sanitisedSender, '$sanitisedSubject', '$sanitisedText', 
          $loggedInCoachId, '$allUsers', 0)";

          $result =  $conn->query($sendMessage);


          $messageSuccess = "Message successfully sent.";
        } else {
          $messageError = "Message not sent - subject or message empty.";
        }
      } // end of else
    } // end of while
  }
}

/**
 * If a user tries to message a group, displays this modal.
 */
if (isset($_POST['messageGroup'])) {

  $groupID = $_POST['groupsToMessage'];

  $getGroupName = $conn->prepare("SELECT group_name FROM webdev_groups
  WHERE id = ?");
  $getGroupName->bind_param("i", $groupID);
  $getGroupName->execute();
  $getGroupName->store_result();
  $getGroupName->bind_result($groupName);
  $getGroupName->fetch();

  echo "<div class='modal is-active' id='addLog'>
  <div class='modal-background'></div>
  <div class='modal-card'>
    <header class='modal-card-head'>
      <p class='modal-card-title'>Composing message to $groupName</p>
      <button class='delete cancelUpdate' aria-label='close' ></button>
    
    </header>
   
    <section class='modal-card-body'>
   
      <form action='inbox.php' method='POST' enctype='multipart/form-data' id='coachMsgAllUser'>
  <div class='field'>
  <label class='label'>Recipient: </label>
  <div class='control'>
    <input class='input' type='text' value='$groupName' name='groupName' readonly>
  </div>
</div>
<input type='hidden' value='$groupID' name='messageRecipient' readonly>




        <div class='field'>
        <label class='label'>Subject: </label>
        <div class='control'>
          <input class='input' id='subjectInput' type='text' placeholder='Question about nutrition' name='messageSubject'>
        </div>
        <p class='subjectWarn help is-danger'></p>
      </div>
      


      <div class='field'>
      <label class='label'>Message: </label>
      <div class='control'>
        <textarea class='textarea' id='messageBody' placeholder = 'Put your message here' name='messageText'></textarea>
      </div>
      <p class='messageWarn help is-danger'></p>
    </div>


    <div id='fileUploader' class='file has-name field' >
    <label class='label' >Attachment: </label>
    
    <label class='file-label' id='attachLabel'> 
    <!-- Allowed file types adapted from https://stackoverflow.com/questions/52047925/uploading-docx-files-using-php-->
      <input class='file-input' type='file' name='msgAttachment' accept='image/png, image/gif, image/jpeg, 
      text/plain, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document'>
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
  <p>Accepted formats: png, gif, jpeg, pdf, .txt, .docx.</p>
  <p> File must be <2mb.</p>


      
      <footer class='modal-card-foot'>
      <input type='submit' class='button is-success coachMsgAllUserButton' id='coachMsgSubmit' value='Submit Message' name='groupMessageSubmit'>

    </footer>


      </form>
    </section>
   
  </div>
</div>
<!-- Changes the file name shown on the upload section to be whatever file the user selects
From https://bulma.io/documentation/form/file/ -->
<script>const fileInput = document.querySelector('#fileUploader input[type=file]');
fileInput.onchange = () => {
  if (fileInput.files.length > 0) {
    const fileName = document.querySelector('#fileUploader .file-name');
    fileName.textContent = fileInput.files[0].name;
  }
}
</script>";
}

/**
 * If a user submits the group message form, attempts to process it.
 */
if (isset($_POST['groupMessageSubmit'])) {
  $messageText = $_POST['messageText'];
  $sanitisedMessageText = $conn->real_escape_string(trim($messageText));
  $filename = $_FILES['msgAttachment']['name'];
  $sanitisedMessageAttachment = $conn->real_escape_string(trim($filename));
  $messageSubject = $_POST['messageSubject'];
  $sanitisedMessageSubject = $conn->real_escape_string(trim($messageSubject));
  $groupID = $_POST['messageRecipient'];

  $getGroupInfo = $conn->prepare("SELECT group_name, coach, member_one, member_two, member_three, member_four
  FROM webdev_groups
  WHERE id = ?");
  $getGroupInfo->bind_param("i", $groupID);
  $getGroupInfo->execute();
  $getGroupInfo->store_result();
  $getGroupInfo->bind_result($groupName, $coach, $firstMember, $secondMember, $thirdMember, $fourthMember);
  $getGroupInfo->fetch();

  if ($sanitisedMessageAttachment != null) {
    $filename = $_FILES['msgAttachment']['name'];
    $sanitisedFileName = $conn->real_escape_string(trim($filename));
    $filetemp = $_FILES['msgAttachment']['tmp_name'];
    // check if file type supported. 
    $fileExt = pathinfo($sanitisedFileName);
    if ($filetemp != null) {

      if (strlen($messageSubject) > 65) {
        $messageError = "Subject too long, must be below 65 characters.";
      } else if ((!$sanitisedMessageSubject == null) && (!$sanitisedMessageText == null)) { // if fields not null
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
          || $fileExt["extension"] == "jpeg" ||  $fileExt["extension"] == "pdf"
          || $fileExt["extension"] == "pDf" || $fileExt["extension"] == "pdF"
          || $fileExt["extension"] == "PdF" || $fileExt["extension"] == "Pdf"
          || $fileExt["extension"] == "PDf" || $fileExt["extension"] == "pDF"
          || $fileExt["extension"] == "PDF" ||  $fileExt["extension"] == "txt"
          || $fileExt["extension"] == "Txt" || $fileExt["extension"] == "tXt"
          || $fileExt["extension"] == "txT" || $fileExt["extension"] == "tXT"
          || $fileExt["extension"] == "TxT" || $fileExt["extension"] == "TXt"
          || $fileExt["extension"] == "TXT" ||  $fileExt["extension"] == "docx"
          || $fileExt["extension"] == "dOcx" || $fileExt["extension"] == "doCx"
          || $fileExt["extension"] == "docX" || $fileExt["extension"] == "Docx"
          || $fileExt["extension"] == "DOcx" || $fileExt["extension"] == "DOcX"
          || $fileExt["extension"] == "DOCX" || $fileExt["extension"] == "DoCx"
          || $fileExt["extension"] == "DoCX" || $fileExt["extension"] == "DocX"
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
          while (file_exists('images/attachments/' . $actual_name . "." . $extension)) {
            $actual_name = (string) $original_name . $i;
            $filename = $actual_name . "." . $extension;
            $i++;
          }

          if (isset($_SESSION['gymafi_userid'])) {
            $sendGroupReplyToCoach = "INSERT INTO webdev_inbox (recipient, sender, subject, message, attachment, coach, user, group_id, hide)
VALUES ($coach, $userid, '$sanitisedMessageSubject', '$sanitisedMessageText', '$filename', $coach, $coach, $groupID, 0)";

            $sendGroupReplyToFirst = "INSERT INTO webdev_inbox (recipient, sender, subject, message, attachment, coach, user, group_id, hide)
VALUES ($firstMember, $userid, '$sanitisedMessageSubject', '$sanitisedMessageText', '$filename', $coach, $firstMember, $groupID, 0)";

            $sendGroupReplyToSecond = "INSERT INTO webdev_inbox (recipient, sender, subject, message, attachment, coach, user, group_id, hide)
VALUES ($secondMember, $userid, '$sanitisedMessageSubject', '$sanitisedMessageText', '$filename', $coach, $secondMember, $groupID, 0)";

            $sendGroupReplyToThird = "INSERT INTO webdev_inbox (recipient, sender, subject, message, attachment, coach, user, group_id, hide)
VALUES ($thirdMember, $userid, '$sanitisedMessageSubject', '$sanitisedMessageText', '$filename', $coach, $thirdMember, $groupID, 0)";

            $sendGroupReplyToFourth = "INSERT INTO webdev_inbox (recipient, sender, subject, message, attachment, coach, user, group_id, hide)
VALUES ($fourthMember, $userid, '$sanitisedMessageSubject', '$sanitisedMessageText', '$filename', $coach, $fourthMember, $groupID, 0)";
          } else if (isset($_SESSION['gymafi_coachid'])) {
            $sendGroupReplyToFirst = "INSERT INTO webdev_inbox (recipient, sender, subject, message, attachment, coach, user, group_id, hide)
        VALUES ($firstMember, $loggedInCoachId, '$sanitisedMessageSubject', '$sanitisedMessageText', '$filename', $loggedInCoachId, $firstMember, $groupID, 0)";

            $sendGroupReplyToSecond = "INSERT INTO webdev_inbox (recipient, sender, subject, message, attachment, coach, user, group_id, hide)
        VALUES ($secondMember, $loggedInCoachId, '$sanitisedMessageSubject', '$sanitisedMessageText', '$filename', $loggedInCoachId, $secondMember, $groupID, 0)";

            $sendGroupReplyToThird = "INSERT INTO webdev_inbox (recipient, sender, subject, message, attachment, coach, user, group_id, hide)
        VALUES ($thirdMember, $loggedInCoachId, '$sanitisedMessageSubject', '$sanitisedMessageText', '$filename', $loggedInCoachId, $thirdMember, $groupID, 0)";

            $sendGroupReplyToFourth = "INSERT INTO webdev_inbox (recipient, sender, subject, message, attachment, coach, user, group_id, hide)
        VALUES ($fourthMember, $loggedInCoachId, '$sanitisedMessageSubject', '$sanitisedMessageText', '$filename', $loggedInCoachId, $fourthMember, $groupID, 0)";
          }

          // transaction adapted from online tutorial <https://www.youtube.com/watch?v=CNt9HPqDIVc>
          $conn->autocommit(false);

          $error = array();
          // if not a coach, send message to coach.
          if (!isset($_SESSION['gymafi_coachid'])) {

            $a = $conn->query($sendGroupReplyToCoach);
            if ($a == false) {
              $groupReplyError = "Could not send group message to coach.";
              array_push($error, $groupReplyError);
            }

            if ($firstMember != $userid) {
              $b = $conn->query($sendGroupReplyToFirst);
              if ($b == false) {
                $groupReplyError = "Could not send group message to first group member.";
                array_push($error, $groupReplyError);
              }
            }

            if ($secondMember != $userid) {
              $c = $conn->query($sendGroupReplyToSecond);
              if ($c == false) {
                $groupReplyError = "Could not send group message to second group member.";
                array_push($error, $groupReplyError);
              }
            }

            if ($thirdMember != $userid) {
              $d = $conn->query($sendGroupReplyToThird);
              if ($d == false) {
                $groupReplyError = "Could not send group message to third group member.";
                array_push($error, $groupReplyError);
              }
            }

            if ($fourthMember != $userid) {
              $e = $conn->query($sendGroupReplyToFourth);
              if ($e == false) {
                $groupReplyError = "Could not send group message to fourth group member.";
                array_push($error, $groupReplyError);
              }
            }
          } else { // if coach, no need to send to coach and no need to check if their id == a member of groups

            $b = $conn->query($sendGroupReplyToFirst);
            if ($b == false) {
              $groupReplyError = "Could not send group message to first group member.";
              array_push($error, $groupReplyError);
            }

            $c = $conn->query($sendGroupReplyToSecond);
            if ($c == false) {
              $groupReplyError = "Could not send group message to second group member.";
              array_push($error, $groupReplyError);
            }

            $d = $conn->query($sendGroupReplyToThird);
            if ($d == false) {
              $groupReplyError = "Could not send group message to third group member.";
              array_push($error, $groupReplyError);
            }

            $e = $conn->query($sendGroupReplyToFourth);
            if ($e == false) {
              $groupReplyError = "Could not send group message to fourth group member.";
              array_push($error, $groupReplyError);
            }
          }

          /**
           * If error array is not empty, one of the queries in the transaction 
           * has failed and it is rolled back. Else, commits the transaction.
           */
          if (!empty($error)) {
            $conn->rollback();
            $groupReplyError = "Cannot send message - please check input and try again.";
          } else {
            //commit if all ok
            $conn->commit();
            move_uploaded_file($filetemp, "images/attachments/$filename");
            $groupReplySuccess = "Group message successfully sent.";
          }
        } else {
          $replyError = "File type not supported. Please try a different file.";
        }
      }
    }
  } else { //if no attachment
    if (strlen($messageSubject) > 65) {
      $messageError = "Subject too long, must be below 65 characters.";
    } else  if ((!$sanitisedMessageText == null) && (!$sanitisedMessageSubject == null)) {

      if (isset($_SESSION['gymafi_userid'])) {


        $sendGroupReplyToCoach = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, group_id, hide)
VALUES ($coach, $userid, '$sanitisedMessageSubject', '$sanitisedMessageText', $coach, $coach, $groupID, 0)";

        $sendGroupReplyToFirst = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, group_id, hide)
VALUES ($firstMember, $userid, '$sanitisedMessageSubject', '$sanitisedMessageText', $coach, $firstMember, $groupID, 0)";

        $sendGroupReplyToSecond = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, group_id, hide)
VALUES ($secondMember, $userid, '$sanitisedMessageSubject', '$sanitisedMessageText', $coach, $secondMember, $groupID, 0)";

        $sendGroupReplyToThird = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, group_id, hide)
VALUES ($thirdMember, $userid, '$sanitisedMessageSubject', '$sanitisedMessageText', $coach, $thirdMember, $groupID, 0)";

        $sendGroupReplyToFourth = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, group_id, hide)
VALUES ($fourthMember, $userid, '$sanitisedMessageSubject', '$sanitisedMessageText', $coach, $fourthMember, $groupID, 0)";
      } else if (isset($_SESSION['gymafi_coachid'])) {

        $sendGroupReplyToFirst = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, group_id, hide)
VALUES ($firstMember, $loggedInCoachId, '$sanitisedMessageSubject', '$sanitisedMessageText', $loggedInCoachId, $firstMember, $groupID, 0)";

        $sendGroupReplyToSecond = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, group_id, hide)
VALUES ($secondMember, $loggedInCoachId, '$sanitisedMessageSubject', '$sanitisedMessageText', $loggedInCoachId, $secondMember, $groupID, 0)";

        $sendGroupReplyToThird = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, group_id, hide)
VALUES ($thirdMember, $loggedInCoachId, '$sanitisedMessageSubject', '$sanitisedMessageText', $loggedInCoachId, $thirdMember, $groupID, 0)";

        $sendGroupReplyToFourth = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, group_id, hide)
VALUES ($fourthMember, $loggedInCoachId, '$sanitisedMessageSubject', '$sanitisedMessageText', $loggedInCoachId, $fourthMember, $groupID, 0)";
      }
      // transaction adapted from online tutorial <https://www.youtube.com/watch?v=CNt9HPqDIVc>
      $conn->autocommit(false);

      $error = array();

      if (!isset($_SESSION['gymafi_coachid'])) {
        $a = $conn->query($sendGroupReplyToCoach);
        if ($a == false) {
          $groupReplyError = "Could not send group message to coach group member.";
          array_push($error, $groupReplyError);
        }

        if ($firstMember != $userid) {
          $b = $conn->query($sendGroupReplyToFirst);
          if ($b == false) {
            $groupReplyError = "Could not send group message to first group member.";
            array_push($error, $groupReplyError);
          }
        }

        if ($secondMember != $userid) {
          $c = $conn->query($sendGroupReplyToSecond);
          if ($c == false) {
            $groupReplyError = "Could not send group message to second group member.";
            array_push($error, $groupReplyError);
          }
        }

        if ($thirdMember != $userid) {
          $d = $conn->query($sendGroupReplyToThird);
          if ($d == false) {
            $groupReplyError = "Could not send group message to third group member.";
            array_push($error, $groupReplyError);
          }
        }

        if ($fourthMember != $userid) {
          $e = $conn->query($sendGroupReplyToFourth);
          if ($e == false) {
            $groupReplyError = "Could not send group message to fourth group member.";
            array_push($error, $groupReplyError);
          }
        }
      } else { // if coach, no need to send message to coach or check if their id == group member id

        $b = $conn->query($sendGroupReplyToFirst);
        if ($b == false) {
          $groupReplyError = "Could not send group message to first group member.";
          array_push($error, $groupReplyError);
        }


        $c = $conn->query($sendGroupReplyToSecond);
        if ($c == false) {
          $groupReplyError = "Could not send group message to second group member.";
          array_push($error, $groupReplyError);
        }


        $d = $conn->query($sendGroupReplyToThird);
        if ($d == false) {
          $groupReplyError = "Could not send group message to third group member.";
          array_push($error, $groupReplyError);
        }


        $e = $conn->query($sendGroupReplyToFourth);
        if ($e == false) {
          $groupReplyError = "Could not send group message to fourth group member.";
          array_push($error, $groupReplyError);
        }
      }

      /**
       * If error array is not empty, one of the queries in the transaction 
       * has failed and it is rolled back. Else, commits the transaction.
       */
      if (!empty($error)) {
        $conn->rollback();
        $groupReplyError = "Cannot send message - please check input and try again.";
        echo $conn->error;
      } else {
        //commit if all ok
        $conn->commit();
        $groupReplySuccess = "Group message successfully sent.";
      }
    } else {
      $groupReplyError = "Cannot send group message - please check input and try again.";
    }
  }
}





/**
 * If a normal user clicks on 'read message' after selecting from dropdown, displays modal with message
 * Offers reply button to reply to coach, sending data when the reply modal opens.
 */
if (isset($_POST['readMessage'])) {
  $messageID = $_POST['messageToRead'];



  $getMessageInfo = $conn->prepare("SELECT webdev_inbox.subject, webdev_inbox.message, 
webdev_inbox.attachment,  webdev_coach.name 
FROM webdev_inbox 
INNER JOIN webdev_coach
ON webdev_inbox.coach = webdev_coach.id
WHERE webdev_inbox.id = ? ");
  $getMessageInfo->bind_param("i", $messageID);
  $getMessageInfo->execute();
  $getMessageInfo->store_result();
  $getMessageInfo->bind_result($subject, $messageText, $attachment, $coachName);
  $getMessageInfo->fetch();



  echo "<div class='modal is-active' id='addLog'>
        <div class='modal-background'></div>
        <div class='modal-card'>
          <header class='modal-card-head'>
            <p class='modal-card-title'>Reading message</p>
            <button class='delete cancelUpdate' aria-label='close' ></button>
          
          </header>
         
          <section class='modal-card-body'>
         
            <form action='inbox.php' method='POST' enctype='multipart/form-data'>
      
              <div class='field'>
                <label class='label'>Subject: </label>
                <div class='control'>
                ";
?>
  <input class='input' type='text' value="<?php echo $subject ?>" name='subject' readonly>
  </div>

  </div>
  <?php
  echo "
              <input type='hidden' id='coachID'  value='$messageID' name='msgID' readonly>
      
              <div class='field'>
              <label class='label'>From: </label>
              <div class='control'>
              ";
  ?>

  <input class='input' type='text' value="<?php echo $coachName ?>" name='coachName' readonly>
  </div>
  </div>
  <?php
  echo "
            <input type='hidden' id='userID' value='$userid' name='messageSender' readonly>
      
      
            <div class='field'>
            <label class='label'>Message: </label>
            <div class='control'>
              <textarea class='textarea' name='messageText' readonly>$messageText</textarea>
            </div>
           
          </div>
      
      
          <label class='label' >Attachment: </label>
          ";
  if ($attachment != null) {
    echo "<p><a class='button is-warning' name='downloadAttachment' href='images/attachments/$attachment' 
            download='$attachment'>Download Attachment - $attachment</a></p>
          ";
  }
  echo "    
            <footer class='modal-card-foot'>
            <input type='submit' class='button is-success' id='coachMsgSubmit' value='Reply to Message' name='replyToMsg'>
      
          </footer>
      
      
            </form>
          </section>
         
        </div>
      </div>";
}


/**
 * If a coach clicks on 'read message' after selecting from dropdown, displays modal with message
 * Offers reply button to reply to client, sending data when the reply modal opens.
 */
if (isset($_POST['readUserMessage'])) {
  $messageID = $_POST['userMessageToRead'];


  $getMessageInfo = $conn->prepare("SELECT webdev_inbox.subject, webdev_inbox.message, 
    webdev_inbox.attachment, webdev_inbox.sender, webdev_user_details.name
    FROM webdev_inbox 
    INNER JOIN webdev_user_details
    ON webdev_inbox.user = webdev_user_details.user_id
    WHERE webdev_inbox.id = ? ");
  $getMessageInfo->bind_param("i", $messageID);
  $getMessageInfo->execute();
  $getMessageInfo->store_result();
  $getMessageInfo->bind_result($subject, $messageText, $attachment, $messageUserId, $senderName);
  $getMessageInfo->fetch();



  echo "<div class='modal is-active' id='readUserMessage'>
        <div class='modal-background'></div>
        <div class='modal-card'>
          <header class='modal-card-head'>
            <p class='modal-card-title'>Reading message</p>
            <button class='delete cancelUpdate' aria-label='close' ></button>
          
          </header>
         
          <section class='modal-card-body'>
         
            <form action='inbox.php' method='POST' enctype='multipart/form-data'>
      
              <div class='field'>
                <label class='label'>Subject: </label>
                <div class='control'>
                ";
  ?>
  <input class='input' type='text' value="<?php echo $subject ?>" name='subject' readonly>
  </div>
  <?php
  echo "
              </div>
      
              <input type='hidden' id='coachID'  value='$messageID' name='msgID' readonly>
      
              <div class='field'>
              <label class='label'>From: </label>
              <div class='control'>
              ";
  ?>
  <input class='input' type='text' value="<?php echo $senderName ?>" name='senderName' readonly>
  <?php
  echo "
              </div>
            </div>
            <input type='hidden' id='userID' value='$messageUserId' name='messageSender' readonly>
      
      
            <div class='field'>
            <label class='label'>Message: </label>
            <div class='control'>
              <textarea class='textarea' name='messageText' readonly>$messageText</textarea>
            </div>
           
          </div>
      
      
          <label class='label' >Attachment: </label>
          ";
  if ($attachment != null) {
    echo "<p><a class='button is-warning' name='downloadAttachment' href='images/attachments/$attachment' 
            download='$attachment'>Download Attachment - $attachment</a></p>
          ";
  }
  echo "    
            <footer class='modal-card-foot'>
            <input type='submit' class='button is-success' id='coachMsgSubmit' value='Reply to Message' name='replyToUserMsg'>
      
          </footer>
      
      
            </form>
          </section>
         
        </div>
      </div>";
}


/**
 * If a coach tries to reply to a user, displays this modal.
 * 
 */
if (isset($_POST['replyToUserMsg'])) {
  if (!isset($_POST['msgID'])) {
    $replyError = "Cannot reply - empty entry selected.";
  }

  $messageID = $_POST['msgID'];


  $getReplyToMsgInfo = $conn->prepare("SELECT webdev_inbox.sender, webdev_inbox.subject, webdev_user_details.name
  FROM webdev_inbox 
  INNER JOIN webdev_user_details
  ON webdev_inbox.user = webdev_user_details.user_id
  WHERE webdev_inbox.id  = ?");
  $getReplyToMsgInfo->bind_param("i", $messageID);
  $getReplyToMsgInfo->execute();
  $getReplyToMsgInfo->store_result();
  $getReplyToMsgInfo->bind_result($recipient, $subject, $userName);
  $getReplyToMsgInfo->fetch();




  echo "  <div class='modal is-active' id='replyMsg'>
        <div class='modal-background'></div>
        <div class='modal-card'>
          <header class='modal-card-head'>
            <p class='modal-card-title'>Reply to '$subject'</p>
            <button class='delete cancelUpdate' aria-label='close' ></button>
          
          </header>
         
          <section class='modal-card-body'>
         
            <form action='inbox.php' method='POST' enctype='multipart/form-data' id='coachReplyToUser'>
      
              <div class='field'>
                <label class='label'>Recipient: </label>
                <div class='control'>
                  <input class='input' type='text' value='$userName' name='replyRecipient' readonly>
                </div>
              </div>

    
              <input type='hidden' value='$messageID' name='initialMsg' readonly>
      
              <div class='field'>
              <label class='label'>Subject: </label>
              <div class='control'>
              ";
  ?>
  <input class='input' type='text' value="<?php echo $subject ?>" name='replySubject' readonly>
  </div>
  <?php
  echo "
            
            </div>
            <input type='hidden' id='userID' value='$recipient' name='replyRecipientID' readonly>
      ";
  ?>

  <div class='field'>
    <label class='label'>Message: </label>
    <div class='control'>
      <textarea class='textarea' id='messageBody' placeholder='Put your response here' name='replyMessageText'></textarea>
    </div>
    <p class='messageWarn help is-danger'></p>
  </div>


  <div id='fileUploader' class='file has-name field'>
    <label class='label'>Attachment: </label>

    <label class='file-label' id='attachLabel'>
      <!-- Allowed file types adapted from https://stackoverflow.com/questions/52047925/uploading-docx-files-using-php-->
      <input class='file-input' type='file' name='replyMsgAttachment' accept='image/png, image/gif, image/jpeg, 
            text/plain, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document'>
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
  <p>Accepted formats: png, gif, jpeg, pdf, txt, doc, docx</p>
  <p> File must be <2mb.</p> <footer class='modal-card-foot'>
      <input type='submit' class='button is-success coachReplyToUserButton' id='replyMsgSubmit' value='Send Reply' name='replyUserMessage'>

      </footer>


      </form>
      </section>

      </div>
      </div>
      <!-- Changes the file name shown on the upload section to be whatever file the user selects
From https://bulma.io/documentation/form/file/ -->
      <script>
        const fileInput = document.querySelector('#fileUploader input[type=file]');
        fileInput.onchange = () => {
          if (fileInput.files.length > 0) {
            const fileName = document.querySelector('#fileUploader .file-name');
            fileName.textContent = fileInput.files[0].name;
          }
        }
      </script>
    <?php
  }


  /**
   * If a coach pushes a request to send a reply, 
   * Sanitises all input from the user to protect against SQL injections.
   * Performs validation to ensure data types (e.g if sender ID is not numeric, which is hidden from the user, 
   * will print out an error)
   * Then determines if  an attachment has been set. Performs a check to see if a file has actually been uploaded, 
   * possibility of file name being sent but file not being uploaded (over limit), 
   * so only performs the 'attachment query' if file exists in temp folder.
   * If a file has been posted, then checks the file extensions. If not compatible, message is not sent and error 
   * is displayed.
   * If no attachment, attempt to write to DB without putting anything in the attachment row.
   */


  if (isset($_POST['replyUserMessage'])) {
    $initialMessage = $_POST['initialMsg'];
    $sanitisedInitialMessage = $conn->real_escape_string(trim($initialMessage));
    $replyRecipient = $_POST['replyRecipient'];
    $sanitisedReplyRecipient = $conn->real_escape_string(trim($replyRecipient));
    $replySubject = $_POST['replySubject'];
    $sanitisedReplySubject = $conn->real_escape_string(trim($replySubject));
    $replyRecipientID = $_POST['replyRecipientID'];
    $sanitisedReplyRecipientID = $conn->real_escape_string(trim($replyRecipientID));
    $replyMessage = $_POST['replyMessageText'];
    $sanitisedReplyMessage = $conn->real_escape_string(trim($replyMessage));
    $replyAttachment = $_FILES['replyMsgAttachment']['name'];
    $sanitisedReplyAttachment = $conn->real_escape_string(trim($replyAttachment));


    if (!ctype_digit($sanitisedReplyRecipientID)) {
      $replyError = "Error - your reply has not been sent.";
    } else {
      if ($sanitisedReplyAttachment != null) {
        $filename = $_FILES['replyMsgAttachment']['name'];
        $sanitisedFileName = $conn->real_escape_string(trim($filename));
        $filetemp = $_FILES['replyMsgAttachment']['tmp_name'];
        // check if file type supported. 
        $fileExt = pathinfo($sanitisedFileName);
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
          || $fileExt["extension"] == "jpeg" ||  $fileExt["extension"] == "pdf"
          || $fileExt["extension"] == "pDf" || $fileExt["extension"] == "pdF"
          || $fileExt["extension"] == "PdF" || $fileExt["extension"] == "Pdf"
          || $fileExt["extension"] == "PDf" || $fileExt["extension"] == "pDF"
          || $fileExt["extension"] == "PDF" ||  $fileExt["extension"] == "txt"
          || $fileExt["extension"] == "Txt" || $fileExt["extension"] == "tXt"
          || $fileExt["extension"] == "txT" || $fileExt["extension"] == "tXT"
          || $fileExt["extension"] == "TxT" || $fileExt["extension"] == "TXt"
          || $fileExt["extension"] == "TXT" ||  $fileExt["extension"] == "docx"
          || $fileExt["extension"] == "dOcx" || $fileExt["extension"] == "doCx"
          || $fileExt["extension"] == "docX" || $fileExt["extension"] == "Docx"
          || $fileExt["extension"] == "DOcx" || $fileExt["extension"] == "DOcX"
          || $fileExt["extension"] == "DOCX" || $fileExt["extension"] == "DoCx"
          || $fileExt["extension"] == "DoCX" || $fileExt["extension"] == "DocX"
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
          while (file_exists('images/attachments/' . $actual_name . "." . $extension)) {
            $actual_name = (string) $original_name . $i;
            $filename = $actual_name . "." . $extension;
            $i++;
          }

          $sendReply = "INSERT INTO webdev_inbox (recipient, sender, subject, message, attachment, coach, user, hide) 
                VALUES ('$sanitisedReplyRecipientID', '$loggedInCoachId', '$sanitisedReplySubject', '$sanitisedReplyMessage', 
                '$filename', $loggedInCoachId, $sanitisedReplyRecipientID, 0)";

          $hideInitial = "UPDATE webdev_inbox SET webdev_inbox.hide = 1 WHERE webdev_inbox.id = $sanitisedInitialMessage";
          $conn->autocommit(false);

          $error = array();

          $a = $conn->query($sendReply);
          $conn->query($hideInitial);

          if (!empty($error)) {
            $conn->rollback();
            $replyError = "Cannot send message - please check input and try again.";
          } else {
            //commit if all ok
            $conn->commit();
            move_uploaded_file($filetemp, "images/attachments/$filename");
            $replySuccess = "Reply successfully sent.";
          }
        } else {
          $replyError = "File type not supported. Please try a different file.";
        }
      } else {
        if ((!$sanitisedReplySubject == null) && (!$sanitisedReplyMessage == null)) {
          $sendReplyMessage = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, hide) 
                VALUES ('$sanitisedReplyRecipientID', '$loggedInCoachId', '$sanitisedReplySubject', 
                '$sanitisedReplyMessage', $loggedInCoachId, $sanitisedReplyRecipientID, 0)";
          $hideInitial = "UPDATE webdev_inbox SET webdev_inbox.hide = 1 WHERE webdev_inbox.id = $initialMessage";

          // transaction adapted from online tutorial <https://www.youtube.com/watch?v=CNt9HPqDIVc>
          $conn->autocommit(false);
          $error = array();
          $a = $conn->query($sendReplyMessage);
          if ($a == false) {
            $replyError = "Error sending reply message.";
            array_push($error, $replyError);
          }
          $b =  $conn->query($hideInitial);
          if ($b == false) {
            $replyError = "Error hiding initial message.";
            array_push($error, $replyError);
          }
          /**
           * If error array is not empty, one of the queries in the transaction 
           * has failed and it is rolled back. Else, commits the transaction.
           */
          if (!empty($error)) {
            $conn->rollback();
            echo $conn->error;
            $replyError = "Cannot send reply - please check input and try again.";
          } else {
            //commit if all ok
            $conn->commit();
            $replySuccess = "Reply successfully sent.";
          }
        } else {
          $replyError = "Cannot send reply - please check input and try again.";
        }
      }
    }
  } // end of sending reply


  /**
   * Allows the user to compose a reply to a group.
   * All group members, apart from the person sending it, will recieve a copy of the message
   * and any attachment that is sent.
   */
  if (isset($_POST['replyToGroup'])) {
    if (!isset($_POST['msgID'])) {
      $replyError = "Cannot reply - empty entry selected.";
    }
    $msgID = $_POST['msgID'];
    $groupID = $_POST['groupID'];
    $groupName = $_POST['groupName'];

    $getReplyToMsgInfo = $conn->prepare("SELECT webdev_inbox.subject
  FROM webdev_inbox 

  WHERE webdev_inbox.id  = ?");
    $getReplyToMsgInfo->bind_param("i", $msgID);
    $getReplyToMsgInfo->execute();
    $getReplyToMsgInfo->store_result();
    $getReplyToMsgInfo->bind_result($subject);
    $getReplyToMsgInfo->fetch();



    echo " <div class='modal is-active' id='replyMsg'>
        <div class='modal-background'></div>
        <div class='modal-card'>
          <header class='modal-card-head'>
            <p class='modal-card-title'>Reply to '$subject'</p>
            <button class='delete cancelUpdate' aria-label='close' ></button>
          
          </header>
         
          <section class='modal-card-body'>
         
            <form action='inbox.php' method='POST' enctype='multipart/form-data' id='coachReplyToUser'>
      
              <div class='field'>
                <label class='label'>Recipient: </label>
                <div class='control'>
                  <input class='input' type='text' value='$groupName' name='replyRecipient' readonly>
                </div>
              </div>

    
              <input type='hidden' value='$groupID ' name='groupID' readonly>
              <input type='hidden' value='$msgID' name='initialMessage' readonly>
      
              <div class='field'>
              <label class='label'>Subject: </label>
              <div class='control'>
              ";
    ?>
      <input class='input' type='text' value="<?php echo $subject ?>" name='replySubject' readonly>
    <?php
    echo "
              </div>
            
            </div>
          
      
      
            <div class='field'>
            <label class='label'>Message: </label>
            <div class='control'>
              <textarea class='textarea' id='messageBody'  placeholder='Put your response here' name='replyMessageText'></textarea>
            </div>
            <p class='messageWarn help is-danger'></p>
          </div>
      
      
          <div id='fileUploader' class='file has-name field' >
          <label class='label' >Attachment: </label>
          
          <label class='file-label' id='attachLabel'> 
          <!-- Allowed file types adapted from https://stackoverflow.com/questions/52047925/uploading-docx-files-using-php-->
            <input class='file-input' type='file' name='replyMsgAttachment' accept='image/png, image/gif, image/jpeg, 
            text/plain, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document''>
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
        <p>Accepted formats: png, gif, jpeg, pdf, .txt, .docx.</p>
        <p> File must be <2mb.</p>
      
      
            
            <footer class='modal-card-foot'>
            <input type='submit' class='button is-success coachReplyToUserButton' id='replyMsgSubmit' value='Send Reply' name='submitReplyToGroup'>
      
          </footer>
      
      
            </form>
          </section>
   
  </div>
</div>
<!-- Changes the file name shown on the upload section to be whatever file the user selects
From https://bulma.io/documentation/form/file/ -->
<script>const fileInput = document.querySelector('#fileUploader input[type=file]');
fileInput.onchange = () => {
  if (fileInput.files.length > 0) {
    const fileName = document.querySelector('#fileUploader .file-name');
    fileName.textContent = fileInput.files[0].name;
  }
}
</script>";
  }

  /**
   * If a user pushes a request to send a reply to a group 
   * Sanitises all input from the user to protect against SQL injections.
   * Performs validation to ensure data types (e.g if sender ID is not numeric, which is hidden from the user, 
   * will print out an error)
   * Then determines if  an attachment has been set. Performs a check to see if a file has actually been uploaded, 
   * possibility of file name being sent but file not being uploaded (over limit), 
   * so only performs the 'attachment query' if file exists in temp folder.
   * If a file has been posted, then checks the file extensions. If not compatible, message is not sent and error 
   * is displayed.
   * If no attachment, attempt to write to DB without putting anything in the attachment row.
   */
  if (isset($_POST['submitReplyToGroup'])) {
    $initialMessage = $_POST['initialMessage'];
    $sanitisedInitialMessage = $conn->real_escape_string(trim($initialMessage));
    $replyAttachment = $_FILES['replyMsgAttachment']['name'];
    $sanitisedReplyAttachment = $conn->real_escape_string(trim($replyAttachment));
    $replyMessage = $_POST['replyMessageText'];
    $sanitisedReplyMessage = $conn->real_escape_string(trim($replyMessage));
    $replySubject = $_POST['replySubject'];
    $sanitisedReplySubject = $conn->real_escape_string(trim($replySubject));

    $groupID = $_POST['groupID'];



    $getGroupInfo = $conn->prepare("SELECT group_name, coach, member_one, member_two, member_three, member_four
    FROM webdev_groups
    WHERE id = ?");
    $getGroupInfo->bind_param("i", $groupID);
    $getGroupInfo->execute();
    $getGroupInfo->store_result();
    $getGroupInfo->bind_result($groupName, $coach, $firstMember, $secondMember, $thirdMember, $fourthMember);
    $getGroupInfo->fetch();

    if ($sanitisedReplyAttachment != null) {
      $filename = $_FILES['replyMsgAttachment']['name'];
      $sanitisedFileName = $conn->real_escape_string(trim($filename));
      $filetemp = $_FILES['replyMsgAttachment']['tmp_name'];
      // check if file type supported. 
      $fileExt = pathinfo($sanitisedFileName);

      if (
        $fileExt["extension"] == "png" || $fileExt["extension"] == "Png"
        || $fileExt["extension"] == "PNg" || $fileExt["extension"] == "PNG"
        || $fileExt["extension"] == "pNg" || $fileExt["extension"] == "pnG"
        || $fileExt["extension"] == "pNG" || $fileExt["extension"] == "PnG"
        ||  $fileExt["extension"] == "jpg" || $fileExt["extension"] == "jPg"
        || $fileExt["extension"] == "jpG" || $fileExt["extension"] == "jPG"
        || $fileExt["extension"] == "JPG" || $fileExt["extension"] == "JPg"
        || $fileExt["extension"] == "Jpg" || $fileExt["extension"] == "JpG"
        || $fileExt["extension"] == "gif" || $fileExt["extension"] == "gIf"
        || $fileExt["extension"] == "giF" || $fileExt["extension"] == "gIF"
        || $fileExt["extension"] == "Gif" || $fileExt["extension"] == "GIf"
        || $fileExt["extension"] == "GiF" || $fileExt["extension"] == "GIF"
        || $fileExt["extension"] == "jpeg" || $fileExt["extension"] == "pdf"
        || $fileExt["extension"] == "pDf" || $fileExt["extension"] == "pdF"
        || $fileExt["extension"] == "PdF" || $fileExt["extension"] == "Pdf"
        || $fileExt["extension"] == "PDf" || $fileExt["extension"] == "pDF"
        || $fileExt["extension"] == "PDF" ||  $fileExt["extension"] == "txt"
        || $fileExt["extension"] == "Txt" || $fileExt["extension"] == "tXt"
        || $fileExt["extension"] == "txT" || $fileExt["extension"] == "tXT"
        || $fileExt["extension"] == "TxT" || $fileExt["extension"] == "TXt"
        || $fileExt["extension"] == "TXT" ||  $fileExt["extension"] == "docx"
        || $fileExt["extension"] == "dOcx" || $fileExt["extension"] == "doCx"
        || $fileExt["extension"] == "docX" || $fileExt["extension"] == "Docx"
        || $fileExt["extension"] == "DOcx" || $fileExt["extension"] == "DOcX"
        || $fileExt["extension"] == "DOCX" || $fileExt["extension"] == "DoCx"
        || $fileExt["extension"] == "DoCX" || $fileExt["extension"] == "DocX"
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
        while (file_exists('images/attachments/' . $actual_name . "." . $extension)) {
          $actual_name = (string) $original_name . $i;
          $filename = $actual_name . "." . $extension;
          $i++;
        }

        //If a client is logged in, generates messages to send to all group members and coach.

        if (isset($_SESSION['gymafi_userid'])) {
          $sendGroupReplyToCoach = "INSERT INTO webdev_inbox (recipient, sender, subject, message, attachment, coach, user, group_id, hide)
          VALUES ($coach, $userid, '$sanitisedReplySubject', '$sanitisedReplyMessage', '$filename', $coach, $coach, $groupID, 0)";

          $sendGroupReplyToFirst = "INSERT INTO webdev_inbox (recipient, sender, subject, message, attachment, coach, user, group_id, hide)
         VALUES ($firstMember, $userid, '$sanitisedReplySubject', '$sanitisedReplyMessage', '$filename', $coach, $firstMember, $groupID, 0)";

          $sendGroupReplyToSecond = "INSERT INTO webdev_inbox (recipient, sender, subject, message, attachment, coach, user, group_id, hide)
          VALUES ($secondMember, $userid, '$sanitisedReplySubject', '$sanitisedReplyMessage', '$filename', $coach, $secondMember, $groupID, 0)";

          $sendGroupReplyToThird = "INSERT INTO webdev_inbox (recipient, sender, subject, message, attachment, coach, user, group_id, hide)
          VALUES ($thirdMember, $userid, '$sanitisedReplySubject', '$sanitisedReplyMessage', '$filename', $coach, $thirdMember, $groupID, 0)";

          $sendGroupReplyToFourth = "INSERT INTO webdev_inbox (recipient, sender, subject, message, attachment, coach, user, group_id, hide)
          VALUES ($fourthMember, $userid, '$sanitisedReplySubject', '$sanitisedReplyMessage', '$filename', $coach, $fourthMember, $groupID, 0)";
          //If a coach is logged in, generates messages to send to all group members
        } else if (isset($_SESSION['gymafi_coachid'])) {

          $sendGroupReplyToFirst = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, group_id, hide)
           VALUES ($firstMember, $loggedInCoachId, '$sanitisedReplySubject', '$sanitisedReplyMessage', $loggedInCoachId, $firstMember, $groupID, 0)";

          $sendGroupReplyToSecond = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, group_id, hide)
         VALUES ($secondMember, $loggedInCoachId, '$sanitisedReplySubject', '$sanitisedReplyMessage', $loggedInCoachId, $secondMember, $groupID, 0)";

          $sendGroupReplyToThird = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, group_id, hide)
          VALUES ($thirdMember, $loggedInCoachId, '$sanitisedReplySubject', '$sanitisedReplyMessage', $loggedInCoachId, $thirdMember, $groupID, 0)";

          $sendGroupReplyToFourth = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, group_id, hide)
          VALUES ($fourthMember, $loggedInCoachId, '$sanitisedReplySubject', '$sanitisedReplyMessage', $loggedInCoachId, $fourthMember, $groupID, 0)";
        }


        $hideInitial = "UPDATE webdev_inbox SET webdev_inbox.hide = 1 WHERE webdev_inbox.id = $sanitisedInitialMessage";

        // transaction adapted from online tutorial <https://www.youtube.com/watch?v=CNt9HPqDIVc>
        $conn->autocommit(false);

        $error = array();

        /**
         * If a client, sends message to coach.
         * Then checks user id against all members in group
         * If a match, does not send message to that user as they should not message themselves.
         */
        if (!isset($_SESSION['gymafi_coachid'])) {

          $a = $conn->query($sendGroupReplyToCoach);
          if ($a == false) {
            $replyError = "Error sending reply to coach.";
            array_push($error,  $replyError);
          }

          if ($firstMember != $userid) {
            $b = $conn->query($sendGroupReplyToFirst);
            if ($b == false) {
              $replyError = "Error sending reply to first group member.";
              array_push($error,  $replyError);
            }
          }

          if ($secondMember != $userid) {
            $c = $conn->query($sendGroupReplyToSecond);
            if ($c == false) {
              $replyError = "Error sending reply to second group member.";
              array_push($error,  $replyError);
            }
          }

          if ($thirdMember != $userid) {
            $d = $conn->query($sendGroupReplyToThird);
            if ($d == false) {
              $replyError = "Error sending reply to third group member.";
              array_push($error,  $replyError);
            }
          }

          if ($fourthMember != $userid) {
            $e = $conn->query($sendGroupReplyToFourth);
            if ($e == false) {
              $replyError = "Error sending reply to fourth group member.";
              array_push($error,  $replyError);
            }
          }
          /**
           * Else if a coach, sends message to all members of group.
           */
        } else {

          $b = $conn->query($sendGroupReplyToFirst);
          if ($b == false) {
            $replyError = "Error sending reply to first group member.";
            array_push($error,  $replyError);
          }

          $c = $conn->query($sendGroupReplyToSecond);
          if ($c == false) {
            $replyError = "Error sending reply to second group member.";
            array_push($error,  $replyError);
          }

          $d = $conn->query($sendGroupReplyToThird);
          if ($d == false) {
            $replyError = "Error sending reply to third group member.";
            array_push($error,  $replyError);
          }

          $e = $conn->query($sendGroupReplyToFourth);
          if ($e == false) {
            $replyError = "Error sending reply to fourth group member.";
            array_push($error,  $replyError);
          }
        }

        $f = $conn->query($hideInitial);
        if ($f == false) {
          array_push($error, 'Problem pushing to db');
        }

        /**
         * If error array is not empty, one of the queries in the transaction 
         * has failed and it is rolled back. Else, commits the transaction.
         */
        if (!empty($error)) {
          $conn->rollback();
          echo $conn->error;
        } else {
          //commit if all ok
          $conn->commit();
          move_uploaded_file($filetemp, "images/attachments/$filename");
          $replySuccess = "Reply successfully sent.";
        }
      } else {
        $replyError = "File type not supported. Please try a different file.";
      }
    } else {
      if ((!$sanitisedReplySubject == null) && (!$sanitisedReplyMessage == null)) {

        if (isset($_SESSION['gymafi_userid'])) {


          $sendGroupReplyToCoach = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, group_id, hide)
          VALUES ($coach, $userid, '$sanitisedReplySubject', '$sanitisedReplyMessage', $coach, $coach, $groupID, 0)";

          $sendGroupReplyToFirst = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, group_id, hide)
          VALUES ($firstMember, $userid, '$sanitisedReplySubject', '$sanitisedReplyMessage', $coach, $firstMember, $groupID, 0)";

          $sendGroupReplyToSecond = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, group_id, hide)
          VALUES ($secondMember, $userid, '$sanitisedReplySubject', '$sanitisedReplyMessage', $coach, $secondMember, $groupID, 0)";

          $sendGroupReplyToThird = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, group_id, hide)
         VALUES ($thirdMember, $userid, '$sanitisedReplySubject', '$sanitisedReplyMessage', $coach, $thirdMember, $groupID, 0)";

          $sendGroupReplyToFourth = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, group_id, hide)
          VALUES ($fourthMember, $userid, '$sanitisedReplySubject', '$sanitisedReplyMessage', $coach, $fourthMember, $groupID, 0)";
        } else if (isset($_SESSION['gymafi_coachid'])) {

          $sendGroupReplyToFirst = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, group_id, hide)
          VALUES ($firstMember, $loggedInCoachId, '$sanitisedReplySubject', '$sanitisedReplyMessage', $loggedInCoachId, $firstMember, $groupID, 0)";

          $sendGroupReplyToSecond = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, group_id, hide)
          VALUES ($secondMember, $loggedInCoachId, '$sanitisedReplySubject', '$sanitisedReplyMessage', $loggedInCoachId, $secondMember, $groupID, 0)";

          $sendGroupReplyToThird = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, group_id, hide)
          VALUES ($thirdMember, $loggedInCoachId, '$sanitisedReplySubject', '$sanitisedReplyMessage', $loggedInCoachId, $thirdMember, $groupID, 0)";

          $sendGroupReplyToFourth = "INSERT INTO webdev_inbox (recipient, sender, subject, message, coach, user, group_id, hide)
          VALUES ($fourthMember, $loggedInCoachId, '$sanitisedReplySubject', '$sanitisedReplyMessage', $loggedInCoachId, $fourthMember, $groupID, 0)";
        }

        $hideInitial = "UPDATE webdev_inbox SET webdev_inbox.hide = 1 WHERE webdev_inbox.id = $sanitisedInitialMessage";
        // transaction adapted from online tutorial <https://www.youtube.com/watch?v=CNt9HPqDIVc>
        $conn->autocommit(false);

        $error = array();

        if (!isset($_SESSION['gymafi_coachid'])) {
          $a = $conn->query($sendGroupReplyToCoach);
          if ($a == false) {
            $replyError = "Error sending reply to group coach.";
            array_push($error,  $replyError);
            echo $conn->error;
          }

          if ($firstMember != $userid) {
            $b = $conn->query($sendGroupReplyToFirst);
            if ($b == false) {
              $replyError = "Error sending reply to first group member.";
              array_push($error,  $replyError);
              echo $conn->error;
            }
          }

          if ($secondMember != $userid) {
            $c = $conn->query($sendGroupReplyToSecond);
            if ($c == false) {
              $replyError = "Error sending reply to second group member.";
              array_push($error,  $replyError);
              echo $conn->error;
            }
          }

          if ($thirdMember != $userid) {
            $d = $conn->query($sendGroupReplyToThird);
            if ($d == false) {
              $replyError = "Error sending reply to third group member.";
              array_push($error,  $replyError);
              echo $conn->error;
            }
          }

          if ($fourthMember != $userid) {
            $e = $conn->query($sendGroupReplyToFourth);
            if ($e == false) {
              $replyError = "Error sending reply to fourth group member.";
              array_push($error,  $replyError);
              echo $conn->error;
            }
          }
        } else {

          $b = $conn->query($sendGroupReplyToFirst);
          if ($b == false) {
            $replyError = "Error sending reply to first group member.";
            array_push($error,  $replyError);
            echo $conn->error;
          }


          $c = $conn->query($sendGroupReplyToSecond);
          if ($c == false) {
            $replyError = "Error sending reply to second group member.";
            array_push($error,  $replyError);
            echo $conn->error;
          }


          $d = $conn->query($sendGroupReplyToThird);
          if ($d == false) {
            $replyError = "Error sending reply to third group member.";
            array_push($error,  $replyError);
            echo $conn->error;
          }


          $e = $conn->query($sendGroupReplyToFourth);
          if ($e == false) {
            $replyError = "Error sending reply to fourth group member.";
            array_push($error,  $replyError);
            echo $conn->error;
          }
        }
        $f = $conn->query($hideInitial);
        if ($f == false) {
          $replyError = "Error sending hiding initial message.";
          array_push($error,  $replyError);
          echo $conn->error;
        }
        /**
         * If error array is not empty, one of the queries in the transaction 
         * has failed and it is rolled back. Else, commits the transaction.
         */
        if (!empty($error)) {
          $conn->rollback();
          $groupReplyError = "Cannot send message - please check input and try again.";
          echo $conn->error;
        } else {
          //commit if all ok
          $conn->commit();
          $groupReplySuccess = "Reply successfully sent.";
        }
      } else {
        $groupReplyError = "Cannot send reply - please check input and try again.";
      }
    }
  }

    ?>

    <!DOCTYPE html>
    <html>

    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <?php
      echo "<title>Gymafi | Inbox ($numOfMessages)</title>";
      ?>
      <link href="styles/bulma.css" rel="stylesheet">
      <link href="styles/lightbox.css" rel="stylesheet">
      <link href="styles/gui.css" rel="stylesheet">
      <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
      <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
      <script src="script/myScript.js"></script>
      <script src="script/lightbox.js"></script>




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
      } else if (isset($loggedInCoachId)) {
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


            <a class='navbar-item has-background-dark has-text-white' href='appointments.php'>
              Appointments
            </a>

            <a class='navbar-item has-background-dark has-text-white has-background-primary' href='inbox.php'>
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

            <a class='navbar-item has-text-black' href='appointments.php'>
              Appointments
            </a>


            <a class='navbar-item has-text-black ' href='admin/groups.php'>
              Groups
            </a>

            <a class='navbar-item has-text-black  has-background-warning' href='inbox.php'>
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
      ?>

      <?php

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

        if ($isApproved == 1) {
          echo "
  <div id='dashColumns'>
    <div class='columns'>
      <div class='column is-3'>
        <article class='message is-link'>
          <div class='message-header'>
            Message Coach
          </div>

          <div class='message-body'>
         <p> Select a coach to message: </p>
         <form action='inbox.php' method='post'>
         <div class='control select'>
         <select name='messageCoach'>
";




          $selectCoach = $conn->prepare("SELECT webdev_coach.name, webdev_coach.id FROM webdev_coach
        INNER JOIN 
        webdev_user_details
        ON
        webdev_coach.id = webdev_user_details.coach
        WHERE webdev_user_details.user_id = ? ");
          $selectCoach->bind_param("i", $userid);
          $selectCoach->execute();
          $selectCoach->store_result();
          $selectCoach->bind_result($coachName, $coachID);
          $selectCoach->fetch();
          echo "<option value='$coachID'> ", htmlentities($coachName, ENT_QUOTES), "</option>";




          echo "
         </select>
         
</div>
";
          if (isset($messageError)) {
            echo "<p class='displayError'>$messageError</p>";
          } else if (isset($messageSuccess)) {
            echo "<p class='displaySucc'>$messageSuccess</p>";
          }
          echo "
<p class='inboxButton'><input type='submit' class='button is-primary msgCoach' value='Compose Message' name='msgCoach'> </p>
          
      </div> <!-- end of message body-->
      
    </article>
   </form>
";
          echo "<div class='columns'>
<div class='column'>
  <article class='message is-link'>
    <div class='message-header'>
      Message Group
    </div>

    <div class='message-body'>
   <p> Select a group to message: </p>
   <form action='inbox.php' method='post'>
   <div class='control select'>
   <select name='groupsToMessage'>
";

          // select all groups registered to the coach logged in 
          $selectGroupsForCoach = "SELECT id, group_name 
FROM webdev_groups
WHERE member_one = $userid OR member_two = $userid
OR member_three = $userid OR member_four = $userid
ORDER BY group_name ASC";
          $executeSelectGroupsForCoach = $conn->query($selectGroupsForCoach);

          $num = $executeSelectGroupsForCoach->num_rows;

          if ($num == 0) {
            $messageError = "(No one to message.)";
          }

          while ($row = $executeSelectGroupsForCoach->fetch_assoc()) {
            $groupID = $row['id'];
            $groupName = $row['group_name'];

            echo "<option value='$groupID'>$groupName</option>";
          }

          echo "
   </select>
   <input type='hidden' name='groupName' value='$groupName'>
   <input type='hidden' name='groupID' value='$groupID'>

</div>
";
          if (isset($groupReplyError)) {
            echo "<p class='displayError'>$groupReplyError</p>";
          } else if (isset($groupReplySuccess)) {
            echo "<p class='displaySucc'>$groupReplySuccess</p>";
          }
          echo "
<p class='inboxButton'><input type='submit' class='button is-primary msgUser' value='Compose Message' name='messageGroup'> </p>
    
</div> <!-- end of message body-->

</article>
</form>
</div>
</div>
";


          echo "
    <div class='columns'>
    <div class='column'>
    <article class='message is-warning'>
      <div class='message-header'>
      
   Read a message
      
      </div>
     
      <div class='message-body'>
      <form action='inbox.php' method='POST' id='readMessage'>
      <p> Select a message to read: <div class='select'>
   <select name='messageToRead'>";

          $getAllMessages = "SELECT webdev_inbox.id, webdev_inbox.subject, webdev_inbox.coach, webdev_coach.name 
   FROM webdev_inbox  
   INNER JOIN webdev_coach 
   ON webdev_inbox.coach = webdev_coach.id
   WHERE user = '$userid' AND webdev_inbox.recipient = '$userid' 
   ORDER BY webdev_inbox.id DESC";
          $executeGetAllMessages = $conn->query($getAllMessages);

          if (!$executeGetAllMessages) {
            echo $conn->error;
          }

          while ($row = $executeGetAllMessages->fetch_assoc()) {
            $messageID = $row['id'];
            $messageSubject = $row['subject'];
            $sender = $row['coach'];
            $coachName = $row['name'];
            $getCoachInfo = "SELECT * FROM webdev_coach where id = $sender";

            $executeGetCoachInfo = $conn->query($getCoach);

            if (!$executeGetCoachInfo) {
              echo $conn->error;
            }


            echo "<option value='$messageID'> ",
              htmlentities($messageSubject, ENT_QUOTES),
              "(from ",
              htmlentities($coachName, ENT_QUOTES),
              ")</option>";
          }


      ?>
          </select>
          </div>





          <p class='inboxButton'><input type='submit' class='button is-warning readMessage' value='Read Message' name='readMessage'> </p>
          </form>
          </div> <!-- end of message body-->
          </article>
          </div>
          </div>



          </div><!-- end of column-->




          <div class='column is-7' id='rightColumns'>
            <article class='message is-dark'>
              <div class='message-header'>
                <p>
                  <h1 class='title' id='inboxHead'>Unread Messages</h1>
                </p>

              </div>
              <div class='message-body'>


                <?php
                if (isset($replyError)) {
                  echo "<p class='displayError'>$replyError</p>";
                } else if (isset($replySuccess)) {
                  echo "<p class='displaySucc'>$replySuccess</p>";
                }

                $getRecentMessages = "SELECT webdev_inbox.id, webdev_inbox.coach, webdev_inbox.subject, 
    webdev_inbox.message, webdev_inbox.user, webdev_inbox.attachment, webdev_user_details.name,
    webdev_inbox.group_id, webdev_coach.name AS coach_name
        FROM webdev_inbox
        INNER JOIN 
        webdev_user_details 
        ON webdev_inbox.user = webdev_user_details.user_id
        INNER JOIN 
        webdev_coach
        ON webdev_user_details.coach = webdev_coach.id
      WHERE  webdev_inbox.recipient = '$userid' 
      AND webdev_inbox.hide = 0
      ORDER BY webdev_inbox.id DESC";
                $executeGetAllRecentMessages = $conn->query($getRecentMessages);
                if (!$executeGetAllRecentMessages) {
                  echo $conn->error;
                }
                $numOfMessages = $executeGetAllRecentMessages->num_rows;
                $countRecentMessages = 0;
                if ($numOfMessages < 7) {
                  $maxMessages = $numOfMessages;
                } else {
                  $maxMessages = 7;
                }
                while ($countRecentMessages < $maxMessages) {

                  $countRecentMessages++;
                  $row = $executeGetAllRecentMessages->fetch_assoc();
                  $messageID = $row['id'];
                  $coach = $row['coach_name'];
                  $subject = $row['subject'];
                  $message = $row['message'];
                  $user = $row['user'];
                  $attachment = $row['attachment'];
                  $groupID = $row['group_id'];

                  echo "
  <article class='message is-dark'>
  <form action='inbox.php' method='POST'>
";
                  if ($groupID != null) {
                    echo "

    <div class='level message-header'>
      <!-- Left side -->
      <div class='level-left' >
        <div class='level-item'>
       ", htmlentities($subject, ENT_QUOTES), "
        </div>
        
      </div>
    ";

                    $getGroupName = $conn->prepare("SELECT group_name FROM webdev_groups
WHERE id = ?");
                    $getGroupName->bind_param("i", $groupID);
                    $getGroupName->execute();
                    $getGroupName->store_result();
                    $getGroupName->bind_result($groupName);
                    $getGroupName->fetch();

                    $getSenderName = $conn->prepare("SELECT sender, name FROM webdev_inbox
            INNER JOIN webdev_user_details 
            ON webdev_inbox.sender = webdev_user_details.user_id
            WHERE webdev_inbox.id = ? ");
                    $getSenderName->bind_param("i", $messageID);
                    $getSenderName->execute();
                    $getSenderName->store_result();
                    $getSenderName->bind_result($senderID, $senderName);
                    $getSenderName->fetch();

                    echo "
      <!-- Right side -->
      <div class='level-right '>
      <div class='level-item'>";
                    if ($senderName == null) {
                      echo " From: ", htmlentities($coach, ENT_QUOTES), " ($groupName)
</div>";
                    } else {
                      echo "From:", htmlentities($senderName, ENT_QUOTES), "($groupName)
        </div>";
                    }
                    echo "
      </div> 
      
    </div>";
                  } else {
                    echo "

            <div class='level message-header'>
              <!-- Left side -->
              <div class='level-left' >
                <div class='level-item'>
                ", htmlentities($subject, ENT_QUOTES), "
                </div>
                
              </div>
            
              <!-- Right side -->
              <div class='level-right '>
              <div class='level-item'>
              From: ", htmlentities($coach, ENT_QUOTES), "
          </div>
              </div> 
              
            </div>";
                  }

                  echo "
  <div class='message-body'>
  ";
                  if (isset($groupName)) {
                    echo "  <input type='hidden' name='groupName' value='$groupName'>";
                  }
                  echo "
  <input type='hidden' name='groupID' value='$groupID'>
  <input type='hidden' name='msgID' value='$messageID'>
    <p>$message </p>  
    <nav class='level'>
    <!-- Left side -->
    <div class='level-left' >
     
      
   ";
                  if ($attachment != null) {
                    echo "<div class='level-item'>
                    
                    <a class='button is-warning recMsgButtons' name='downloadAttachment' href='images/attachments/$attachment' download='$attachment'>Download Attachment - $attachment</a>
                    </div>";
                  }
                  echo "
              
    
                </div>
                ";
                  if ($groupID != null) {
                    echo "
                  <!-- Right side -->
                  <div class='level-right field is-grouped recMsgButtons'>
                  <input type='submit' value='Reply to Group' class='button is-link replyBut' name='replyToGroup'></a>
                  <input type='submit' value='Mark as Read' class='button is-success' name='markAsRead'></a>";
                  } else {
                    echo "
                  <!-- Right side -->
                  <div class='level-right field is-grouped recMsgButtons'>";
                    if (isset($_SESSION['gymafi_userid'])) {
                      echo " <input type='hidden' name='coachName' value='$coach'>
                     <input type='submit' value='Reply' class='button is-link replyBut' name='replyToMsg'></a> ";
                    } else if (isset($_SESSION['gymafi_coachid'])) {
                      echo " <input type='submit' value='Reply' class='button is-link replyBut' name='replyToUserMsg'></a>";
                    }
                    echo "
                  <input type='submit' value='Mark as Read' class='button is-success' name='markAsRead'></a>";
                  }
                ?>
                  </span></h1></a>


              </div>
              </nav>



          </div>
          </form>
          </article>

        <?php
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

        // Else, if coach logged in display same UI but grab different info.
      } else if (isset($loggedInCoachId)) {
        echo "<div id='dashColumns'>
    <div class='columns'>
      <div class='column is-3'>
        <article class='message is-link'>
          <div class='message-header'>
            Message Client
          </div>

          <div class='message-body'>
         <p> Select a client to message: </p>
         <form action='inbox.php' method='post'>
         <div class='control select'>
         <select name='messageUser'>
         <option value='001'>All Clients</option>
";

        // select all users registered to the coach logged in 
        $selectUsersForCoach = "SELECT webdev_users.id, webdev_user_details.name FROM webdev_coach 
    INNER JOIN webdev_user_details
    ON webdev_coach.id = webdev_user_details.coach
    INNER JOIN webdev_users
    ON webdev_user_details.user_id = webdev_users.id
    WHERE webdev_coach.id = $loggedInCoachId
    ORDER BY webdev_user_details.name ASC";
        $executeSelectUsersForCoach = $conn->query($selectUsersForCoach);

        $num = $executeSelectUsersForCoach->num_rows;

        if ($num == 0) {
          $messageError = "(No one to message.)";
        }

        while ($row = $executeSelectUsersForCoach->fetch_assoc()) {
          $userID = $row['id'];
          $userName = $row['name'];

          // adds a new option to the drop down list for all approved users to the logged in coach
          echo "<option value='$userID'>", htmlentities($userName, ENT_QUOTES), "</option>";
        }

        echo "
         </select>
 

</div>
";
        if (isset($messageError)) {
          echo "<p class='displayError'>$messageError</p>";
        } else if (isset($messageSuccess)) {
          echo "<p class='displaySucc'>$messageSuccess</p>";
        }
      ?>
      <p class='inboxButton'><input type='submit' class='button is-primary msgUser' value='Compose Message' name='msgUser'> </p>

      </div> <!-- end of message body-->

      </article>
      </form>

      <div class='columns'>
        <div class='column'>
          <article class='message is-link'>
            <div class='message-header'>
              Message Group
            </div>

            <div class='message-body'>
              <p> Select a group to message: </p>
              <form action='inbox.php' method='post'>
                <div class='control select'>
                  <select name='groupsToMessage'>

                    <?php
                    // select all groups registered to the coach logged in 
                    $selectGroupsForCoach = "SELECT id, group_name 
    FROM webdev_groups
    WHERE coach = $loggedInCoachId";
                    $executeSelectGroupsForCoach = $conn->query($selectGroupsForCoach);

                    $num = $executeSelectGroupsForCoach->num_rows;

                    if ($num == 0) {
                      $messageError = "(No one to message.)";
                    }



                    while ($row = $executeSelectGroupsForCoach->fetch_assoc()) {
                      $groupID = $row['id'];
                      $groupName = $row['group_name'];

                      echo "<option value='$groupID'>$groupName($groupID)</option>";
                    }

                    echo "
         </select>
         <input type='hidden' name='groupName' value='$groupName'>
         <input type='hidden' name='groupID' value='$groupID'>

</div>
";
                    if (isset($groupReplyError)) {
                      echo "<p class='displayError'>$groupReplyError</p>";
                    } else if (isset($groupReplySuccess)) {
                      echo "<p class='displaySucc'>$groupReplySuccess</p>";
                    }
                    ?>
                    <p class='inboxButton'><input type='submit' class='button is-primary msgUser' value='Compose Message' name='messageGroup'> </p>

                </div> <!-- end of message body-->

          </article>
          </form>
        </div>
      </div>

      <div class='columns'>
        <div class='column'>
          <article class='message is-warning'>
            <div class='message-header'>

              Read a message

            </div>

            <div class='message-body'>
              <form action='inbox.php' method='POST' id='readMessage'>
                <p> Select a message to read: <div class='select'>
                    <select name='userMessageToRead'>";
                      <?php
                      $getAllMessages = "SELECT webdev_inbox.id, webdev_inbox.subject, webdev_user_details.name 
    FROM webdev_inbox  
    INNER JOIN webdev_user_details
    ON webdev_inbox.sender = webdev_user_details.user_id
    WHERE webdev_inbox.recipient = '$loggedInCoachId' 
    ORDER BY webdev_inbox.id DESC";
                      $executeGetAllMessages = $conn->query($getAllMessages);

                      if (!$executeGetAllMessages) {
                        echo $conn->error;
                      }

                      while ($row = $executeGetAllMessages->fetch_assoc()) {
                        $messageID = $row['id'];
                        $messageSubject = $row['subject'];
                        $senderName = $row['name'];
                        $getCoachInfo = "SELECT * FROM webdev_coach where id = $sender";

                        $executeGetCoachInfo = $conn->query($getCoach);

                        if (!$executeGetCoachInfo) {
                          echo $conn->error;
                        }


                        echo "<option value='$messageID'>", htmlentities($messageSubject, ENT_QUOTES), "(from ", htmlentities($senderName, ENT_QUOTES), ")</option>";
                      }
                      ?>


                    </select>
                  </div>


                  <p class='inboxButton'><input type='submit' class='button is-warning readMessage' value='Read Message' name='readUserMessage'> </p>
              </form>
            </div> <!-- end of message body-->
          </article>
        </div>
      </div>

      </div><!-- end of column-->


      <div class='column is-7' id='rightColumns'>
        <article class='message is-dark'>
          <div class='message-header'>
            <p>
              <h1 class='title' id='inboxHead'>Unread Messages</h1>
            </p>

          </div>
          <div class='message-body'>


            <?php
            if (isset($replyError)) {
              echo "<p class='displayError'>$replyError</p>";
            } else if (isset($replySuccess)) {
              echo "<p class='displaySucc'>$replySuccess</p>";
            }

            $getRecentMessages = "SELECT webdev_inbox.id, webdev_inbox.subject, webdev_inbox.coach, webdev_user_details.name, webdev_inbox.message,
    webdev_inbox.attachment, webdev_inbox.user, webdev_inbox.group_id
    FROM webdev_inbox  
    INNER JOIN webdev_user_details
    ON webdev_inbox.sender = webdev_user_details.user_id
    WHERE webdev_inbox.recipient = '$loggedInCoachId' 
    AND webdev_inbox.hide = 0
    ORDER BY webdev_inbox.id DESC";
            $executeGetAllRecentMessages = $conn->query($getRecentMessages);
            if (!$executeGetAllRecentMessages) {
              echo $conn->error;
            }

            $numOfMessages = $executeGetAllRecentMessages->num_rows;
            $countRecentMessages = 0;
            if ($numOfMessages < 7) {
              $maxMessages = $numOfMessages;
            } else {
              $maxMessages = 7;
            }
            while ($countRecentMessages < $maxMessages) {

              $countRecentMessages++;
              $row = $executeGetAllRecentMessages->fetch_assoc();
              $messageID = $row['id'];
              $senderName = $row['name'];
              $subject = $row['subject'];
              $message = $row['message'];
              $user = $row['user'];
              $attachment = $row['attachment'];
              $groupID = $row['group_id'];


              echo "
  <article class='message is-dark'>
  <form action='inbox.php' method='POST'>
";

              if ($groupID != null) {
                echo "

<div class='level message-header'>
<!-- Left side -->
<div class='level-left' >
<div class='level-item'>",
                  htmlentities($subject, ENT_QUOTES),
                  "
</div>

</div>
";

                $getGroupName = $conn->prepare("SELECT group_name FROM webdev_groups
WHERE id = ?");
                $getGroupName->bind_param("i", $groupID);
                $getGroupName->execute();
                $getGroupName->store_result();
                $getGroupName->bind_result($groupName);
                $getGroupName->fetch();

                $getSenderName = $conn->prepare("SELECT sender, name FROM webdev_inbox
  INNER JOIN webdev_user_details 
  ON webdev_inbox.sender = webdev_user_details.user_id
  WHERE webdev_inbox.id = ? ");
                $getSenderName->bind_param("i", $messageID);
                $getSenderName->execute();
                $getSenderName->store_result();
                $getSenderName->bind_result($senderID, $senderName);
                $getSenderName->fetch();

                echo "
<!-- Right side -->
<div class='level-right '>
<div class='level-item'>";
                echo "From: $senderName ($groupName)
</div>
</div> 

</div>";
              } else {

                echo "
  <div class='level message-header'>
    <!-- Left side -->
    <div class='level-left' >
      <div class='level-item'>",
                  htmlentities($subject, ENT_QUOTES),
                  "
      </div>
      
    </div> 
  
    <!-- Right side -->
    <div class='level-right '>
    <div class='level-item'>
    From: ",
                  htmlentities($senderName, ENT_QUOTES),
                  "
</div>
    </div> 
    
  </div>";
              }

              echo "
  <div class='message-body'>
  <input type='hidden' name='groupName' value='$groupName'>
  <input type='hidden' name='groupID' value='$groupID'> 
  <input type='hidden' name='msgID' value='$messageID'>";


              echo "    <p> $message </p>  
    <nav class='level'>
    <!-- Left side -->
    <div class='level-left' >
     
      
   ";
              if ($attachment != null) {
                echo "<div class='level-item'>
                    
                    <a class='button is-warning recMsgButtons' name='downloadAttachment' href='images/attachments/$attachment' download='$attachment'>Download Attachment - $attachment</a>
                    </div>";
              }
              echo "
              
                  </div>";
              if ($groupID != null) {
                echo "
                    <!-- Right side -->
                    <div class='level-right field is-grouped recMsgButtons'>
                    <input type='submit' value='Reply to Group' class='button is-link replyBut' name='replyToGroup'></a>
                    <input type='submit' value='Mark as Read' class='button is-success' name='markAsRead'></a>";
              } else {
                echo "
                    <!-- Right side -->
                    <div class='level-right field is-grouped recMsgButtons'>
                    <input type='submit' value='Reply' class='button is-link replyBut' name='replyToUserMsg'></a>
                    <input type='submit' value='Mark as Read' class='button is-success' name='markAsRead'></a>";
              }


            ?>
              </span></h1></a>


          </div>
          </nav>



      </div>
      </form>
      </article>

    <?php
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
      }
  ?>



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