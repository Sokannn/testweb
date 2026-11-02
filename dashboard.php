<?php
// dashboard.php
require_once 'config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("អ្នកគ្មានសិទ្ធិចូលកាន់ទំព័រនេះទេ!");
}

// ទាញយក Login Logs
$logs = $pdo->query("SELECT * FROM login_logs ORDER BY created_at DESC LIMIT 50")->fetchAll();
?>
<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <title>Dashboard អ្នកគ្រប់គ្រង</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb-4">
            <h2>📊 Dashboard អ្នកគ្រប់គ្រងប្រព័ន្ធ</h2>
            <a href="logout.php" class="btn btn-danger">ចាកចេញ</a>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-dark text-white">កំណត់ត្រា Login Logs ថ្មីៗ</div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>ល.រ</th>
                            <th>គណនីព្យាយាម Login</th>
                            <th>IP Address</th>
                            <th>ស្ថានភាព</th>
                            <th>កាលបរិច្ឆេទ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($logs as $log): ?>
                        <tr>
                            <td><?= $log['id'] ?></td>
                            <td><?= htmlspecialchars($log['username_attempted']) ?></td>
                            <td><?= $log['ip_address'] ?></td>
                            <td>
                                <?php if($log['status'] === 'success'): ?>
                                    <span class="badge bg-success">ជោគជ័យ</span>
                                <?php elseif($log['status'] === 'blocked_attempt'): ?>
                                    <span class="badge bg-danger">គណនីជាប់ Block</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">ខុសពាក្យសម្ងាត់</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $log['created_at'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>