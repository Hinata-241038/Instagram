<?php
session_start();
 
// エラーメッセージを初期化
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // DB接続設定
    $host = '%';
    $dbname = 'user_information';
    $db_user = 'murakami';   // ←適宜変更してください
    $db_pass = '8701177';   // ←適宜変更してください
 
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // エラーモードを設定
    } catch (PDOException $e) {
        exit('DB接続エラー: ' . $e->getMessage());
    }
 
    $username = $_POST['username'];
    $password = $_POST['password'];
 
    // パスワードをハッシュ化（本番環境では password_hash/verify を推奨）
    $hashed_password = hash('sha256', $password);
    

    $sql = "SELECT id, username FROM users WHERE username = :username AND password = :password"; // idも取得するように変更
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->execute();
 
    $user = $stmt->fetch(PDO::FETCH_ASSOC); // 連想配列として取得

    if ($user) {
        $_SESSION['username'] = $user['username']; // データベースから取得したユーザー名を使用
        $_SESSION['user_id'] = $user['id']; // ユーザーのIDをセッションに保存
        header('Location: mainmenu.php');
        exit;
    } else {
        $error = "ユーザ名またはパスワードが間違っています。";
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン画面</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="login-box">
        <h2>Login</h2>
        <form action="mainmenu.php" method="post">
            <div class="user-box">
                <input type="text" name="username" required>
                <label>ユーザー名</label>
            </div>
            <div class="user-box">
                <input type="password" name="password" required>
                <label>パスワード</label>
            </div>
            <center>
                <button type="submit" class="login-button">ログイン</button>
            </center>
        </form>
    </div>
</body>
</html>
