<?php
// login.php
require_once 'config.php';

$error = '';
$account_blocked = false;
$blocked_username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    $agent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user) {
        if ($user['is_blocked'] == 1) {
            $account_blocked = true;
            $blocked_username = $user['username'];
            $error = "គណនីរបស់អ្នកត្រូវបាន Block ដោយសារបញ្ចូលពាក្យសម្ងាត់ខុស ៣ដង! សូមផ្ញើសំណើដោះសោខាងក្រោម។";

            // កត់ត្រា Log
            $log = $pdo->prepare("INSERT INTO login_logs (user_id, username_attempted, ip_address, user_agent, status) VALUES (?, ?, ?, ?, 'blocked_attempt')");
            $log->execute([$user['id'], $username, $ip, $agent]);
        } elseif (password_verify($password, $user['password'])) {
            // Login ជោគជ័យ -> កំណត់ចំនួនខុសមក 0 វិញ
            $pdo->prepare("UPDATE users SET failed_attempts = 0 WHERE id = ?")->execute([$user['id']]);

            // កត់ត្រា Log
            $log = $pdo->prepare("INSERT INTO login_logs (user_id, username_attempted, ip_address, user_agent, status) VALUES (?, ?, ?, ?, 'success')");
            $log->execute([$user['id'], $username, $ip, $agent]);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];

            // បែងចែកសិទ្ធិពេល Login រួច
            if ($user['role'] === 'admin') {
                header("Location: dashboard.php");
            } else {
                header("Location: suspect_search.php");
            }
            exit;
        } else {
            // ពាក្យសម្ងាត់មិនត្រឹមត្រូវ
            $new_attempts = $user['failed_attempts'] + 1;

            if ($new_attempts >= 3) {
                // គ្រប់ ៣ដង -> Block គណនី
                $pdo->prepare("UPDATE users SET failed_attempts = ?, is_blocked = 1 WHERE id = ?")->execute([$new_attempts, $user['id']]);
                $account_blocked = true;
                $blocked_username = $user['username'];
                $error = "អ្នកបានវាយពាក្យសម្ងាត់ខុស ៣ដង! គណនីត្រូវបាន Block។ សូមបំពេញមូលហេតុដើម្បីស្នើដោះសោ។";
            } else {
                $pdo->prepare("UPDATE users SET failed_attempts = ? WHERE id = ?")->execute([$new_attempts, $user['id']]);
                $remaining = 3 - $new_attempts;
                $error = "ពាក្យសម្ងាត់មិនត្រឹមត្រូវទេ! អ្នកនៅសល់ឱកាសសាកល្បង {$remaining} ដងទៀត។";
            }

            // កត់ត្រា Log
            $log = $pdo->prepare("INSERT INTO login_logs (user_id, username_attempted, ip_address, user_agent, status) VALUES (?, ?, ?, ?, 'failed')");
            $log->execute([$user['id'], $username, $ip, $agent]);
        }
    } else {
        $error = "មិនមានឈ្មោះអ្នកប្រើប្រាស់នេះក្នុងប្រព័ន្ធទេ!";
    }
}
?>
<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <title>ប្រព័ន្ធគ្រប់គ្រង - ចូលប្រើប្រាស់</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Kantumruy Pro', sans-serif; background-color: #f0f2f5; }
        .login-card { max-width: 420px; margin: 80px auto; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
<div class="container">
    <div class="card login-card p-4">
        <h4 class="text-center mb-4 text-primary fw-bold">ប្រព័ន្ធគ្រប់គ្រងប៉ុស្តិ៍នគរបាល</h4>

        <?php if ($error): ?>
            <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!$account_blocked): ?>
            <form method="POST" action="index.php">
                <div class="mb-3">
                    <label class="form-label">ឈ្មោះគណនី (Username)</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">ពាក្យសម្ងាត់ (Password)</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">ចូលប្រព័ន្ធ</button>
            </form>
        <?php else: ?>
            <!-- Form ស្នើសុំ Unblock ពេលគណនីត្រូវបានផ្អាក -->
            <form method="POST" action="request_unblock.php">
                <input type="hidden" name="username" value="<?= htmlspecialchars($blocked_username) ?>">
                <div class="mb-3">
                    <label class="form-label text-danger">មូលហេតុនៃការវាយពាក្យសម្ងាត់ខុស៖</label>
                    <textarea name="reason" class="form-control" rows="3" placeholder="សូមបញ្ជាក់មូលហេតុលម្អិត..." required></textarea>
                </div>
                <button type="submit" class="btn btn-warning w-100 text-dark fw-bold">ផ្ញើសំណើដោះសោទៅ Admin</button>
            </form>
            
        <?php endif; ?>
        <?php echo password_hash("123456", PASSWORD_DEFAULT); ?>
    </div>
</div>
</body>
</html>