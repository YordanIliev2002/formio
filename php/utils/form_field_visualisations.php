
<?php
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