<?php
session_start();
 
// DB接続
$dsn = 
"mysql:host=localhost;dbname=insta?app;charset=utf8mb4";
$db_user = "root";
$db_pass = "";
    try {
        $pdo = new PDO($dsn, $db_user,$db_pass); //
    } catch (PDOException $e) {
        die("DB接続失敗:".$e->getMessage());
    }
// ログイン処理
if ($_SERVER["REQUEST_METHOD"]==="PSOT") {
    $username = $POST["username"]??"";
    $password = $POST["password"]??"";

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user["password"])) {
        // セッションに保存して遷移
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];
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
    <input type="password" name = "password" required>
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