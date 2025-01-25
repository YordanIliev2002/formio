<?php
require "utils/constants.php";

$file = $UPLOAD_PATH . $_GET["id"];

// Prevents directory traversal attacks
$expectedDirectory = realpath($UPLOAD_PATH);
if (strpos(realpath($file), $expectedDirectory) !== 0) {
    echo "Invalid file path.";
    exit;
}

if (file_exists($file)) {
    header('Content-Disposition: attachment; filename=' . basename($file));
    readfile($file);
    exit;
} else {
    echo 'File not found.';
}
