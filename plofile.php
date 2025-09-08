<?php
session_start();

// ログインしていなければ login.php にリダイレクト
if (!isset($_SESSION["username"]) || !isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
$username = $_SESSION["username"];
$user_id = $_SESSION["user_id"]; // セッションからuser_idを取得
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロフィール画面</title>
    <link rel="stylesheet" href="plofile_style.css">
</head>
<body>
    <div class="container">
        <header class="profile-header">
            <div class="profile-info">
                <div class="profile-avatar-container">
                    <img src="<?php echo htmlspecialchars(isset($avatar_path) ? $avatar_path : 'placeholder_avatar.jpg'); ?>" alt="アカウントアイコン" class="profile-avatar">
                </div>
                <div class="profile-stats">
                    <h1><?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?></h1>
                    <div class="stats-numbers">
                        <div><strong><?php echo htmlspecialchars(isset($post_count) ? $post_count : 0); ?></strong> 投稿</div>
                        <div><strong>500</strong> フォロワー</div>
                        <div><strong>300</strong> フォロー中</div>
                    </div>
                    <p class="profile-bio">
                        <?php echo isset($self_introduction) ? $self_introduction : '自己紹介文が設定されていません。'; ?>
                    </p>
                    <a href="edit_profile.php" class="edit-profile-button">プロフィールを編集</a>
                </div>
            </div>
        </header>

<form action="delete_posts.php" method="post" onsubmit="return confirm('選択した投稿を本当に削除しますか？');">
<div class="profile-posts-grid">
<?php
            // データベース接続
            $servername = "localhost";
            $password = "";
            $db_username = "root"; 
            $dbname = "user_information"; 

            $conn = new mysqli($servername, $db_username, $password, $dbname);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // --- 変数の初期化 ---
            $user_name = "ユーザー名が見つかりません";
            $profile_bio = "自己紹介文が設定されていません。";
            $profile_avatar_url = "placeholder_avatar.jpg";
            $post_count = 0;

            // ★ ログイン中のユーザーIDとユーザー名を使用
            $current_profile_user_name = $username; 
            $current_posts_user_id = $user_id; 

            // ユーザーのプロフィール情報を取得
            $stmt_profile = $conn->prepare("SELECT id, id, user_id, self_introduction, avatar_path, created_at, update_at FROM profile_1 WHERE id = ?"); 
            $stmt_profile->bind_param("s", $current_profile_user_name); 
            $stmt_profile->execute();
            $result_profile = $stmt_profile->get_result();

            if ($result_profile->num_rows > 0) {
                $profile_data = $result_profile->fetch_assoc();
                $user_name = htmlspecialchars($profile_data['username']); 
                $profile_bio = !empty($profile_data['self_introduction']) ? nl2br(htmlspecialchars($profile_data['self_introduction'])) : "自己紹介文が設定されていません。";
            }
            $stmt_profile->close();

            // 投稿数を取得
            $stmt_posts_count = $conn->prepare("SELECT COUNT(*) AS post_count FROM posts WHERE user_id = ?");
            $stmt_posts_count->bind_param("i", $current_posts_user_id); 
            $stmt_posts_count->execute();
            $result_posts_count = $stmt_posts_count->get_result();
            if ($result_posts_count->num_rows > 0) {
                $row_posts_count = $result_posts_count->fetch_assoc();
                $post_count = $row_posts_count['post_count'];
            }
            $stmt_posts_count->close();

            // データベースから投稿を取得（現在のユーザーの投稿のみ）
            // ★ idカラムも取得するように変更
            $stmt_posts = $conn->prepare("SELECT id, image_path, caption FROM posts WHERE user_id = ? ORDER BY created_at DESC");
            $stmt_posts->bind_param("i", $current_posts_user_id);
            $stmt_posts->execute();
            $result = $stmt_posts->get_result();

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<div class='post-item'>";
                    // ★ チェックボックスを追加
                    echo "<img src='" . htmlspecialchars($row["image_path"]) . "' alt='投稿画像'>";
                    echo "<input type='checkbox' name='post_ids[]' value='" . $row['id'] . "' class='delete-checkbox'>";
                    
                    if (!empty($row["caption"])) {
                        echo "<div class='post-caption'>" . nl2br(htmlspecialchars($row["caption"])) . "</div>";
                    }
                    echo "</div>";
                }
            } else {
                echo "<p>まだ投稿がありません。</p>";
            }

            $stmt_posts->close();
            $conn->close();
            ?>
        </div>
        <button type="submit" class="delete-button">選択した投稿を削除</button>
</form>

    <div class="nav-buttons">
        <a href="mainmenu.php" class="nav-button">戻る</a>
    </div>
    </div>
</body>
</html>