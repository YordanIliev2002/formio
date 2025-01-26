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
    <link rel="stylesheet" href="/css/create_form_style.css">
</head>
<body>
    <section id="box">
    <h2>Create a new form</h2>
    <form method="POST" action="create_form.php">
        <!-- TODO - validate the form on the client side too, for better feedback -->
        <label for="form_definition">Form definition:</label>
        <textarea id="form_definition" name="form_definition" rows="5" cols="33" placeholder="Put your form json definition here..."></textarea>
        <input type="submit" value="Create form" class="primary-button">
        <button type="button" onclick="location.href='index.php'" class="primary-button" >Return to Home instead</button>
    </form>
    </section>
</body>
</html>