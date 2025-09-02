<?php
session_start();

// ログインしていない場合はリダイレクト
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// データベース接続情報
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "user_information";

// データベース接続
$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// フォームからデータ取得
$user_id = $_SESSION["user_id"]; // セッションからuser_idを取得
$caption = $_POST["caption"];

// アップロードされたファイルの処理
$target_dir = "uploads/"; // 画像を保存するディレクトリ
$image_path = "";
if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // ファイルのバリデーション (例: 画像ファイルのみ許可)
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check !== false) {
        // ディレクトリが存在しない場合は作成
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        // ファイルを移動
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        } else {
            echo "画像のアップロードに失敗しました。";
        }
    } else {
        echo "アップロードされたファイルは画像ではありません。";
    }
}

// データベースに投稿を保存
if (!empty($image_path)) {
    $stmt = $conn->prepare("INSERT INTO posts (user_id, image_path, caption, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $user_id, $image_path, $caption);

    if ($stmt->execute()) {
        echo "投稿が完了しました！";
    } else {
        echo "投稿の保存に失敗しました: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "画像が選択されていません。";
}

$conn->close();

// 投稿後にメインメニューに戻る
header("Location: mainmenu.php");
exit;
?>