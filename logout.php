<?php
session_start();

// セッション変数を全て解除
$_SESSION = [];

// セッションを完全に破棄
session_destroy();

// ログイン画面にリダイレクト
header("Location: login.php");
exit;
