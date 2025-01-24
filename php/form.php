<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // TODO - filter extra fields in post request
    // TODO - validate the form
    // TODO - validate the access code

    require 'utils/db_connection.php';

    // TODO - copy paste!
    $stmt = $conn->prepare("SELECT form_definition FROM forms WHERE id = ?");
    $stmt->bind_param("i", $_POST["form_id"]);
    $stmt->execute();
    $stmt->bind_result($formDefinition);
    $stmt->fetch();
    $stmt->close();
    // TODO - test with UTF 8
    $formDefinition = json_decode($formDefinition, true); 

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
    exit;
} else if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // TODO - verify the access code
    if (!isset($_GET["id"])) {
        echo "No id";
        exit;
    }
    require 'utils/db_connection.php';
    $stmt = $conn->prepare("SELECT form_definition FROM forms WHERE id = ?");
    $stmt->bind_param("i", $_GET["id"]);
    $stmt->execute();
    $stmt->bind_result($formDefinition);
    $stmt->fetch();
    // TODO - test with UTF 8
    $formDefinition = json_decode($formDefinition, true); 
    $conn->close();
}
?>
<?php
// TODO - move to a new file?
function visualizeField($field) {
    echo '<label for="' . htmlspecialchars($field["name"]) . '">' . htmlspecialchars($field["label"]) . '</label>';
    if ($field["required"]) {
        // TODO - no inline css
        echo '<span style="color: red; padding-left: 5px">*</span>';
    }
    echo "<br>";
    switch ($field["type"]) {
        case "text":
            ?>
            <input
                type="text"
                id="<?= htmlspecialchars($field["name"]) ?>"
                name="<?= htmlspecialchars($field["name"]) ?>"
                <?php if ($field["required"]) { echo "required"; } ?>
            >
            <?php
        break;
        case "textarea":
            ?>
            <textarea
                id="<?= htmlspecialchars($field["name"]) ?>"
                name="<?= htmlspecialchars($field["name"]) ?>"
                rows="5"
                cols="40"
                <?php if ($field["required"]) { echo "required"; } ?>
            ></textarea>
            <?php
        break;
        case "multiple_choice":
            if (!empty($field["choices"]) && is_array($field["choices"])) {
                ?>
                <select
                    id="<?= htmlspecialchars($field["name"]) ?>"
                    name="<?= htmlspecialchars($field["name"]) ?>"
                    <?php if ($field["required"]) { echo "required"; } ?>
                >
                    <option value="" selected disabled>-- Please select --</option>
                    <?php foreach ($field["choices"] as $option) { ?>
                        <option value="<?= htmlspecialchars($option) ?>">
                            <?= htmlspecialchars($option) ?>
                        </option>
                    <?php } ?>
                </select>
                <?php
            } else {
                echo "ERROR: Missing or invalid options for multiple choice field.";
            }
            break;
        default:
            echo "ERROR: Unknown field type " . $field["type"];
    }
    // TODO - no br
    echo "<br>";
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
    <form method="POST" action="form.php">
        <input type="hidden" name="form_id" value="<?= htmlspecialchars($_GET["id"]) ?>">
        <?php foreach ($formDefinition["fields"] as $field) { ?>
            <?php visualizeField($field); ?>
        <?php } ?>
        <input type="submit" value="Submit">
    </form>
</body>
</html>