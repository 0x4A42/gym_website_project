<?php
// unsets all sessions to log the user out so they can only access the public pages.
session_start();
unset($_SESSION['gymafi_userid']);
unset($_SESSION['gymafi_coachid']);
unset($_SESSION['gymafi_superadmin']);
header('location:index.php');
