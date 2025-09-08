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
    <style>
        /* 全体のスタイル */
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #e0f2fe; /* 淡い水色 */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* コンテナボックスのスタイル */
        .login-container {
            background-color: #ffffff;
            padding: 40px 50px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }

        /* タイトルのスタイル */
        .login-title {
            font-size: 2rem;
            font-weight: 600;
            color: #1a569c;
            margin-bottom: 25px;
        }
        
        /* フォームのスタイル */
        .login-form div {
            margin-bottom: 20px;
            text-align: left;
        }

        /* ラベルのスタイル */
        .login-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }

        /* 入力フィールドのスタイル */
        .login-form input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 1rem;
        }
        
        /* ボタンのコンテナスタイル */
        .button-container {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            margin-top: 25px;
        }

        /* ボタン共通のスタイル */
        .button-container button,
        .button-container a {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            font-weight: bold;
        }

        /* 登録ボタンのスタイル */
        .button-container button {
            background-color: #007bff;
            color: #fff;
            transition: background-color 0.3s ease;
        }

        .button-container button:hover {
            background-color: #0056b3;
        }

        /* 戻る（リンク）ボタンのスタイル */
        .button-container a {
            background-color: #e9ecef;
            color: #333;
            transition: background-color 0.3s ease;
        }

        .button-container a:hover {
            background-color: #cfd8dc;
        }

        /* メッセージ表示エリアのスタイル */
        .message {
            margin-bottom: 15px;
            color: #dc3545;
        }
    </style>
</head>
<body>
    <h2>New user Register</h2>

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

    <div style="margin-top: 20px;">
        <a href="login.php">戻る</a>
    </div>
</body>
</html>