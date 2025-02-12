<?php
function fetchResponses()
{
    require 'utils/db_connection.php';
    $responses = [];
    $stmt = $conn->prepare("SELECT responses.*, users.mail as mail FROM responses JOIN users ON users.faculty_number = responses.author_fn WHERE form_id = ?");
    $formId = $_POST["form_id"] ?? $_GET["form_id"];
    $stmt->bind_param("s", $formId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $responses[] = $row;
    }
    $stmt->close();
    return $responses;
}
function fetchInvites()
{
    require 'utils/db_connection.php';
    $invites = [];
    $stmt = $conn->prepare("SELECT * FROM invites WHERE form_id = ?");
    $formId = $_POST["form_id"] ?? $_GET["form_id"];
    $stmt->bind_param("s", $formId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $invites[] = [
            'faculty_number' => $row['faculty_number'],
            'did_submit' => ($row['did_submit'] == 0) ? "𝕏" : "✓"
        ];
    }
    $stmt->close();
    return $invites;
}
?>
<?php
require 'utils/assert_user_is_logged_in.php';
$responses = fetchResponses();
$invites = fetchInvites();

if (isset($_POST['export'])) {
    $data = [];
    foreach ($responses as $response) {
        $obj = json_decode($response['response'], true);
        $obj["_faculty_number"] = $response["author_fn"];
        $data[] = $obj;
    }
    $jsonData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    // TODO - check if downloader is the right one
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="responses.json"');

    echo $jsonData;
    exit;
}
?>

<!DOCTYPE HTML>
<html>

<head>
    <title>Form statistics</title>
</head>
<link rel="stylesheet" href="css/utils/common.css">
<link rel="stylesheet" href="css/statistics_style.css">

<body>
    <section id="buttons">
        <form method="post">
            <input type="hidden" name="form_id" value="<?= htmlspecialchars($_GET["form_id"]) ?>">
            <button type="submit" name="export" class="primary-button">Export</button>
        </form>
        <button type="button" onclick="location.href='index.php'" class="primary-button">Return to Home instead</button>
    </section>
    <section id="tables">
        <table>
            <caption>Table of submissions: </caption>
            <thead>
                <tr>
                    <th>Faculty Number</th>
                    <th>Mail</th>
                    <th>Submitted At</th>
                </tr>
            </thead>
            <?php foreach ($responses as $response): ?>

                <tr>
                    <td><?php echo htmlspecialchars($response['author_fn']); ?></td>
                    <td><?php echo htmlspecialchars($response['mail']); ?></td>
                    <td><?php echo htmlspecialchars($response['submitted_at']); ?></td>
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
            <?php foreach ($invites as $invite): ?>
                <tr>
                    <td><?php echo htmlspecialchars($invite['faculty_number']); ?></td>
                    <td style="color: <?= ($invite['did_submit'] == "✓") ? 'green' : 'red'; ?>;"><?php echo htmlspecialchars($invite['did_submit']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </section>
</body>

</html>