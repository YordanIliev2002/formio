<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'utils/db_connection.php';
    $facultyNumber = $_POST["faculty_number"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM users WHERE faculty_number = ?");
    $stmt->bind_param("s", $facultyNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
        header("Location: login.php?error=1&faculty_number=$facultyNumber");
        exit;
    }
    $stmt->close();

    if (password_verify($password, $result->fetch_assoc()['password_hash'])) {
        session_start();
        $_SESSION["user_faculty_number"] = $facultyNumber;
        header("Location: index.php");
    } else {
        header("Location: login.php?error=1&faculty_number=$facultyNumber");
    }
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <link rel="stylesheet" href="css/utils/common.css">
    <link rel="stylesheet" href="css/login_style.css">
</head>

<body>
    <section id="main">
        <h2 id="login">L♡gin</h2>
        <section id="form">
            <form method="POST" action="login.php">
                <label for="faculty_number">Faculty Number:</label>
                <input type="text" name="faculty_number" id="faculty_number" required value="<?= htmlspecialchars($_GET['faculty_number'] ?? '') ?>">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>

                <?php if (isset($_GET["error"])): ?>
                    <p style="color: red;">Error occurred during login. Please try again.</p>
                <?php endif ?>

                <input type="submit" value="Login">

            </form>
        </section>
    </section>
</body>

</html>