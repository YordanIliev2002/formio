<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'utils/db_connection.php';
    // Retrieve form data
    $facultyNumber = $_POST["faculty_number"];
    $user_name = $_POST["user_name"];
    $password = $_POST["password"];
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    $mail = $_POST["mail"];

    $stmt = $conn->prepare("INSERT INTO users (faculty_number, user_name, password_hash, mail) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $facultyNumber, $user_name, $password_hash, $mail);
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
    <link rel="stylesheet" href="css/utils/common.css">
    <link rel="stylesheet" href="css/register_style.css">
</head>

<body>
    <section id="main">
        <h2 id="register">Register</h2>
        <section id="form">
            <form method="POST" action="register.php">
                <label for="faculty_number">Faculty Number:</label>
                <input type="text" name="faculty_number" id="faculty_number" required value="<?= htmlspecialchars($_GET['faculty_number'] ?? '') ?>">

                <label for="mail">Mail:</label>
                <input type="email" name="mail" id="mail" required value="<?= htmlspecialchars($_GET['mail'] ?? '') ?>">

                <label for="user_name">Name:</label>
                <input type="text" name="user_name" id="user_name" required value="<?= htmlspecialchars($_GET['user_name'] ?? '') ?>">

                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>

                <?php if (isset($_GET["error"])): ?>
                    <p style="color: red;">Error occurred during registration. Please try again.</p>
                <?php endif ?>
                <section id="buttons">
                    <input type="submit" value="Register">
            </form>
            <button type="button" onclick="location.href='index.php'" class="primary-button">Back</button>
        </section>
    </section>
    </section>
</body>

</html>