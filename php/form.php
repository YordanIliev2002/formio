<?php
require 'utils/assert_user_is_logged_in.php';
require 'utils/form_field_visualisations.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // TODO - validate the form

    require 'utils/db_connection.php';
    require 'utils/queries.php';
    $formDefinition = getFormDefinition($_POST["form_id"]);

    if (isset($_GET["access_code"]) && isset($formDefinition["accessCode"]) && $formDefinition["accessCode"] != $_GET["access_code"]) {
        header("Location: form.php?id=" . $_GET["id"] . "&access_code_error=1");
        exit;
    }

    $keysToKeep = array_map(function ($fieldDef) {
        return $fieldDef["name"];
    }, $formDefinition["fields"]);

    $fieldsAndValues = [];
    foreach ($keysToKeep as $key) {
        if (isset($_POST[$key])) {
            $fieldsAndValues[$key] = $_POST[$key];
        }
    }
    $response = json_encode($fieldsAndValues, JSON_UNESCAPED_UNICODE);
    echo $response;

    $stmt = $conn->prepare("INSERT INTO responses (form_id, author_fn, response) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $_POST["form_id"], $_SESSION["user_faculty_number"], $response);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit;
} else if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (!isset($_GET["id"])) {
        header("Location: index.php");
        exit;
    }
    require 'utils/queries.php';
    $formDefinition = getFormDefinition($_GET["id"]);

    if (isset($_GET["access_code"]) && isset($formDefinition["accessCode"]) && $formDefinition["accessCode"] != $_GET["access_code"]) {
        header("Location: form.php?id=" . $_GET["id"] . "&access_code_error=1");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($formDefinition["title"]) ?></title>
</head>
<body>
    <h2><?= htmlspecialchars($formDefinition["title"]) ?></h2>
    <p>
        <?= htmlspecialchars($formDefinition["description"]) ?>
    </p>
    <?php if (!isset($_GET["access_code"]) && isset($formDefinition["accessCode"])): ?>
        <?php if (isset($_GET["access_code_error"])): ?>
            <p style="color: red;">Access code is incorrect. Please try again.</p>
        <?php endif; ?>
        <p>This form requires an access code.</p>
        <form method="GET" action="form.php">
            <input type="hidden" name="id" value="<?= htmlspecialchars($_GET["id"]) ?>">
            <input type="text" name="access_code" placeholder="Access Code">
            <button type="submit">Submit</button>
        </form>
    <?php else: ?>
        <form method="POST" action="form.php">
            <input type="hidden" name="form_id" value="<?= htmlspecialchars($_GET["id"]) ?>">
            <input type="hidden" name="access_code" value="<?= htmlspecialchars($_GET["access_code"]) ?>">
            <?php foreach ($formDefinition["fields"] as $field): ?>
                <?php visualizeField($field); ?>
            <?php endforeach; ?>
            <input type="submit" value="Submit">
        </form>
    <?php endif; ?>
</body>
</body>
</html>