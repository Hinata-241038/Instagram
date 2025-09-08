<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
$username = $_SESSION["username"];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>Main Menu</title>
<link rel="stylesheet" href="mainmenu_style.css">
</head>
<body>
<div class="container">
<h1>ようこそ <?= htmlspecialchars($_SESSION["username"], ENT_QUOTES, 'UTF-8') ?> さん</h1>
<form method="post" action="upload.php" enctype="multipart/form-data">
<div class="form-group">
<textarea name="caption" placeholder="キャプションを入力..."></textarea>
</div>
<input type="file" name="image">
<button type="submit" class="upload-button">投稿する</button>
</form>

<!-- ナビゲーションボタン -->
    <div class="menu-buttons">
        <a href="logout.php" class="menu-button">ログアウト</a>
        
        <a href="plofile.php" class="menu-button">マイプロフィール</a>
    </div>

</div>
</body>
</html>