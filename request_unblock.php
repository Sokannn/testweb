<?php
// request_unblock.php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $reason = trim($_POST['reason'] ?? '');

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && !empty($reason)) {
        // កត់ត្រាសំណើចូល Database
        $stmtReq = $pdo->prepare("INSERT INTO unblock_requests (user_id, reason) VALUES (?, ?)");
        $stmtReq->execute([$user['id'], $reason]);

        // រៀបចំសារផ្ញើទៅកាន់ Telegram
        $msg = "🚨 <b>ការស្នើសុំដោះសោគណនី (Unblock)</b>\n\n";
        $msg .= "👤 <b>ឈ្មោះ៖</b> {$user['fullname']}\n";
        $msg .= "🏷 <b>គណនី៖</b> {$user['username']}\n";
        $msg .= "🏢 <b>អង្គភាព/ប៉ុស្តិ៍៖</b> {$user['station_name']}\n";
        $msg .= "📝 <b>មូលហេតុ៖</b> " . htmlspecialchars($reason) . "\n";
        $msg .= "⏰ <b>កាលបរិច្ឆេទ៖</b> " . date('Y-m-d H:i:s') . "\n";

        // បង្កើតប៊ូតុង Unblock ផ្ទាល់លើ Telegram
        $keyboard = [
            [
                ['text' => '✅ ដោះសោ (Unblock)', 'callback_data' => 'unblock_' . $user['id']],
                ['text' => '❌ បដិសេធ', 'callback_data' => 'reject_' . $user['id']]
            ]
        ];

        sendTelegramMessage($msg, $keyboard);

        echo "<!DOCTYPE html><html lang='km'><head><meta charset='UTF-8'><link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'></head><body class='p-5 text-center'>";
        echo "<div class='alert alert-success d-inline-block p-4'>សំណើរបស់អ្នកត្រូវបានផ្ញើជូនអ្នកគ្រប់គ្រងតាម Telegram រួចរាល់ហើយ។ សូមរង់ចាំការត្រួតពិនិត្យ និងដោះសោ។<br><a href='login.php' class='btn btn-primary mt-3'>ត្រឡប់ទៅទំព័រដើម</a></div>";
        echo "</body></html>";
        exit;
    }
}
header("Location: login.php");
exit;