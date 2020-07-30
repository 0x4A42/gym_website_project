<?php
//declare password
$pw = "";

//declare MySQL username
$user = "";

//declare webserver
$webserver = "";

//declare DB  
$db = "";

//mysqli api library in PHP to connect to the DB
$conn = new mysqli($webserver, $user, $pw, $db);

if ($conn->connect_error) {
    echo "Connection failed: " . $conn->connect_error;
}
