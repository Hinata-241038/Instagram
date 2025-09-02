<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION["user_id"];
$username = $_SESSION["username"];

// データベース接続
$servername = "localhost";
$password = "";
$db_username = "root"; 
$dbname = "user_information"; 

$conn = new mysqli($servername, $db_username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$new_bio = $_POST['bio'];
$new_avatar_path = "";

// 現在のプロフィール情報を取得して、古いアバターパスを確認
$stmt_get_avatar = $conn->prepare("SELECT avatar_path FROM profile WHERE user_name = ?");
$stmt_get_avatar->bind_param("s", $username);
$stmt_get_avatar->execute();
$result_get_avatar = $stmt_get_avatar->get_result();
$current_avatar_row = $result_get_avatar->fetch_assoc();
$current_avatar_path = $current_avatar_row['avatar_path'];
$stmt_get_avatar->close();

// アバター画像がアップロードされた場合
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == UPLOAD_ERR_OK) {
    $target_dir = "avatars/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $file_extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
    $new_avatar_filename = $user_id . '_' . time() . '.' . $file_extension;
    $new_avatar_path = $target_dir . $new_avatar_filename;

    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $new_avatar_path)) {
        // 古いアバター画像を削除
        if (!empty($current_avatar_path) && file_exists($current_avatar_path)) {
            unlink($current_avatar_path);
        }
    } else {
        echo "アバター画像のアップロードに失敗しました。";
        exit;
    }
} else {
    // 新しい画像がアップロードされなかった場合、既存のパスを使用
    $new_avatar_path = $current_avatar_path;
}

// データベースを更新
$stmt_update = $conn->prepare("UPDATE profile SET self_introduction = ?, avatar_path = ? WHERE user_name = ?");
$stmt_update->bind_param("sss", $new_bio, $new_avatar_path, $username);

if ($stmt_update->execute()) {
    // 更新成功
    header("Location: plofile.php");
    exit;
} else {
    echo "プロフィールの更新に失敗しました: " . $stmt_update->error;
}

$stmt_update->close();
$conn->close();
?>