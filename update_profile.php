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
$new_avatar_path = null; // 初期値をnullに設定

// 現在のプロフィール情報を取得して、古いアバターパスを確認
$stmt_get_profile = $conn->prepare("SELECT id, avatar_path FROM profile_1 WHERE id = ?");
$stmt_get_profile->bind_param("s", $username);
$stmt_get_profile->execute();
$result_get_profile = $stmt_get_profile->get_result();
$current_profile_row = $result_get_profile->fetch_assoc();

if ($current_profile_row) {
    $current_avatar_path = $current_profile_row['avatar_path'];
    $profile_exists = true;
} else {
    $current_avatar_path = null;
    $profile_exists = false;
}
$stmt_get_profile->close();

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
    // ★ 修正箇所
    $new_avatar_path = $current_avatar_path;
    if ($new_avatar_path === null) {
        $new_avatar_path = '';
    }
}

// データベースにプロフィール情報を保存または更新
if ($profile_exists) {
    // 既存のプロフィールを更新
    $stmt_update = $conn->prepare("UPDATE profile_1 SET self_introduction = ?, avatar_path = ? WHERE id = ?");
    $stmt_update->bind_param("sss", $new_self_introduction, $new_avatar_path, $id);

    if ($stmt_update->execute()) {
        header("Location: plofile.php");
        exit;
    } else {
        echo "プロフィールの更新に失敗しました: " . $stmt_update->error;
    }
    $stmt_update->close();
} else {
    // プロフィールを新規作成
    $stmt_insert = $conn->prepare("INSERT INTO profile_1 (user_id, id, self_introduction, avatar_path) VALUES (?, ?, ?, ?)");
    $stmt_insert->bind_param("isss", $user_id, $id, $new_self_introduction, $new_avatar_path);

    if ($stmt_insert->execute()) {
        header("Location: plofile.php");
        exit;
    } else {
        echo "プロフィールの新規作成に失敗しました: " . $stmt_insert->error;
    }
    $stmt_insert->close();
}

$conn->close();
?>