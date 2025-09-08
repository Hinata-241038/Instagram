<?php
// 1. データベース接続情報の設定
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_information";

$message = ""; // ユーザーへのメッセージを保存する変数

// 2. フォームからデータが送信されたか確認
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_username = $_POST['username'];
    $input_password = $_POST['password'];

    try {
        // 3. データベースに接続
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 4. 入力値の検証（ユーザー名が既に存在するかチェック）
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$input_username]);
        if ($stmt->fetchColumn() > 0) {
            $message = "このユーザー名は既に使われています。別のユーザー名をお試しください。";
        } else {
            // 5. パスワードのハッシュ化
            $hashed_password = password_hash($input_password, PASSWORD_DEFAULT);

            // 6. ユーザー情報をデータベースに挿入
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->execute([$input_username, $hashed_password]);

            $message = "ユーザー登録が完了しました！";
        }
    } catch(PDOException $e) {
        $message = "エラーが発生しました: " . $e->getMessage();
    }
    // データベース接続を閉じる
    $conn = null;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ユーザー登録</title>
</head>
<body>
    <h2>新規ユーザー登録</h2>

    <?php if (!empty($message)): ?>
        <p style="color: red;"><?php echo $message; ?></p>
    <?php endif; ?>

    <form action="register.php" method="post">
        <div>
            <label for="username">ユーザー名:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div>
            <label for="password">パスワード:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">登録</button>
    </form>
</body>
</html>