<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tms";

// Create connection
$db = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

?>