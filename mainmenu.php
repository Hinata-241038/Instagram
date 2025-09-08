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
<div class="flex-buttons">
<label for="file-upload" class="menu-button">ファイルを選択</label>
<input id="file-upload" type="file" name="image" style="display: none;">
<button type="submit" class="menu-button">投稿する</button>
<div class="flex-buttons">
</div>
<span id="file-name" class="file-name">選択されていません</span>
</form>

<!-- ナビゲーションボタン -->
    <div class="menu-buttons">
        <a href="logout.php" class="menu-button">ログアウト</a>        
        <a href="plofile.php" class="menu-button">マイプロフィール</a>
    </div>

<script>
document.getElementById('file-upload').addEventListener('change', function() {
const fileName = this.files[0] ? this.files[0].name : '選択されていません';
document.getElementById('file-name').textContent = fileName;
});
</script>
</div>
</body>
</html>