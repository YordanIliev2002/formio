<?php
session_start();
if (isset($_SESSION["user_faculty_number"])) {
    // fetch the user's forms
    require 'utils/db_connection.php';
    $stmt = $conn->prepare(
        "SELECT
        id,
        JSON_UNQUOTE(JSON_EXTRACT(form_definition, '$.title')) AS title,
        JSON_UNQUOTE(JSON_EXTRACT(form_definition, '$.accessCode')) AS access_code,
        (SELECT count(*) from responses r where r.form_id = f.id) AS response_count
        FROM forms f WHERE author_fn = ?"
    );
    $stmt->bind_param("s", $_SESSION["user_faculty_number"]);
    $stmt->execute();
    $user_forms = $stmt->get_result();
    $stmt->close();
    $stmt = $conn->prepare("SELECT form_id, JSON_UNQUOTE(JSON_EXTRACT(form_definition, '$.title')) AS title FROM invites INNER JOIN forms ON invites.form_id = forms.id WHERE faculty_number = ? AND did_submit = 0");
    $stmt->bind_param("s", $_SESSION["user_faculty_number"]);
    $stmt->execute();
    $invites = $stmt->get_result();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Formio</title>
    <link rel="stylesheet" href="css/utils/common.css">
    <link rel="stylesheet" href="css/index_style.css">
</head>

<body>
    <?php if (isset($_SESSION["user_faculty_number"])): ?>

        <section id="logged_buttons">
            <button id="hello">Hello, <?= $_SESSION["user_faculty_number"] ?></button>
            <button onclick="location.href='create_form.php'" class="button_logged">Create a new form</button>
            <button onclick="location.href='logout.php'" class="button_logged">Logout</button>
        </section>

        <section id="logged_your_forms">

            <section id="tables" class="<?= $user_forms->num_rows > 0 && $invites->num_rows > 0 ? "flex-row" : "flex-column" ?>">
                <?php if ($user_forms->num_rows == 0): ?>
                    <h2>You don't have any forms yet. Use the button in upper right corner to create one.</h2>
                <?php else: ?>
                    <table>
                        <thead>
                            <caption>Your forms</caption>
                            <tr>
                                <th>Form name</th>
                                <th>Open</th>
                                <th>Copy URL</th>
                                <th>Access code</th>
                                <th>Submissions</th>
                                <th>Invite users</th>
                                <th>Statistics</th>
                            </tr>
                        </thead>

                        <?php while ($row = $user_forms->fetch_assoc()): ?>
                            <?php
                            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
                            $host = $_SERVER['HTTP_HOST'];
                            $path = rtrim(dirname($_SERVER['PHP_SELF']), '/');
                            $form_url = $protocol . $host . $path . "/form.php?id=" . urlencode($row["id"]);
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($row["title"]) ?></td>
                                <td><button onclick="location.href='<?= $form_url ?>'" class="primary-button">Open</button></td>
                                <td><button id="copy-button-<?= $row["id"] ?>" onclick="onCopyButtonClick('<?= $row["id"] ?>', '<?= $form_url ?>')" class="primary-button">Copy URL</button></td>
                                <td>
                                    <?php if ($row["access_code"]): ?>
                                        <p accesscode="<?= $row["access_code"] ?>">Hover to reveal</p>
                                    <?php else: ?>
                                        <p>-</p>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <p id="filled"><?= $row["response_count"] ?></p>
                                </td>
                                <td><a href="invite_users.php?form_id=<?= $row["id"] ?>" class="primary-button">Invite users</a></td>
                                <td><a href="statistics.php?form_id=<?= $row["id"] ?>" class="primary-button">Statistics</a></td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                <?php endif; ?>
                <?php if ($invites->num_rows > 0): ?>
                    <table>
                        <thead>
                            <caption>Pending forms</caption>
                            <th>Name</th>
                            <th>Open</th>
                        </thead>
                        <?php while ($row = $invites->fetch_assoc()): ?>
                            <?php
                            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
                            $host = $_SERVER['HTTP_HOST'];
                            $path = rtrim(dirname($_SERVER['PHP_SELF']), '/');
                            $form_url = $protocol . $host . $path . "/form.php?id=" . urlencode($row["form_id"]);
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($row["title"]) ?></td>
                                <td><button onclick="location.href='<?= $form_url ?>'" class="primary-button">Open</button></td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                <?php endif; ?>
            </section>
        </section>
    <?php else: ?>
        <h2 id="welcome">Welcome to Formio!</h2>
        <section id="not_logged">
            <p>You are not logged in.</p>
            <section id="buttons">
                <button onclick="location.href='login.php'" class="button_not_logged">Login</button>
                <button onclick="location.href='register.php'" class="button_not_logged">Register</button>
            </section>
        </section>
    <?php endif; ?>

    <script>
        function onCopyButtonClick(formId, url) {
            var textarea = document.createElement('textarea');
            textarea.value = url;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            const button = document.getElementById('copy-button-' + formId);
            button.innerHTML = 'Copied!';
            setTimeout(() => {
                button.innerHTML = 'Copy URL';
            }, 1000);
        }

        (() => {
            const accessCodes = document.querySelectorAll('[accesscode]');
            accessCodes.forEach((accessCode) => {
                const code = accessCode.getAttribute('accesscode');
                accessCode.textContent = "Hover to reveal";
                accessCode.addEventListener('mouseover', () => {
                    accessCode.textContent = code;
                });
                accessCode.addEventListener('mouseout', () => {
                    accessCode.textContent = "Hover to reveal";
                });
            });
        })();
    </script>
</body>

</html>