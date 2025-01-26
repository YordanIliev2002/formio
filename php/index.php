<?php
session_start();
if (isset($_SESSION["user_faculty_number"])) {
    // fetch the user's forms
    require 'utils/db_connection.php';
    $stmt = $conn->prepare("SELECT id, JSON_UNQUOTE(JSON_EXTRACT(form_definition, '$.title')) AS title, (SELECT count(*) from responses r where r.form_id = f.id) AS 'response_count' FROM forms f WHERE author_fn = ?");
    $stmt->bind_param("s", $_SESSION["user_faculty_number"]);
    $stmt->execute();
    $user_forms = $stmt->get_result();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Formio</title>
    <link rel="stylesheet" href="/css/index_style.css">
</head>

<body>
    <?php if (isset($_SESSION["user_faculty_number"])): ?>

        <section id="logged_buttons">
            <button onclick="location.href='create_form.php'" id="button_logged">Create a new form</button>
            <button onclick="location.href='logout.php'" id="button_logged">Logout</button>
        </section>
        <section id="logged_your_forms">
            <p id="hello">Hello, <?= htmlspecialchars($_SESSION["user_faculty_number"]) ?></p>
            <h2>Your forms</h2>
            <ul>
                <?php while ($row = $user_forms->fetch_assoc()): ?>
                    <?php
                    $form_url = "http://localhost:8000/form.php?id=" . urlencode($row["id"]);
                    ?>
                    <li>
                        <a href="form.php?id=<?= $row["id"] ?>"><?= htmlspecialchars($row["title"]) ?></a>
                        <a href="statistics.php?form_id=<?= $row["id"] ?>" id="stat_link">Statistics</a>
                        <button onclick="copyToClipboard('<?= $form_url ?>')" id="button_url">Copy Url to Clipboard</button>
                        <a href="invite_users.php?form_id=<?= $row["id"] ?>" id = "invite_users">Invite Users</a>
                        <p id="filled">(Filled in <?= $row["response_count"] ?> times)</p>
                    </li>
                <?php endwhile; ?>
            </ul>
        </section>
    <?php else: ?>
        <h2 id="welcome">Welcome to Formio!</h2>
        <section id="not_logged">
            <p>You are not logged in.</p>
            <section id="buttons">
                <button onclick="location.href='login.php'" id="button_not_logged">Login</button>
                <button onclick="location.href='register.php'" id="button_not_logged">Register</button>
            </section>
        </section>
    <?php endif; ?>


    <script>
        function copyToClipboard(url) {
            var textarea = document.createElement('textarea');
            textarea.value = url;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            alert("URL copied to clipboard: " + url);
        }
    </script>
</body>

</html>