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