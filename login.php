<?php
session_start();
 
$error = "";

// PHPエラー表示設定（デバッグ時のみ推奨、本番環境ではオフにするかログに出力）
ini_set('display_errors', 1); //
ini_set('display_startup_errors', 1); //
error_reporting(E_ALL); //

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // DB接続設定
    // ホスト名が localhost なら 'localhost' または '127.0.0.1' にする
    $host = 'localhost'; // あなたの環境に合わせて変更
    $dbname = 'user_information'; // あなたのDB名
    $db_user = 'murakami';   // DB接続用ユーザー名 (phpMyAdminで設定したユーザー)
    // ★★★ここを、phpMyAdminでmurakami@localhostユーザーに設定したパスワードに修正します★★★
    $db_pass = '8701177';   // 
    // 例: $db_pass = 'myDBUserPass123'; 
 
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_user, $db_pass); //
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // エラーモードを設定
    } catch (PDOException $e) {
        // DB接続エラーが発生した場合、このメッセージが表示されます
        exit('DB接続エラー: ' . $e->getMessage()); //
    }
 
    $username_input = $_POST['username']; // ユーザーがフォームに入力したユーザー名
    $password_input = $_POST['password']; // ユーザーがフォームに入力した生のパスワード

    // まず、入力されたユーザー名でデータベースからユーザー情報を取得
    // パスワードカラムのハッシュ値も取得する
    $sql = "SELECT id, username, password FROM users WHERE username = :username"; //
    $stmt = $pdo->prepare($sql); //
    $stmt->bindParam(':username', $username_input); //
    $stmt->execute(); //
 
    $user = $stmt->fetch(PDO::FETCH_ASSOC); // 連想配列として取得

    // ユーザーが見つかり、かつ入力パスワードと保存されているハッシュが一致するか検証
    // ここでpassword_verify()を使用
    if ($user && password_verify($password_input, $user['password'])) { //
        // ログイン成功！
        $_SESSION['username'] = $user['username']; // データベースから取得したユーザー名を使用
        $_SESSION['user_id'] = $user['id']; // ユーザーのIDをセッションに保存
        header('Location: mainmenu.php'); // ログイン成功後のリダイレクト先
        exit; //
    } else {
        // ログイン失敗
        $error = "パスワードが間違っています。"; // エラーメッセージを修正
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン画面</title>
    <link rel="stylesheet" href="login_style.css"> 
</head>
<body>
    <div class="login-box">
        <h2>Login</h2>
        <?php if (!empty($error)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <div class="user-box">
                <input type="text" name="username" required>
                <label>ユーザー名</label>
            </div>
            <div class="user-box">
                <input type="password" name="password" required>
                <label>パスワード</label>
            </div>
            <button type="submit" class="login-button">ログイン</button>
        </form>
    </div>
</body>
</html>