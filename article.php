<?php
require_once 'includes/db.php';

if (!isset($_GET['id'])) {
    echo "Invalid article ID.";
    exit;
}

$id = (int)$_GET['id'];
$sql = "SELECT * FROM articles WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    echo "Article not found.";
    exit;
}

$row = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($row['title']); ?></title>
</head>
<body>
    <h1><?php echo htmlspecialchars($row['title']); ?></h1>
    <?php
    if ($row['image']) {
        echo '<img src="uploads/' . htmlspecialchars($row['image']) . '" alt="Image"><br><br>';
    }
    ?>
    <div style="font-size: <?php echo htmlspecialchars($row['font_size']); ?>; color: <?php echo htmlspecialchars($row['font_color']); ?>">
        <?php echo nl2br($row['content']); ?>
    </div>
</body>
</html>
