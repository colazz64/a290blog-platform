<?php
$servername = "localhost";
$username = "root";
$password = "mysql";  // ← 修改这里，默认 AMPPS 是这个
$dbname = "test";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("connect fail: " . $conn->connect_error);
}
?>
