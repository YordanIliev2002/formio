<?php
require 'utils/assert_user_is_logged_in.php';
require 'utils/form_field_visualisations.php';
require "utils/constants.php";

function hasUserFilledForm($formId)
{
    require 'utils/db_connection.php';
    $stmt = $conn->prepare("SELECT COUNT(*) FROM responses WHERE form_id = ? AND author_fn = ?");
    $stmt->bind_param("ss", $formId, $_SESSION["user_faculty_number"]);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count > 0;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // TODO - validate the form

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

    foreach ($formDefinition["fields"] as $field) {
        if ($field["type"] === "file") {
            $file = $_FILES[$field["name"]];
            if ($file["error"] !== UPLOAD_ERR_NO_FILE) {
                $fileExt = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
                $fileId = uniqid();
                $targetFile = $UPLOAD_PATH . $fileId . "." . $fileExt;

                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
                $host = $_SERVER['HTTP_HOST'];
                $fileUrl = $protocol . $host . "/files.php?id=" . urlencode($fileId . "." . $fileExt);

                if (move_uploaded_file($file["tmp_name"], $targetFile)) {
                    $fieldsAndValues[$field["name"]] = $fileUrl;
                } else {
                    echo "Sorry, there was an error uploading your file $targetFile";
                    exit;
                }
            }
        }
    }
    $response = json_encode($fieldsAndValues, JSON_UNESCAPED_UNICODE);

    require 'utils/db_connection.php';
    $stmt = $conn->prepare("INSERT INTO responses (form_id, author_fn, response) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $_POST["form_id"], $_SESSION["user_faculty_number"], $response);
    $stmt->execute();
    $stmt->close();
    $stmt = $conn->prepare("UPDATE invites SET did_submit = ? WHERE form_id = ? AND faculty_number = ?");
    $did_submit_const = 1;
    $stmt->bind_param("iss", $did_submit_const, $_POST["form_id"], $_SESSION["user_faculty_number"]);
    $stmt->execute();
    $stmt->close();
    header("Location: form.php?id=" . $_POST["form_id"] . "&success=1");
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
    <link rel="stylesheet" href="/css/utils/common.css">
    <link rel="stylesheet" href="/css/form_style.css">
</head>

<body>
    <section id="main">
        <h2><?= htmlspecialchars($formDefinition["title"]) ?></h2>
        <p>
            <?= htmlspecialchars($formDefinition["description"]) ?>
        </p>
        <?php if (hasUserFilledForm($_GET["id"])): ?>
            <?php if (isset($_GET["success"])): ?>
                <p>Thank you for filling out this form!</p>
            <?php else: ?>
                <p>You have already filled this form.</p>
            <?php endif; ?>
            <button onclick="location.href='index.php'" class="primary-button">Return to Home</button>
        <?php elseif (!isset($_GET["access_code"]) && isset($formDefinition["accessCode"])): ?>
            <p>This form requires an access code.</p>
            <?php if (isset($_GET["access_code_error"])): ?>
                <p style="color: red;">This access code is incorrect. Please try again.</p>
            <?php endif; ?>
            <form method="GET" action="form.php">
                <input type="hidden" name="id" value="<?= htmlspecialchars($_GET["id"]) ?>">
                <input type="text" name="access_code" placeholder="Access Code" class="text-field">
                <section>
                    <button type="submit" class="primary-button">Submit</button>
                    <button type="button" onclick="location.href='index.php'" class="primary-button">Return to Home</button>
                </section>
            </form>
        <?php else: ?>
            <form method="POST" action="form.php" enctype="multipart/form-data">
                <input type="hidden" name="form_id" value="<?= htmlspecialchars($_GET["id"]) ?>">
                <input type="hidden" name="access_code" value="<?= htmlspecialchars($_GET["access_code"] ?? '') ?>">
                <?php foreach ($formDefinition["fields"] as $field): ?>
                    <?php visualizeField($field); ?>
                <?php endforeach; ?>
                <section>
                    <input type="submit" class="primary-button" value="Submit">
                    <button type="button" onclick="location.href='index.php'" class="primary-button">Return to Home</button>
                </section>
            </form>
        <?php endif; ?>
    </section>
</body>

</html>