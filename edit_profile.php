<?php
session_start();

// ログインしていなければ login.php にリダイレクト
if (!isset($_SESSION["username"]) || !isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
$username = $_SESSION["username"];
$user_id = $_SESSION["user_id"];

// データベース接続
$servername = "localhost";
$password = "";
$db_username = "root"; 
$dbname = "user_information"; 

$conn = new mysqli($servername, $db_username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 現在のプロフィール情報を取得
$current_bio = "";
$current_avatar_url = "placeholder_avatar.jpg";

$stmt = $conn->prepare("SELECT self_introduction, avatar_path FROM profile_1 WHERE user_name = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $current_bio = htmlspecialchars($row['self_introduction']);
    if (!empty($row['avatar_path'])) {
        $current_avatar_url = htmlspecialchars($row['avatar_path']);
    }
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロフィール編集</title>
    <link rel="stylesheet" href="plofile_style.css">
</head>
<body>
    <div class="container">
        <h1>プロフィールを編集</h1>
        <form action="update_profile.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="avatar">アバター画像:</label>
                <img src="<?= $current_avatar_url ?>" alt="現在のアバター" class="profile-avatar-edit">
                <input type="file" name="avatar" id="avatar">
            </div>
            <div class="form-group">
                <label for="self_introduction">自己紹介文:</label>
                <textarea name="self_introduction" id="self_introduction" rows="5"><?= $current_bio ?></textarea>
            </div>
            <button type="submit" class="update-button">更新</button>
        </form>
        <div class="nav-buttons">
            <a href="plofile.php" class="nav-button">戻る</a>
        </div>
    </div>
</body>
</html>