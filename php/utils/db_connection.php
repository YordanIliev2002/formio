<?php

$db_host = "127.0.0.1";
$db_user = "root";
$db_password = "formio-password";
$db_database = "formio";

$conn = new mysqli($db_host, $db_user, $db_password, $db_database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>