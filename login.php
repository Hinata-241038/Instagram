<?php
session_start();

// 固定のユーザー情報
$valid_username = "goto";
$valid_password = "12341234";

// ログイン処理
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input_user = $_POST["username"] ?? "";
    $input_pass = $_POST["password"] ?? "";

    if (isset($users[$input_user]) && $users[$input_user] === $input_pass) {
        // ログイン成功 → セッションに保存
        $_SESSION["username"] = $input_user;
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
        <h2>ログイン</h2>
        <form method="post">
            <div class="form-group">
        <label>ユーザー名</label>
        <input type="text" name="username" required>
    </div>
    <div class="form-group">
    <label>パスワード</label>
    <input type="password" name="password" required>
</div>
<?php if(!empty($error)): ?>
    <p class= "error-message" style = "display:block;"><?=htmlspecialchars($error)?></p>
        <?php endif; ?>
        <div class = "button-group">
            <button type = "submit" class = "login-button">ログイン</button>
            <a href="register.php" class = "register-button">新規登録></a>
            </div>
        </form>
    </div>
</body>
</html>