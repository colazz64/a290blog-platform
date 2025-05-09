<?php
require_once 'includes/db.php';
require_once 'includes/validation.php';

$title = $content = $font_size = $font_color = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $font_size = $_POST['font_size'];
    $font_color = $_POST['font_color'];

    if (!validate_title($title)) {
        $errors[] = "Title must be between 3 and 15 words.";
    }

    if (!validate_content($content)) {
        $errors[] = "Content must be between 20 and 200 words.";
    }

    $image_name = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        if (!validate_image($_FILES['image'])) {
            $errors[] = "Invalid image. Only PNG/JPG under 2MB allowed.";
        } else {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_name = uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $image_name);
        }
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO articles (title, image, content, font_size, font_color) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $title, $image_name, $content, $font_size, $font_color);
        $stmt->execute();
        $new_id = $stmt->insert_id;
        header("Location: article.php?id=$new_id");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add New Article</title>
</head>
<body>
    <h1>Add New Article</h1>

    <?php
    if (!empty($errors)) {
        echo "<ul style='color: red;'>";
        foreach ($errors as $e) {
            echo "<li>$e</li>";
        }
        echo "</ul>";
    }
    ?>

    <form action="" method="post" enctype="multipart/form-data">
        <label>Title:</label><br>
        <input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>" maxlength="200"><br><br>

        <label>Article Image:</label><br>
        <input type="file" name="image" accept="image/png, image/jpeg"><br><br>

        <label>Font Size:</label>
        <select name="font_size">
            <?php
            for ($i = 10; $i <= 16; $i++) {
                $selected = ($font_size == $i . "px") ? "selected" : "";
                echo "<option value='{$i}px' $selected>{$i}px</option>";
            }
            ?>
        </select>

        <label>Color:</label>
        <input type="color" name="font_color" value="<?php echo htmlspecialchars($font_color ?: '#000000'); ?>"><br><br>

        <label>Content:</label><br>
        <textarea name="content" rows="10" cols="50"><?php echo htmlspecialchars($content); ?></textarea><br><br>

        <button type="submit">Save</button>
    </form>
</body>
</html>
