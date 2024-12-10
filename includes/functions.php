<?php
require_once __DIR__ . '/db.php';

function redirect($url) {
    header("Location: " . $url);
    exit;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserProfile($user_id) {
    $pdo = getPdoConnection();
    $stmt = $pdo->prepare("SELECT * FROM profiles WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}

function getJobStatus($user_id) {
    $pdo = getPdoConnection();
    $stmt = $pdo->prepare("SELECT * FROM job_status WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}

function getSchedules($user_id) {
    $pdo = getPdoConnection();
    $stmt = $pdo->prepare("SELECT * FROM schedules WHERE user_id = ? ORDER BY event_date ASC, event_time ASC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

function getOtherUserProfiles($current_user_id) {
    $pdo = getPdoConnection();
    $stmt = $pdo->prepare("
        SELECT u.id as user_id, p.name, p.university, p.major, p.icon_image, p.github_url, p.twitter_url, p.homepage_url, p.is_status_public, u.account_id
        FROM users u
        LEFT JOIN profiles p ON u.id = p.user_id
        WHERE u.id != ?
    ");
    $stmt->execute([$current_user_id]);
    return $stmt->fetchAll();
}

function getUserById($user_id) {
    $pdo = getPdoConnection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}

function getMessages($user_id, $other_id) {
    $pdo = getPdoConnection();
    $stmt = $pdo->prepare("
        SELECT * FROM messages 
        WHERE (sender_id=? AND receiver_id=?) OR (sender_id=? AND receiver_id=?) 
        ORDER BY created_at ASC
    ");
    $stmt->execute([$user_id, $other_id, $other_id, $user_id]);
    return $stmt->fetchAll();
}

function getDMUsers($user_id) {
    $pdo = getPdoConnection();
    $stmt = $pdo->prepare("
        SELECT DISTINCT 
        CASE WHEN sender_id=? THEN receiver_id ELSE sender_id END as other_id
        FROM messages 
        WHERE sender_id=? OR receiver_id=?
    ");
    $stmt->execute([$user_id, $user_id, $user_id]);
    return $stmt->fetchAll();
}

function getUnreadMessageCount($user_id) {
    $pdo = getPdoConnection();
    $stmt = $pdo->prepare("SELECT COUNT(*) AS cnt FROM messages WHERE receiver_id=? AND is_read=0");
    $stmt->execute([$user_id]);
    $row = $stmt->fetch();
    return $row ? (int)$row['cnt'] : 0;
}

function deleteMessage($message_id, $user_id) {
    $pdo = getPdoConnection();
    $stmt = $pdo->prepare("DELETE FROM messages WHERE id=? AND sender_id=?");
    $stmt->execute([$message_id, $user_id]);
}

function getGlobalChatMessages() {
    $pdo = getPdoConnection();
    $stmt = $pdo->query("
        SELECT gc.*, p.name, u.account_id
        FROM global_chat gc
        LEFT JOIN profiles p ON gc.user_id = p.user_id
        LEFT JOIN users u ON gc.user_id = u.id
        ORDER BY gc.created_at ASC
    ");
    return $stmt->fetchAll();
}

function insertGlobalChatMessage($user_id, $message) {
    $pdo = getPdoConnection();
    $stmt = $pdo->prepare("INSERT INTO global_chat (user_id, message, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([$user_id, $message]);
}

function getProfileData($user_id) {
    $pdo = getPdoConnection();
    $stmt = $pdo->prepare("SELECT p.*, u.email, u.id as uid, u.account_id FROM profiles p JOIN users u ON p.user_id=u.id WHERE p.user_id=?");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}

function getJobStatusData($user_id) {
    $pdo = getPdoConnection();
    $stmt = $pdo->prepare("SELECT * FROM job_status WHERE user_id=?");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}
