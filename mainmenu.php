<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instagram</title>
    <link rel="stylesheet" href="mainmenu_style.css">
</head>
<body>
    <div class="container">
        <?php
        session_start();

        // ログインしていない場合はログインページへリダイレクト
        if (!isset($_SESSION['user_id'])) { // user_idをセッションに保存するように変更
            header('Location: login.php');
            exit();
        }

        // データベース接続 (実際の認証情報に置き換えてください)
        $servername = "localhost";
        $username = "murakami"; //あなたのデータベースのユーザー名
        $password = "8701177";
        $dbname = "user_information"; // あなたのデータベース名

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $current_user_id = $_SESSION['user_id']; // セッションから現在のユーザーIDを取得
        
        // フォームがPOST送信された場合の処理
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo "<h1>投稿結果</h1>";

            $caption = isset($_POST['caption']) ? htmlspecialchars($_POST['caption']) : '';
            $image_path = '';

            // ファイルの処理
            if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
                $target_dir = "uploads/"; // アップロードされた画像を保存するディレクトリ
                // 'uploads' ディレクトリが存在しない場合は作成
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                $file_name = uniqid() . "_" . basename($_FILES['media']['name']); // 一意なファイル名
                $target_file = $target_dir . $file_name;
                $image_path = $target_file;

                if (move_uploaded_file($_FILES['media']['tmp_name'], $target_file)) {
                    echo "<p>ファイル <strong>" . htmlspecialchars($file_name) . "</strong> がアップロードされました。</p>";
                    // 画像プレビューの表示
                    echo "<p><img src='" . htmlspecialchars($image_path) . "' alt='Uploaded Image' style='max-width: 300px; height: auto;'></p>";

                    // データをデータベースに挿入
                    // 今のところuser_idを1と仮定; 実際のアプリケーションではセッションから取得します
                    $user_id = 1;
                    $stmt = $conn->prepare("INSERT INTO posts (user_id, image_path, caption) VALUES (?, ?, ?)");
                    $stmt->bind_param("iss", $user_id, $image_path, $caption);

                    if ($stmt->execute()) {
                        echo "<p>投稿が正常に保存されました。</p>";
                    } else {
                        echo "<p>データベースへの保存中にエラーが発生しました: " . $stmt->error . "</p>";
                    }
                    $stmt->close();

                } else {
                    echo "<p>ファイルのアップロード中にエラーが発生しました。</p>";
                }
            } else if (isset($_FILES['media']) && $_FILES['media']['error'] !== UPLOAD_ERR_NO_FILE) {
            switch ($_FILES['media']['error']) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            echo "<p>アップロードされたファイルが大きすぎます。</p>";
            break;
        case UPLOAD_ERR_PARTIAL:
            echo "<p>ファイルが完全にアップロードされませんでした。</p>";
            break;
        case UPLOAD_ERR_NO_FILE:
            echo "<p>ファイルが選択されませんでした。</p>"; // このケースはここでは通常発生しない
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
            echo "<p>一時フォルダが見つかりません。</p>";
            break;
        case UPLOAD_ERR_CANT_WRITE:
            echo "<p>ディスクへの書き込みに失敗しました。</p>";
            break;
        case UPLOAD_ERR_EXTENSION:
            echo "<p>PHPの拡張機能によってアップロードが中断されました。</p>";
            break;
        default:
            echo "<p>ファイルのアップロード中に不明なエラーが発生しました。エラーコード: " . $_FILES['media']['error'] . "</p>";
            break;
            }
        } else {
            echo "<p>ファイルが選択されませんでした。</p>";
        }
            echo "<p><strong>キャプション:</strong> " . nl2br($caption) . "</p>";
            echo '<p><a href="mainmenu.php">もう一度投稿する</a></p>';
            echo '<p><a href="plofile.php">プロフィール画面へ</a></p>';

        } else {
            // POSTリクエストではない場合（初回アクセス時など）はフォームを表示
        ?>
            <h1>新しい投稿</h1>
            <form action="mainmenu.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="media-upload" class="upload-button">
                        <span class="icon">&#x1F4F7;</span> 写真/動画を選択
                    </label>
                    <input type="file" id="media-upload" name="media" accept="image/*,video/*" style="display: none;">
                    <span id="file-name" class="file-name">選択されていません</span>
                </div>
                <div class="form-group">
                    <textarea id="caption" name="caption" rows="5" placeholder="キャプションを入力..."></textarea>
                </div>
                <button type="submit" class="submit-button">投稿する</button>
            </form>
        <?php
        }
        $conn->close();
        ?>
    </div>

    <script>
        document.getElementById('media-upload').addEventListener('change', function(event) {
            const fileNameSpan = document.getElementById('file-name');
            if (event.target.files.length > 0) {
                fileNameSpan.textContent = event.target.files[0].name;
            } else {
                fileNameSpan.textContent = '選択されていません';
            }
        });
    </script>
</body>
</html>