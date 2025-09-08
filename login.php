<?php
session_start();

// 固定のユーザー情報
$valid_username = "goto";
$valid_password = "12341234";

// ログイン処理
// 1. POSTメソッドのスペルミスを修正
if ($_SERVER["REQUEST_METHOD"]==="POST") {
    // 2. $_POST変数のスペルミスを修正
    $username = $_POST["username"]??"";
    $password = $_POST["password"]??"";

    if ($username === $valid_username && $password === $valid_password) {
        // セッションに保存して遷移
        $_SESSION["user_id"] = 1;
        $_SESSION["username"] = $valid_username;
        header("Location: mainmenu.php");
        exit;
    } else {
        $error = "ユーザー名またはパスワードが違います。";
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン</title>
    <link rel="stylesheet" href="login_style.css">
</head>
<body>
    <div class="login-container">
        <h2>イソフ、夕々”ラム</h2>
        <form method="post">
            <div class="form-group">
        <label>ユーザー名</label>
        <input type="text" name="username" required>
    <div class="form-group">
    <label>パスワード</label>
    <input type="password" name="password" required>
</div>
</div>
<?php if(!empty($error)): ?>
    <p class= "error-message" style = "display:block;"><?=htmlspecialchars($error)?></p>
        <?php endif; ?>
        <div class = "button-group">
            <button type = "submit" class = "login-button">ログイン</button>
            <a href="register.php" class = "register-button">新規登録</a>
            </div>
        </form>
    </div>
</body>
</html>