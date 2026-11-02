<?php
// config.php
session_start();

$db_host = 'sql205.infinityfree.com';
$db_name = 'if0_43333449_post';
$db_user = 'if0_43333449';
$db_pass = 'olsV6ZnDwDXVxu';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("ការតភ្ជាប់ទិន្នន័យបរាជ័យ៖ " . $e->getMessage());
}

// កំណត់ Token របស់ Telegram Bot និង Chat ID របស់អ្នកគ្រប់គ្រង
define('TELEGRAM_BOT_TOKEN', '9167813492:AAH65n6hxHMQXX81tVzWF3AMaS7Q2FOW6vU');
define('ADMIN_CHAT_ID', '435588412');

// មុខងារផ្ញើសារទៅកាន់ Telegram
function sendTelegramMessage($message, $inline_keyboard = null) {
    $url = "https://api.telegram.org/bot" . TELEGRAM_BOT_TOKEN . "/sendMessage";
    $payload = [
        'chat_id' => ADMIN_CHAT_ID,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];
    if ($inline_keyboard) {
        $payload['reply_markup'] = json_encode(['inline_keyboard' => $inline_keyboard]);
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}