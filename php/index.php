<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Formio</title>
</head>
<body>
    <?php if (isset($_SESSION["user_faculty_number"])): ?>
        <p>Hello, <?= htmlspecialchars($_SESSION["user_faculty_number"]) ?></p>
        <button onclick="location.href='logout.php'">Logout</button>
    <?php else: ?>
        <p>You are not logged in.</p>
        <button onclick="location.href='login.php'">Login</button>
        <button onclick="location.href='register.php'">Register</button>
    <?php endif; ?>
</body>
</html>
