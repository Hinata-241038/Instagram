<?php
session_start();
 
// エラーメッセージを初期化
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // DB接続設定
    $host = 'localhost';
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン画面</title>
    <link rel="stylesheet" href="login_style.css">
</head>
<body>
    <div class="login-container">
        <h2>ログイン画面</h2>
        <?php if (!empty($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
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