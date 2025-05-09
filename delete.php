<?php
require_once 'includes/db.php';

if (!isset($_GET['id'])) {
    die("No article ID provided.");
}

$id = (int)$_GET['id'];

// 获取要删除的图像文件名
$stmt = $conn->prepare("SELECT image FROM articles WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Article not found.");
}
$row = $result->fetch_assoc();
$image = $row['image'];

// 删除图像文件
if ($image && file_exists("uploads/$image")) {
    unlink("uploads/$image");
}

// 删除数据库记录
$stmt = $conn->prepare("DELETE FROM articles WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: admin.php");
exit();
?>
