<?php
session_start();

if (!isset($_SESSION['user_faculty_number'])) {
    header("Location: index.php");
    exit;
}
?>