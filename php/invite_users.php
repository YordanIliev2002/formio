<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require 'utils/db_connection.php';
    $form_id = $_POST['form_id'];
    $faculty_nums = $_POST['faculty_nums'];
    $faculty_nums_array = array_map('trim', preg_split('/[\r\n,]+/', $faculty_nums));

    $failures = [];
    foreach ($faculty_nums_array as $faculty_num) {
        if (!empty($faculty_num)) {
            try {
                $stmt = $conn->prepare("INSERT INTO invites (form_id, faculty_number, did_submit) VALUES (?, ?, false)");
                $stmt->bind_param("ss", $form_id, $faculty_num);
                $stmt->execute();
                $stmt->close();
            } catch (Exception $e) {
                $failures[] = $faculty_num;
            }
        }
    }
    $error = "Failed to invite some faculty numbers: " . implode(", ", $failures);

    $conn->close();
}
?>

<!DOCTYPE HTML>
<html>

<head>
    <title>Invite Users</title>
    <link rel="stylesheet" href="/css/utils/common.css">
    <link rel="stylesheet" href="/css/invite_users_style.css">
</head>

<body>
    <section id="main">
        <form method="POST">
            <label for="faculty_nums">Add invites:</label>
            <textarea id="faculty_nums" name="faculty_nums" rows="5" cols="33" placeholder="Add the faculty numbers of those you want to invite, separated by commas..."></textarea>
            <input type="hidden" name="form_id" value="<?= htmlspecialchars($_GET["form_id"]) ?>">
            <?php if (isset($error)) : ?>
                <p id="error"><?= $error ?></p>
            <?php endif; ?>
            <input type="submit" value="Add users" class="primary-button">
        </form>
        <button type="button" onclick="location.href='index.php'" class="primary-button" >Return to Home</button>
    </section>
</body>

</html>