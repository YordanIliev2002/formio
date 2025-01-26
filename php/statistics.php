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
    $dbDataInvites = [];
    $stmt = $conn->prepare("SELECT * FROM invites WHERE form_id = ?");
    $stmt->bind_param("s",$formId );
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        while ($row = $result->fetch_assoc()) {
             $dbDataInvites[] = [
                'faculty_number' => $row['faculty_number'],
                'did_submit' => ($row['did_submit'] == 0) ? "𝕏" : "✓"
             ];
        }
        
    }

    if(isset($_POST['export'])){
        $data = [];
        if($result->num_rows > 0){
            foreach($dbData as $tableData) {
                 $data[] = json_decode($tableData['response'], true);
            }
            $jsonData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
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
    <link rel="stylesheet" href="/css/statistics_style.css">
    <body>
    <section id="buttons">
    <form method="post">
        <input type="hidden" name="form_id" value="<?= htmlspecialchars($_GET["form_id"]) ?>">
        <button type="submit" name="export" id = "export_button">Export</button>
    </form>

    <a href="index.php" id="return_home">Return to Home</a>
    </section>
    <section id="tables">
    <table>
    <caption>Table of submissions: </caption>
        <thead>
        <tr>
            <th>Author Name</th>
            <th>Submitted At</th>
        </tr>
        </thead>
         <?php foreach ($dbData as $data): ?>
            
            <tr>        
            <td><?php echo htmlspecialchars($data['author_fn']); ?></td>
            <td><?php echo htmlspecialchars($data['submitted_at']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <table>
    <caption>Table of invites: </caption>
        <thead>
        <tr>
            <th>Faculty Number</th>
            <th>Has submitted</th>
        </tr>
        </thead>
         <?php foreach ($dbDataInvites as $data): ?>
            <tr>        
            <td><?php echo htmlspecialchars($data['faculty_number']); ?></td>
            <td style="color: <?= ($data['did_submit']== "✓") ? 'green' : 'red'; ?>;"><?php echo htmlspecialchars($data['did_submit']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    </section>
    </body>
</html>