<?php
session_start();
if (isset($_SESSION["user_faculty_number"])) {
    // fetch the user's forms
    require 'utils/db_connection.php';
    $stmt = $conn->prepare("SELECT id, form_definition->>'$.title' as 'title' FROM forms WHERE author_fn = ?");
    $stmt->bind_param("s", $_SESSION["user_faculty_number"]);
    $stmt->execute();
    $user_forms = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Formio</title>
</head>
<body>
    <?php if (isset($_SESSION["user_faculty_number"])): ?>
        <section>
            <p>Hello, <?= htmlspecialchars($_SESSION["user_faculty_number"]) ?></p>
            <button onclick="location.href='create_form.php'">Create a new form</button>
            <button onclick="location.href='logout.php'">Logout</button>
        </section>
        <section>
            <h2>Your forms</h2>
            <ul>
                <?php while ($row = $user_forms->fetch_assoc()): ?>
                    <li>
                        <a href="form.php?id=<?= $row["id"] ?>"><?= htmlspecialchars($row["title"]) ?></a>
                    </li>
                <?php endwhile; ?>
            </ul>
        </section>
    <?php else: ?>
        <p>You are not logged in.</p>
        <button onclick="location.href='login.php'">Login</button>
        <button onclick="location.href='register.php'">Register</button>
    <?php endif; ?>
</body>
</html>
