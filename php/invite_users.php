<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require 'utils/db_connection.php';
    $form_id = $_POST['form_id'];
    $faculty_nums = $_POST['faculty_nums'];
    $faculty_nums_array = array_map('trim', preg_split('/[\r\n,]+/', $faculty_nums));

    foreach ($faculty_nums_array as $faculty_num) {
        if (!empty($faculty_num)) {
            $stmt = $conn->prepare("INSERT INTO invites (form_id, faculty_number, did_submit) VALUES (?, ?, 0)");
            $stmt->bind_param("ss", $form_id, $faculty_num);
            $stmt->execute();
            $stmt->close();
        }
    }

    $conn->close();
}
?>

<!DOCTYPE HTML>
<html>

<head>
    <title>Invite Users</title>
    <link rel="stylesheet" href="/css/invite_users_style.css">
</head>

<body>
    <section id="box">
        <form method="POST">
            <label for="faculty_nums">Add invites:</label>
            <textarea id="faculty_nums" name="faculty_nums" rows="5" cols="33" placeholder="Add the faculty numbers of those you want to invite, separated by commas..."></textarea>
            <input type="hidden" name="form_id" value="<?= htmlspecialchars($_GET["form_id"]) ?>">
            <input type="submit" value="Add users">
        </form>
        <a href="index.php" id="return_home">Return to Home</a>
    </section>
</body>

</html>