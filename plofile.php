<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION["user_id"]; // ★ここを修正！セッションからuser_idを取得
?>

<!DOCTYPE html>
<html lang="ja">
...
<body>
...
<div class="profile-posts-grid">
<?php
// データベース接続
$servername = "localhost";
$password = "";
$db_username = "root";
$dbname = "user_information";

$conn = new mysqli($servername, $db_username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- プロフィール情報の取得 (必要に応じて修正) ---
// user_nameではなく、user_idでプロフィール情報を取得するのがより確実です
$stmt_profile = $conn->prepare("SELECT name, user_name, gender, self_introduction FROM users WHERE id = ?");
$stmt_profile->bind_param("i", $user_id);
$stmt_profile->execute();
$result_profile = $stmt_profile->get_result();
$profile_data = $result_profile->fetch_assoc();

// 投稿数を取得
$stmt_posts_count = $conn->prepare("SELECT COUNT(*) AS post_count FROM posts WHERE user_id = ?");
$stmt_posts_count->bind_param("i", $user_id); // ★ここを修正！
$stmt_posts_count->execute();
$result_posts_count = $stmt_posts_count->get_result();
if ($result_posts_count->num_rows > 0) {
    $row_posts_count = $result_posts_count->fetch_assoc();
    $post_count = $row_posts_count['post_count'];
}
$stmt_posts_count->close();

// データベースから投稿を取得（現在のユーザーの投稿のみ）
$stmt_posts = $conn->prepare("SELECT image_path, caption FROM posts WHERE user_id = ? ORDER BY created_at DESC");
$stmt_posts->bind_param("i", $user_id); // ★ここを修正！
$stmt_posts->execute();
$result = $stmt_posts->get_result();
// ... 投稿のループ表示 ...
?>