<?php
// telegram_webhook.php
require_once 'config.php';

$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (isset($update['callback_query'])) {
    $callback_id = $update['callback_query']['id'];
    $data = $update['callback_query']['data'];
    $message_id = $update['callback_query']['message']['message_id'];
    $chat_id = $update['callback_query']['message']['chat']['id'];

    if (strpos($data, 'unblock_') === 0) {
        $user_id = str_replace('unblock_', '', $data);

        // ដោះសោគណនីក្នុង Database
        $stmt = $pdo->prepare("UPDATE users SET is_blocked = 0, failed_attempts = 0 WHERE id = ?");
        $stmt->execute([$user_id]);

        $pdo->prepare("UPDATE unblock_requests SET status = 'approved' WHERE user_id = ?")->execute([$user_id]);

        // កែប្រែសារក្នុង Telegram បញ្ជាក់ថាបាន Unblock រួច
        $text = "✅ <b>គណនីនេះត្រូវបាន Unblock ដោយជោគជ័យ!</b>";
        file_get_contents("https://api.telegram.org/bot" . TELEGRAM_BOT_TOKEN . "/sendMessage?chat_id={$chat_id}&text=" . urlencode($text) . "&parse_mode=HTML");
    } elseif (strpos($data, 'reject_') === 0) {
        $user_id = str_replace('reject_', '', $data);
        $pdo->prepare("UPDATE unblock_requests SET status = 'rejected' WHERE user_id = ?")->execute([$user_id]);

        $text = "❌ <b>សំណើដោះសោត្រូវបានបដិសេធ!</b>";
        file_get_contents("https://api.telegram.org/bot" . TELEGRAM_BOT_TOKEN . "/sendMessage?chat_id={$chat_id}&text=" . urlencode($text) . "&parse_mode=HTML");
    }

    // បញ្ជាក់ឆ្លើយតបទៅកាន់ Telegram Callback
    file_get_contents("https://api.telegram.org/bot" . TELEGRAM_BOT_TOKEN . "/answerCallbackQuery?callback_query_id=" . $callback_id);
}