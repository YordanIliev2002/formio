<?php
    require 'utils/db_connection.php';
    $dbData = [];
    $stmt = $conn->prepare("SELECT * FROM responses WHERE form_id = ?");
    $formId = $_POST["form_id"] ?? $_GET["form_id"];
    $stmt->bind_param("s",$formId );
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){

        while ($row = $result->fetch_assoc()) {
             $dbData[] = $row;
            
        }
        
    }
    if(isset($_POST['export'])){
        $data = [];
        if($result->num_rows > 0){
            foreach($dbData as $tableData) {
                 $data[] = json_decode($tableData['response'], true);
            }
            $jsonData = json_encode($data, JSON_PRETTY_PRINT);
// TODO - check if downloader is the right one
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="responses.json"');

            echo $jsonData;
            $stmt->close();
            exit;
        } else{
            echo "There were no responses";
        }
    }
    $stmt->close();
?>

<!DOCTYPE HTML>
<html>
    <head><title>Form statistics</title></head>
    <body>
        <p>You can export the answers to this form in JSON file with the button below: </p>
    <form method="post">
        <input type="hidden" name="form_id" value="<?= htmlspecialchars($_GET["form_id"]) ?>">
        <button type="submit" name="export">Export</button>


    </form>
    <p>Table of submissions: </p>
    <table>
         <?php foreach ($dbData as $data): ?>
            <tr>
                <td>Author Name</td>
                <td><?php echo htmlspecialchars($data['author_fn']); ?></td>
            </tr>
            <tr>
                <td>Submitted Date</td>
                <td><?php echo htmlspecialchars($data['submitted_at']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    </body>
</html>