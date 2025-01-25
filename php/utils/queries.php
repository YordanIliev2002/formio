<?php
function getFormDefinition($formId) {
    require "db_connection.php";
    $stmt = $conn->prepare("SELECT form_definition FROM forms WHERE id = ?");
    $stmt->bind_param("i", $formId);
    $stmt->execute();
    $formDefinition = "";
    $stmt->bind_result($formDefinition);
    $stmt->fetch();
    $stmt->close();
    return json_decode($formDefinition, true); 
}
?>