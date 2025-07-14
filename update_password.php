<?php

// DB接続設定
$host = 'localhost'; // あなたの環境に合わせて変更
$dbname = 'user_information'; // あなたのDB名
$db_user = 'murakami';   // DB接続用ユーザー名
$db_pass = '8701177';    // DB接続用パスワード (phpMyAdminログインに使っているパスワード)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "DB接続成功。<br>";
} catch (PDOException $e) {
    exit('DB接続エラー: ' . $e->getMessage());
}

// 更新したいユーザー名と、そのユーザーの新しい生のパスワード
$target_username = 'murakami';
$new_plain_password = 'your_new_secure_password'; // ★★★ ここにmurakamiユーザーでログインしたいパスワードを入力 ★★★

// パスワードを安全にハッシュ化
$hashed_password_for_db = password_hash($new_plain_password, PASSWORD_DEFAULT);

// データベースを更新するSQL
$sql = "UPDATE users SET password = :password WHERE username = :username";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':password', $hashed_password_for_db);
$stmt->bindParam(':username', $target_username);

try {
    $stmt->execute();
    echo "ユーザー '{$target_username}' のパスワードを安全な形式に更新しました。<br>";
    echo "新しいハッシュ: " . htmlspecialchars($hashed_password_for_db) . "<br>";
} catch (PDOException $e) {
    echo "パスワード更新エラー: " . $e->getMessage() . "<br>";
}

// 実行後、このファイルをWebサーバーから削除するか、内容をコメントアウトしてください。

?>