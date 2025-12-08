<?php
session_start();
require('connect-db.php');
require('comment-db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$comment_id = $_POST['comment_id'] ?? null;

if ($comment_id) {
    deleteComment($comment_id, $user_id);
}

// Try to redirect back to referrer or dashboard
$back = $_SERVER['HTTP_REFERER'] ?? 'dashboard.php';
header('Location: ' . $back);
exit();

?>
