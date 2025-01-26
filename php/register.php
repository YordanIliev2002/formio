<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'utils/db_connection.php';
    // Retrieve form data
    $facultyNumber = $_POST["faculty_number"];
    $user_name = $_POST["user_name"];
    $password = $_POST["password"];
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (faculty_number, user_name, password_hash) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $facultyNumber, $user_name, $password_hash);
    try {
        $stmt->execute();
        session_start();
        $_SESSION["user_faculty_number"] = $facultyNumber;
        header("Location: index.php");
    } catch (Exception $e) {
        header("Location: register.php?error=1&faculty_number=$facultyNumber&user_name=$user_name");
    }
    $stmt->close();
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="/css/register_style.css">
</head>
<body>
    <section id="box">
    <h2 id = "register">Register</h2>
    <section id="form">
    <form method="POST" action="register.php">
        <label for="faculty_number">Faculty Number:</label>
        <input type="text" name="faculty_number" id="faculty_number" required value="<?= htmlspecialchars($_GET['faculty_number'] ?? '') ?>">

        <label for="user_name">Name:</label>
        <input type="text" name="user_name" id="user_name" required value="<?= htmlspecialchars($_GET['user_name'] ?? '') ?>">
        
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>
        
        <?php if (isset($_GET["error"])): ?>
          <p style="color: red;">Error occurred during reistration. Please try again.</p>
        <?php endif ?>
        <input type="submit" value="Register">
    </form>
    </section>
    </section>
</body>
</html>