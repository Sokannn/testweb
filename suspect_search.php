<?php
// suspect_search.php
require_once 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <title>ស្វែងរកមុខសញ្ញា</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>🔍 ផ្នែកស្វែងរកមុខសញ្ញា (ប៉ុស្តិ៍ស្រុកពញាក្រែក)</h3>
            <div>
                <span>សួស្តី, <?= htmlspecialchars($_SESSION['fullname']) ?></span>
                <a href="logout.php" class="btn btn-sm btn-outline-danger ms-2">ចាកចេញ</a>
            </div>
        </div>
        <div class="card p-3">
            <input type="text" class="form-control" placeholder="បញ្ចូលឈ្មោះ ឬភិនភាគដើម្បីស្វែងរកមុខសញ្ញា...">
        </div>
    </div>
</body>
</html>