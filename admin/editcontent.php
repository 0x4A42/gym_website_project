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
 * If the page requested to edit has a small number of content items, based on value in database, displays this modal.
 * This will only have one row of data with a title and two pieces of content max, such as on the login page. 
 */
if (isset($_POST['editPage'])) {
    $pageToEdit = $_POST['pageID'];
    $pageName = $_POST['pageName'];
    $pageContentSize = $_POST['contentSize'];

    echo "<div class='modal is-active' id='addLog'>
    <div class='modal-background'></div>
    <div class='modal-card'>
      <header class='modal-card-head'>
        <p class='modal-card-title'>Editing content for $pageName</p>
        <button class='delete cancelUpdate' aria-label='close' ></button>
      
      </header>
     
      <section class='modal-card-body'>
     
        <form action='editcontent.php' method='POST' enctype='multipart/form-data' id='editPageContent'>
  ";
    /**
     * Get data and display modal depending on the ID of the page, 
     * as some pages have more content than others.
     */
    if (
        $pageToEdit == 2 || $pageToEdit == 4 ||
        $pageToEdit == 6 || $pageToEdit == 7
        || $pageToEdit == 8
    ) {

        $getContentOfPage = $conn->prepare("SELECT webdev_page_content.id, webdev_page_content.description, webdev_page_content.title, 
webdev_page_content.content, webdev_page_content.content_2, webdev_pages.page_name
FROM webdev_page_content
INNER JOIN webdev_pages
ON webdev_page_content.page_id = webdev_pages.id
WHERE webdev_page_content.page_id = ? ");
        $getContentOfPage->bind_param("i", $pageToEdit);
        $getContentOfPage->execute();
        $getContentOfPage->store_result();
        $getContentOfPage->bind_result($contentID, $contentDesc, $contentTitle, $contentOne, $contentTwo, $contentPageName);
        $getContentOfPage->fetch();

        echo "
         <div class='field'>
           <label class='label'>Description: $contentDesc</label>
          </div>
          <div class='field'>
          <div class='control'>
          Title:";
?>

        <input class='input' type='text' value="<?php echo $contentTitle ?>" name='contentTitle'>
        </div>
        </div>
        <?php
        echo "
        <input type='hidden' name='pageID' value='$pageToEdit'>
        <input type='hidden' name='pageName' value='$contentPageName'>
        <div class='field'>
            <div class='control'>
                Content:
                
          <textarea class='textarea' id='contentOne' name='contentOne'>";
        echo $contentOne ?></textarea>
        </div>
        </div>
        <?php

        if ($contentTwo != "") {

            echo "
          <div class='field'>
          <div class='control'>
          Content (cont):
          <textarea class='textarea' id='contentTwo' name='contentTwo'>";
            echo $contentTwo ?></textarea>
            </div>
            </div>
        <?php
        }
        /**
         * Get data and display modal depending on the ID of the page, 
         * as some pages have more content than others.
         */
    } else if ($pageToEdit == 3) {
        $getTextOfPage = "SELECT webdev_page_content.id, webdev_page_content.description, webdev_page_content.title, 
        webdev_page_content.content, webdev_page_content.content_2, webdev_pages.page_name
      FROM webdev_page_content
      INNER JOIN webdev_pages
      ON webdev_page_content.page_id = webdev_pages.id
      WHERE webdev_page_content.page_id = $pageToEdit
      ";
        $executeGetTextOfPage = $conn->query($getTextOfPage);
        if (!$executeGetTextOfPage) {
            echo $conn->error;
        }
        while ($row = $executeGetTextOfPage->fetch_assoc()) {
            $contentTitleCount = "contentTitle";
            $contentTitleIdentifier = "title";
            $contentBodyIdentifier = "body";
            $contentBodyIdentifier2 = "secondBody";
            $contentID = $row['id'];
            $contentDesc = $row['description'];
            $contentTitle = $row['title'];
            $contentOne = $row['content'];
            $contentTwo = $row['content_2'];
            $pageName = $row['page_name'];
            $contentTitleIdentifier .= $contentID;
            $contentBodyIdentifier .= $contentID;
            $contentBodyIdentifier2 .= $contentID;

            echo "
    <div class='field'>
      <label class='label'>Description: $contentDesc</label>
     </div>
     <div class='field'>
     <div class='control'>
     Coach Name:";
        ?>

            <input class='input' type='text' value="<?php echo $contentTitle ?>" name="<?php echo $contentTitleIdentifier ?>">
            </div>
            <?php
            echo "
            </div>
            <input type='hidden' name='pageID' value='$pageToEdit'>
            <input type='hidden' name='pageName' value='$pageName'>
            <div class='field'>
                <div class='control'>
                   
                    Content:
                    <textarea class='textarea' id='contentOne' name='$contentBodyIdentifier'>";
            echo $contentOne ?></textarea>
            </div>

            </div>
            <?php
            echo "
            <div class='field'>
                <div class='control'>
                    Content 2:
                    <textarea class='textarea' id='contentOne' name='$contentBodyIdentifier2'>";
            echo $contentTwo ?></textarea>
            </div>

            </div>

        <?php
        }
        /**
         * Get data and display modal depending on the ID of the page,
         * as some pages have more content than others.
         */
    } else if ($pageToEdit == 1) {
        $getTextOfPage = "SELECT webdev_page_content.id, webdev_page_content.description, webdev_page_content.title,
            webdev_page_content.content, webdev_pages.page_name
            FROM webdev_page_content
            INNER JOIN webdev_pages
            ON webdev_page_content.page_id = webdev_pages.id
            WHERE webdev_page_content.page_id = $pageToEdit
            ";
        $executeGetTextOfPage = $conn->query($getTextOfPage);
        if (!$executeGetTextOfPage) {
            echo $conn->error;
        }


        while ($row = $executeGetTextOfPage->fetch_assoc()) {
            $contentTitleIdentifier = "title";
            $contentBodyIdentifier = "body";
            $contentID = $row['id'];
            $contentDesc = $row['description'];
            $contentTitle = $row['title'];
            $contentOne = $row['content'];
            $pageName = $row['page_name'];
            $contentTitleIdentifier .= $contentID;
            $contentBodyIdentifier .= $contentID;


            echo "
            <div class='field'>
                <label class='label'>Description: $contentDesc</label>
            </div>
            <div class='field'>
                <div class='control'>
                    Title:
                    ";
        ?> <input class='input' type='text' value="<?php echo $contentTitle ?>" name="<?php echo $contentTitleIdentifier ?>">
            </div>
            </div>
            <?php
            echo "
            <input type='hidden' name='pageID' value='$pageToEdit'>
            <input type='hidden' name='pageName' value='$pageName'>
            <div class='field'>
                <div class='control'>
                    
         Content:
         <textarea class='textarea' id='contentOne' name='$contentBodyIdentifier'>";
            echo $contentOne ?></textarea>
            </div>
            </div>
<?php
        }
    }
    echo "
    <footer class='modal-card-foot'>
    <input type='submit' class='button is-success userMessageCoachButton' id='coachMsgSubmit' value='Confirm Changes' name='editPageSubmit'>
      </footer>
  
        </form>
      </section>
      </div>
      </div>
      </div>";
}

/**
 * Attempts to validate the changes made to the content of the page.
 * If valid, updates DB. 
 */
if (isset($_POST['editPageSubmit'])) {

    /**
     * Different methods of validation depending on the page that is being edited, 
     * as pages have different content amounts and therefore a need for differing amounts of
     * fetching from the db.
     */
    if ($_POST['pageID'] == 1) {

        //Store posted data in variables, sanitised for possible database entry.
        $pageID = $_POST['pageID'];
        $pageName = $_POST['pageName'];


        $imageTitle = $_POST['title5'];
        $sanitisedImageTitle = $conn->real_escape_string(trim($imageTitle));
        $imageContent = $_POST['body5'];
        $sanitisedImageContent = $conn->real_escape_string(trim($imageContent));


        $weightTitle = $_POST['title6'];
        $sanitisedWeightTitle = $conn->real_escape_string(trim($weightTitle));
        $weightBody = $_POST['body6'];
        $sanitisedWeightBody = $conn->real_escape_string(trim($weightBody));


        $nutritionTitle = $_POST['title7'];
        $sanitisedNutritionTitle = $conn->real_escape_string(trim($nutritionTitle));
        $nutritionBody = $_POST['body7'];
        $sanitisedNutritionBody = $conn->real_escape_string(trim($nutritionBody));



        $wellBeingTitle = $_POST['title8'];
        $sanitisedWellBeingTitle = $conn->real_escape_string(trim($wellBeingTitle));
        $wellBeingBody = $_POST['body8'];
        $sanitisedWellBeingBody = $conn->real_escape_string(trim($wellBeingBody));



        $bodyBuidlingTitle = $_POST['title9'];
        $sanitisedBodyBuidlingTitle = $conn->real_escape_string(trim($bodyBuidlingTitle));
        $bodyBuidlingBody = $_POST['body9'];
        $sanitisedBodyBuidlingBody = $conn->real_escape_string(trim($bodyBuidlingBody));


        /**
         * Gets the current data stored in the database to compare 'updated' values with.
         * If all are the same, nothing has been changed and the update query is not sent.
         */
        $idForImage = 5;
        $getCurrentImageContent = $conn->prepare("SELECT title, content FROM webdev_page_content WHERE id = ?");
        $getCurrentImageContent->bind_param("i",  $idForImage);
        $getCurrentImageContent->execute();
        $getCurrentImageContent->store_result();
        $getCurrentImageContent->bind_result($currentImageTitle, $currentImageContent);
        $getCurrentImageContent->fetch();

        $idForWeight = 6;
        $getCurrentWeightContent = $conn->prepare("SELECT title, content FROM webdev_page_content WHERE id = ?");
        $getCurrentWeightContent->bind_param("i", $idForWeight);
        $getCurrentWeightContent->execute();
        $getCurrentWeightContent->store_result();
        $getCurrentWeightContent->bind_result($currentWeightTitle, $currentWeightContent);
        $getCurrentWeightContent->fetch();

        $idForNutri = 7;
        $getCurrentNutritionContent = $conn->prepare("SELECT title, content FROM webdev_page_content WHERE id = ?");
        $getCurrentNutritionContent->bind_param("i", $idForNutri);
        $getCurrentNutritionContent->execute();
        $getCurrentNutritionContent->store_result();
        $getCurrentNutritionContent->bind_result($currentNutritionTitle, $currentNutritionContent);
        $getCurrentNutritionContent->fetch();

        $idForWellbeing = 8;
        $getCurrentWellbeingContent = $conn->prepare("SELECT title, content FROM webdev_page_content WHERE id = ?");
        $getCurrentWellbeingContent->bind_param("i", $idForWellbeing);
        $getCurrentWellbeingContent->execute();
        $getCurrentWellbeingContent->store_result();
        $getCurrentWellbeingContent->bind_result($currentWellbeingTitle, $currentWellbeingContent);
        $getCurrentWellbeingContent->fetch();

        $idForBodyBuilding = 9;
        $getCurrentBodyBuildingContent = $conn->prepare("SELECT title, content FROM webdev_page_content WHERE id = ?");
        $getCurrentBodyBuildingContent->bind_param("i", $idForBodyBuilding);
        $getCurrentBodyBuildingContent->execute();
        $getCurrentBodyBuildingContent->store_result();
        $getCurrentBodyBuildingContent->bind_result($currentBodyBuildingTitle, $currentBodyBuildingContent);
        $getCurrentBodyBuildingContent->fetch();


        /**
         * Compares current values with the values that have been posted.
         * If the same, no changes made, displays error and no query is sent.
         * Else, an update query is sent. 
         */
        if (($imageTitle == $currentImageTitle) && ($imageContent == $currentImageContent)
            && ($weightTitle == $currentWeightTitle) && ($weightBody == $currentWeightContent)
            && ($nutritionTitle == $currentNutritionTitle) && ($nutritionBody == $currentNutritionContent)
            && ($wellBeingTitle == $currentWellbeingTitle) && ($wellBeingBody == $currentWellbeingContent)
            && ($bodyBuidlingTitle == $currentBodyBuildingTitle) && ($bodyBuidlingBody == $currentBodyBuildingContent)
        ) {
            $updateContentError = "Content of $pageName has not been updated - no fields have been changed.";
        } else {
            // transaction adapted from online tutorial https://www.youtube.com/watch?v=CNt9HPqDIVc
            $conn->autocommit(false);

            $error = array();

            /**
             * Checks if these specific fields have been changed, if so - sends query to update this section.
             * If not, no query is sent  - attempt to save resources, reduce runtime if not necessary.
             */
            if (($imageTitle  != $currentImageTitle) || ($imageContent  != $currentImageContent)) {

                $updateImageText = "UPDATE webdev_page_content SET webdev_page_content.title = '$sanitisedImageTitle', 
            webdev_page_content.content = '$sanitisedImageContent'
            WHERE webdev_page_content.id = '5'";
                $a = $conn->query($updateImageText);
                if ($a == false) {
                    array_push($error, 'Problem updating image content for index!');
                }
            }

            /**
             * Checks if these specific fields have been changed, if so - sends query to update this section.
             * If not, no query is sent  - attempt to save resources, reduce runtime if not necessary.
             */
            if (($weightTitle  != $currentWeightTitle) || ($weightBody  != $currentWeightContent)) {
                $updateWeightLoss = "UPDATE webdev_page_content SET webdev_page_content.title = '$sanitisedWeightTitle', 
                webdev_page_content.content = '$sanitisedWeightBody'
                WHERE webdev_page_content.id = '6'";

                $b = $conn->query($updateWeightLoss);
                if ($b == false) {
                    array_push($error, 'Problem updating first article for index!');
                }
            }

            /**
             * Checks if these specific fields have been changed, if so - sends query to update this section.
             * If not, no query is sent  - attempt to save resources, reduce runtime if not necessary.
             */
            if (($nutritionTitle  != $currentNutritionTitle) || ($nutritionBody != $currentNutritionContent)) {
                $updateNutirion = "UPDATE webdev_page_content SET webdev_page_content.title = '$sanitisedNutritionTitle', 
                webdev_page_content.content = '$sanitisedNutritionBody'
                WHERE webdev_page_content.id = '7'";

                $c = $conn->query($updateNutirion);
                if ($c == false) {
                    array_push($error, 'Problem updating second article for index!');
                }
            }

            /**
             * Checks if these specific fields have been changed, if so - sends query to update this section.
             * If not, no query is sent  - attempt to save resources, reduce runtime if not necessary.
             */
            if (($wellBeingTitle != $currentWellbeingTitle) || ($wellBeingBody != $currentWellbeingContent)) {
                $updateWellBeing = "UPDATE webdev_page_content SET webdev_page_content.title = '$sanitisedWellBeingTitle', 
                webdev_page_content.content = '$sanitisedWellBeingBody'
                WHERE webdev_page_content.id = '8'";

                $d = $conn->query($updateWellBeing);
                if ($d == false) {
                    array_push($error, 'Problem updating third article for index!');
                }
            }

            /**
             * Checks if these specific fields have been changed, if so - sends query to update this section.
             * If not, no query is sent  - attempt to save resources, reduce runtime if not necessary.
             */
            if (($bodyBuidlingTitle   != $currentBodyBuildingTitle) || ($bodyBuidlingBody   != $currentBodyBuildingContent)) {
                $updateBodyBuilding = "UPDATE webdev_page_content SET webdev_page_content.title = '$sanitisedBodyBuidlingTitle', 
                webdev_page_content.content = '$sanitisedBodyBuidlingBody'
                WHERE webdev_page_content.id = '9'";

                $e = $conn->query($updateBodyBuilding);
                if ($e == false) {
                    array_push($error, 'Problem updating fourth article for index!');
                }
            }

            /**
             * If error array is not empty, one of the queries in the transaction 
             * has failed and it is rolled back. Else, commits the transaction.
             */
            if (!empty($error)) {
                $conn->rollback();
                $updateContentError = "Could not update content for $pageName - please try again.";
            } else {
                //commit
                $conn->commit();
                $updateContentSuccess = "Website content for $pageName successfully updated.";
            }
        }
        /**
         * Different methods of validation depending on the page that is being edited, 
         * as pages have different content amounts and therefore a need for differing amounts of
         * fetching from the db.
         */
    } else if (($_POST['pageID'] == 2) || ($_POST['pageID'] == 4) ||
        ($_POST['pageID'] == 6) || ($_POST['pageID'] == 7) ||
        ($_POST['pageID'] == 8)
    ) {

        //Store posted data in variables, sanitised for possible database entry.

        $pageName = $_POST['pageName'];
        $contentTitle = $_POST['contentTitle'];
        $sanitisedContentTitle = $conn->real_escape_string(trim($contentTitle));
        $contentOne = $_POST['contentOne'];
        $sanitisedContentOne = $conn->real_escape_string(trim($contentOne));
        $pageID = $_POST['pageID'];

        $contentTwo = $_POST['contentTwo'];
        $sanitisedContentTwo = $conn->real_escape_string(trim($contentTwo));

        /**
         * Gets the current data stored in the database to compare 'updated' values with.
         * If all are the same, nothing has been changed and the update query is not sent.
         */

        $getCurrentContent = $conn->prepare("SELECT title, content, content_2 FROM webdev_page_content WHERE page_id = ?");
        $getCurrentContent->bind_param("i",  $pageID);
        $getCurrentContent->execute();
        $getCurrentContent->store_result();
        $getCurrentContent->bind_result($currentTitle, $currentContentOne, $currentContentTwo);
        $getCurrentContent->fetch();

        /**
         * Compares current values with the values that have been posted.
         * If the same, no changes made, displays error and no query is sent.
         * Else, an update query is sent. 
         */

        if (($contentTitle == $currentTitle) && ($contentOne == $currentContentOne)
            && ($contentTwo == $currentContentTwo)
        ) {
            $updateContentError = "Content of $pageName has not been updated - no fields have been changed.";
        } else {

            $updateContent = "UPDATE webdev_page_content SET webdev_page_content.title = '$sanitisedContentTitle', 
        webdev_page_content.content = '$sanitisedContentOne', webdev_page_content.content_2 = '$sanitisedContentTwo'
        WHERE webdev_page_content.page_id = '$pageID'";
            $executeUpdateContent = $conn->query($updateContent);
            if (!$executeUpdateContent) {
                echo $conn->error;
                $updateContentError = "Could not update content for $pageName - please try again.";
            } else {
                $updateContentSuccess = "Website content for $pageName successfully updated.";
            }
        }
        /**
         * Different methods of validation depending on the page that is being edited, 
         * as pages have different content amounts and therefore a need for differing amounts of
         * fetching from the db.
         */
    } else if ($_POST['pageID'] == 3) {

        //Store posted data in variables, sanitised for possible database entry.
        $pageName = $_POST['pageName'];
        $pageID = $_POST['pageID'];

        $firstCoachTitle = $_POST['title10'];
        $sanitisedFirstCoachTitle = $conn->real_escape_string(trim($firstCoachTitle));
        $firstCoachContent = $_POST['body10'];
        $sanitisedFirstCoachContent = $conn->real_escape_string(trim($firstCoachContent));
        $firstCoachContentTwo = $_POST['secondBody10'];
        $sanitisedFirstCoachContentTwo = $conn->real_escape_string(trim($firstCoachContentTwo));

        $secondCoachTitle = $_POST['title11'];
        $sanitisedSecondCoachTitle = $conn->real_escape_string(trim($secondCoachTitle));
        $secondCoachContent = $_POST['body11'];
        $sanitisedSecondCoachContent = $conn->real_escape_string(trim($secondCoachContent));
        $secondCoachContentTwo = $_POST['secondBody11'];
        $sanitisedSecondCoachContentTwo = $conn->real_escape_string(trim($secondCoachContentTwo));



        $thirdCoachTitle = $_POST['title12'];
        $sanitisedThirdCoachTitle = $conn->real_escape_string(trim($thirdCoachTitle));
        $thirdCoachContent = $_POST['body12'];
        $sanitisedThirdCoachContent = $conn->real_escape_string(trim($thirdCoachContent));
        $thirdCoachContentTwo = $_POST['secondBody12'];
        $sanitisedThirdCoachContentTwo = $conn->real_escape_string(trim($thirdCoachContentTwo));

        /**
         * Gets the current data stored in the database to compare 'updated' values with.
         * If all are the same, nothing has been changed and the update query is not sent.
         */

        $contentIDOfCoachOne = 10;
        $getCurrentContentCoachOne = $conn->prepare("SELECT title, content, content_2 FROM webdev_page_content WHERE id = ?");
        $getCurrentContentCoachOne->bind_param("i",  $contentIDOfCoachOne);
        $getCurrentContentCoachOne->execute();
        $getCurrentContentCoachOne->store_result();
        $getCurrentContentCoachOne->bind_result($currentC1Title, $currentC1ContentOne, $currentC1ContentTwo);
        $getCurrentContentCoachOne->fetch();


        $contentIDOfCoachTwo = 11;
        $getCurrentContentCoachTwo = $conn->prepare("SELECT title, content, content_2 FROM webdev_page_content WHERE id = ?");
        $getCurrentContentCoachTwo->bind_param("i",  $contentIDOfCoachTwo);
        $getCurrentContentCoachTwo->execute();
        $getCurrentContentCoachTwo->store_result();
        $getCurrentContentCoachTwo->bind_result($currentC2Title, $currentC2ContentOne, $currentC2ContentTwo);
        $getCurrentContentCoachTwo->fetch();

        $contentIDOfCoachThree = 12;
        $getCurrentContentCoachThree = $conn->prepare("SELECT title, content, content_2 FROM webdev_page_content WHERE id = ?");
        $getCurrentContentCoachThree->bind_param("i",  $contentIDOfCoachThree);
        $getCurrentContentCoachThree->execute();
        $getCurrentContentCoachThree->store_result();
        $getCurrentContentCoachThree->bind_result($currentC3Title, $currentC3ContentOne, $currentC3ContentTwo);
        $getCurrentContentCoachThree->fetch();



        /**
         * Compares current values with the values that have been posted.
         * If the same, no changes made, displays error and no query is sent.
         * Else, an update query is sent. 
         */

        if (($firstCoachTitle == $currentC1Title) && ($firstCoachContent == $currentC1ContentOne) &&
            ($firstCoachContentTwo == $currentC1ContentTwo) && ($secondCoachTitle == $currentC2Title) &&
            ($secondCoachContent == $currentC2ContentOne) && ($secondCoachContentTwo == $currentC2ContentTwo) &&
            ($thirdCoachTitle == $currentC3Title) && ($thirdCoachContent == $currentC3ContentOne) &&
            ($thirdCoachContentTwo == $currentC3ContentTwo)
        ) {
            $updateContentError = "Content of $pageName has not been updated - no fields have been changed.";
        } else {

            // transaction adapted from online tutorial https://www.youtube.com/watch?v=CNt9HPqDIVc
            $conn->autocommit(false);
            $updateCoachError = array();

            /**
             * Checks if these specific fields have been changed, if so - sends query to update this section.
             * If not, no query is sent  - attempt to save resources, reduce runtime if not necessary.
             */
            if (($firstCoachTitle != $currentC1Title) || ($firstCoachContent != $currentC1ContentOne) ||
                ($firstCoachContentTwo != $currentC1ContentTwo)
            ) {
                $updateFirstCoach = "UPDATE webdev_page_content SET webdev_page_content.title = '$sanitisedFirstCoachTitle', 
                webdev_page_content.content = '$sanitisedFirstCoachContent', webdev_page_content.content_2 = '$sanitisedFirstCoachContentTwo'
                WHERE webdev_page_content.id = '$contentIDOfCoachOne'";
                $coachA = $conn->query($updateFirstCoach);
                if ($coachA == false) {
                    array_push($updateCoachError, 'Problem updating first coach.');
                }
            }

            /**
             * Checks if these specific fields have been changed, if so - sends query to update this section.
             * If not, no query is sent  - attempt to save resources, reduce runtime if not necessary.
             */
            if (($secondCoachTitle != $currentC2Title) || ($secondCoachContent != $currentC2ContentOne) ||
                ($secondCoachContentTwo != $currentC2ContentTwo)
            ) {

                $updateSecondCoach = "UPDATE webdev_page_content SET webdev_page_content.title = '$sanitisedSecondCoachTitle', 
                webdev_page_content.content = '$sanitisedSecondCoachContent', webdev_page_content.content_2 = '$sanitisedSecondCoachContentTwo'
                WHERE webdev_page_content.id = '$contentIDOfCoachTwo'";

                $coachB = $conn->query($updateSecondCoach);
                if ($coachB == false) {
                    array_push($updateCoachError, 'Problem updating second coach.');
                }
            }

            /**
             * Checks if these specific fields have been changed, if so - sends query to update this section.
             * If not, no query is sent  - attempt to save resources, reduce runtime if not necessary.
             */
            if (($thirdCoachTitle != $currentC3Title) || ($thirdCoachContent != $currentC3ContentOne) ||
                ($thirdCoachContentTwo != $currentC3ContentTwo)
            ) {

                $updateThirdCoach = "UPDATE webdev_page_content SET webdev_page_content.title = '$sanitisedThirdCoachTitle', 
                webdev_page_content.content = '$sanitisedThirdCoachContent', webdev_page_content.content_2 = '$sanitisedThirdCoachContentTwo'
                WHERE webdev_page_content.id = '$contentIDOfCoachThree'";

                $coachC = $conn->query($updateThirdCoach);
                if ($coachC == false) {
                    array_push($updateCoachError, 'Problem updating third coach.');
                }
            }
            /**
             * If error array is not empty, one of the queries in the transaction 
             * has failed and it is rolled back. Else, commits the transaction.
             */
            if (!empty($updateCoachError)) {

                $conn->rollback();
                $updateContentError = "Could not update content for $pageName - please try again.";
            } else {

                $conn->commit();
                $updateContentSuccess = "Website content for $pageName successfully updated.";
            }
        }
    }
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gymafi | Edit Site </title>
    <link href="../styles/bulma.css" rel="stylesheet">
    <link href="../styles/lightbox.css" rel="stylesheet">
    <link href="../styles/gui.css" rel="stylesheet">
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
    <script src="../script/myScript.js"></script>
    <script src="../script/lightbox.js"></script>

</head>

<!-- display log out button-->

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

    <!-- Navigation bar -->
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


                <a class='navbar-item has-text-black ' href='groups.php'>
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

                        <a class='navbar-item has-text-black  has-background-warning' href='editcontent.php'>
                            Edit Site Content
                        </a>
                    </div>



                </div> <!-- end of navbar dropdown-->
            </div> <!-- end of nav-bar item-->
        </div> <!-- end of navbarBasicExample-->


        </div>
    </nav>

    <!-- Displays a list of all of the pages the coach can edit the content of -->
    <div id='dashColumns'>

        <div class='column is-9' id='editContentColumn'>
            <article class='message is-dark'>
                <div class='message-header'>
                    <p>
                        <h1 class='title titleHeader'>Content Editor</h1>
                    </p>

                </div>
                <div class='message-body'>
                    Please select a page to edit the content of.
                    <?php

                    if (isset($updateContentError)) {
                        echo "<p class='displayError'> $updateContentError</p>";
                    } else if (isset($updateContentSuccess)) {
                        echo "<p class='displaySucc'> $updateContentSuccess</p>";
                    }

                    $getAllPages = "SELECT * FROM webdev_pages";
                    $executeGetAllPages = $conn->query($getAllPages);

                    if (!$executeGetAllPages) {
                        echo $conn->error;
                    }
                    echo "<div class='columns'>";
                    /*
                    * variable to track pages edit articles printed out,
                    *  when gets to 2 ends current columns row and starts new one
                    */
                    $pageCounter = 0;
                    while ($row = $executeGetAllPages->fetch_assoc()) {
                        $pageID = $row['id'];
                        $pageName = $row['page_name'];
                        $pageContentSize = $row['content_size'];


                        echo "<div class='column'> 
                        <article class='message is-dark'>
                        <form action='editcontent.php' method='POST'>
                        <div class='message-header'>
                            <p>
                            ", htmlentities($pageName, ENT_QUOTES), "
                            </p>
        
                        </div>";
                        /**
                         * Hidden input values to send when form is posted
                         */
                        echo "
                        <div class='message-body'>
                        <input type='hidden' name='pageID' value='$pageID'>
                        <input type='hidden' name='pageName' value='$pageName'>
                        <input type='hidden' name='contentSize' value='$pageContentSize'>
                        </div>
                       

                        <input type='submit' value='Edit ", htmlentities($pageName, ENT_QUOTES), "' class='button is-success' name='editPage'></a>
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

        <!--Page footer-->
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