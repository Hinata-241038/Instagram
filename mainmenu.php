<?php

session_start();

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");

    exit;

}

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
<h1>ようこそ <?= htmlspecialchars($_SESSION["username"]) ?> さん</h1>
<form method="post" action="upload.php" enctype="multipart/form-data">
<div class="form-group">
<textarea name="caption" placeholder="キャプションを入力..."></textarea>
</div>
<input type="file" name="image">
<button type="submit" class="upload-button">投稿する</button>
</form>
</div>
</body>
</html>

 