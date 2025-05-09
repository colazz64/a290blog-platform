<?php
// 数据库配置
$host = 'localhost';
$port = 3307; // ⚠️ 改成你实际设置的端口
$user = 'root';
$password = '';
$database = 'test'; // 你想连接的数据库名，可以先在 phpMyAdmin 创建

// 创建连接
$conn = new mysqli($host, $user, $password, $database, $port);

// 检查连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}
echo "✅ 数据库连接成功！";

// 关闭连接
$conn->close();
?>
