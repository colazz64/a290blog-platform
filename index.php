<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'includes/db.php';

// Articles per page
$articlesPerPage = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $articlesPerPage;

$sql = "SELECT * FROM articles ORDER BY created_at DESC LIMIT $offset, $articlesPerPage";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Blog Home</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .container { display: flex; gap: 20px; }
        .column { flex: 1; }
        .article { border: 1px solid #ccc; padding: 10px; margin-bottom: 15px; }
        img { max-width: 100%; }
        .pagination { margin-top: 20px; text-align: center; }
        .admin-button {
            text-align: right;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h1>Welcome to Our Blog</h1>

    <div class="admin-button">
        <a href="admin.php">
            <button>Go to Admin Dashboard</button>
        </a>
    </div>

    <div class="container">
        <div class="column">
            <?php
            $i = 0;
            while ($row = $result->fetch_assoc()) {
                if ($i == 3) echo '</div><div class="column">';
                echo '<div class="article">';
                echo '<h2>' . htmlspecialchars($row['title']) . '</h2>';
                if ($row['image']) {
                    echo '<img src="uploads/' . htmlspecialchars($row['image']) . '" alt="Article image">';
                }
                $snippet = implode(' ', array_slice(explode(' ', strip_tags($row['content'])), 0, 50)) . '...';
                echo '<p>' . $snippet . '</p>';
                echo '<a href="article.php?id=' . $row['id'] . '">Read More</a>';
                echo '</div>';
                $i++;
            }
            ?>
        </div>
    </div>

    <div class="pagination">
        <?php
        $countResult = $conn->query("SELECT COUNT(*) AS total FROM articles");
        $totalArticles = $countResult->fetch_assoc()['total'];
        $totalPages = ceil($totalArticles / $articlesPerPage);

        if ($page > 1) {
            echo '<a href="?page=' . ($page - 1) . '">&lt; previous</a> ';
        }
        echo '(' . $page . ')';
        if ($page < $totalPages) {
            echo ' <a href="?page=' . ($page + 1) . '">next &gt;</a>';
        }
        ?>
    </div>
</body>
</html>
