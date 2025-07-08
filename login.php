<?php
session_start();
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // DB接続設定
    $host = 'localhost';
    $dbname = 'user_information';
    $db_user = 'murakami';   // ←適宜変更してください
    $db_pass = '8701177';   // ←適宜変更してください
 
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_user, $db_pass);
    } catch (PDOException $e) {
        exit('DB接続エラー: ' . $e->getMessage());
    }
 
    $username = $_POST['username'];
    $password = $_POST['password'];
 
    // パスワードをハッシュ化（本番環境では password_hash/verify を推奨）
    $hashed_password = hash('sha256', $password);
 
    $sql = "SELECT * FROM users WHERE username = :username AND password = :password";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->execute();
 
    $user = $stmt->fetch();
   
    if ($user) {
        $_SESSION['username'] = $username;
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン画面</title>
    <link rel="stylesheet" href="login_style.css">
</head>
<body>
    <div class="login-container">
        <h2>ログイン画面</h2>
        <form action="#" method="post">
            <div class="form-group">
                <label for="username">ユーザーネーム</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">パスワード</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="button-group">
                <button type="submit" class="login-button">ログイン</button>
                <a href="#" class="register-button">新規登録</a>
            </div>
        </form>
    </div>
</body>
</html>
 