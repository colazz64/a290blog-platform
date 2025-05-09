<?php
require_once 'includes/db.php';
require_once 'includes/validation.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die("Article ID missing.");
}

// 获取原始文章数据
$stmt = $conn->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$original = $stmt->get_result()->fetch_assoc();
if (!$original) {
    die("Article not found.");
}

$title = $original['title'];
$content = $original['content'];
$font_size = $original['font_size'];
$font_color = $original['font_color'];
$image_name = $original['image'];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $font_size = $_POST['font_size'];
    $font_color = $_POST['font_color'];

    // 验证输入
    if (!validate_title($title)) {
        $errors[] = "Title must be between 3 and 15 words.";
    }
    if (!validate_content($content)) {
        $errors[] = "Content must be between 20 and 200 words.";
    }

    // 检查是否上传新图
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        if (!validate_image($_FILES['image'])) {
            $errors[] = "Invalid image. Only PNG/JPG under 2MB allowed.";
        } else {
            // 删除旧图
            if ($image_name && file_exists("uploads/$image_name")) {
                unlink("uploads/$image_name");
            }
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_name = uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $image_name);
        }
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE articles SET title=?, image=?, content=?, font_size=?, font_color=? WHERE id=?");
        $stmt->bind_param("sssssi", $title, $image_name, $content, $font_size, $font_color, $id);
        $stmt->execute();
        header("Location: article.php?id=$id");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Article</title>
</head>
<body>
    <h1>Edit Article</h1>

    <?php if (!empty($errors)): ?>
        <ul style="color:red;">
            <?php foreach ($errors as $e): echo "<li>$e</li>"; endforeach; ?>
        </ul>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data">
        <label>Title:</label><br>
        <input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>" maxlength="200"><br><br>

        <label>Current Image:</label><br>
        <?php if ($image_name): ?>
            <img src="uploads/<?php echo htmlspecialchars($image_name); ?>" width="200"><br>
        <?php endif; ?>
        <label>Upload New Image:</label><br>
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
        <input type="color" name="font_color" value="<?php echo htmlspecialchars($font_color); ?>"><br><br>

        <label>Content:</label><br>
