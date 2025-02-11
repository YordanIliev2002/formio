<?php
require 'utils/assert_user_is_logged_in.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'utils/db_connection.php';
    $formDefinition = $_POST["form_definition"];
    // TODO - validate the form definition
    $stmt = $conn->prepare("INSERT INTO forms (form_definition, author_fn) VALUES (?, ?)");
    $stmt->bind_param("ss", $formDefinition, $_SESSION["user_faculty_number"]);
    try {
        $stmt->execute();
        header("Location: index.php");
    } catch (Exception $e) {
        echo "There was an error"; // TODO - better error handling
    }
    $stmt->close();
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Create a new form</title>
    <link rel="stylesheet" href="css/utils/common.css">
    <link rel="stylesheet" href="css/create_form_style.css">
</head>

<body>
    <section id="main">
        <h2>Create a new form</h2>
        <form method="POST" action="create_form.php">
            <!-- TODO - validate the form on the client side too, for better feedback -->
            <label for="form_definition">Form definition:</label>
            <textarea id="form_definition" name="form_definition" rows="5" cols="33" placeholder="Put your form json definition here..."></textarea>
            <p id="error"></p>
            <input id="submit-button" disabled type="submit" value="Create form" class="primary-button">
            <button type="button" onclick="location.href='index.php'" class="primary-button">Return to Home instead</button>
        </form>
    </section>

    <script>
        function isNotEmpty(value) {
            return value !== undefined && value.trim() !== "";
        }

        function isFieldValid(field) {
            switch (field.type) {
                case "text":
                    return isNotEmpty(field.name) && isNotEmpty(field.label);
                case "textarea":
                    return isNotEmpty(field.name) && isNotEmpty(field.label);
                case "multiple_choice":
                    return isNotEmpty(field.name) && isNotEmpty(field.label) && Array.isArray(field.choices) && field.choices.every(isNotEmpty);
                case "file":
                    return isNotEmpty(field.name) && isNotEmpty(field.label) && isNotEmpty(field.fileType);
                default:
                    return false;
            }
            return true;
        }

        const formDefinition = document.getElementById("form_definition");
        const error = document.getElementById("error");
        const submitButton = document.getElementById("submit-button");

        function validateFormDefinition() {
            const content = formDefinition.value;
            if (content.trim() === "") {
                error.textContent = "Empty definition";
                submitButton.disabled = true;
                return;
            }

            let json = null;
            try {
                json = JSON.parse(content);
            } catch {
                error.textContent = "Not a json";
                submitButton.disabled = true;
                return;
            }

            try {
                if (typeof json !== "object") {
                    error.textContent = "Not an object";
                    submitButton.disabled = true;
                    return;
                }
                if (!isNotEmpty(json.title)) {
                    error.textContent = "No title";
                    submitButton.disabled = true;
                    return;
                }
                if (!isNotEmpty(json.description)) {
                    error.textContent = "No description";
                    submitButton.disabled = true;
                    return;
                }
                if (json.fields === undefined || !Array.isArray(json.fields)) {
                    error.textContent = "No fields";
                    submitButton.disabled = true;
                    return;
                }
                const areFieldsValid = json.fields.length > 0 && json.fields.every(field => isFieldValid(field));
                if (!areFieldsValid) {
                    error.textContent = "Invalid fields";
                    submitButton.disabled = true;
                    return;
                }
            } catch {
                error.textContent = "Unexpected error";
                submitButton.disabled = true;
                return;
            }
            error.textContent = "";
            submitButton.disabled = false;
        }

        (() => {
            formDefinition.addEventListener("input", validateFormDefinition);
            validateFormDefinition();
        })();
    </script>
</body>

</html>