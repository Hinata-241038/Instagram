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
                    <img src="<?php echo htmlspecialchars(isset($profile_avatar_url) ? $profile_avatar_url : 'placeholder_avatar.jpg'); ?>" alt="アカウントアイコン" class="profile-avatar">
                </div>
                <div class="profile-stats">
                    <h1><?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?></h1>
                    <div class="stats-numbers">
                        <div><strong><?php echo htmlspecialchars(isset($post_count) ? $post_count : 0); ?></strong> 投稿</div>
                        <div><strong>500</strong> フォロワー</div>
                        <div><strong>300</strong> フォロー中</div>
                    </div>
                    <p class="profile-bio">
                        <?php echo isset($profile_bio) ? $profile_bio : '自己紹介文が設定されていません。'; ?>
                    </p>
                </div>
            </div>
        </header>

<div class="profile-posts-grid">
            <?php
            session_start(); // セッション開始

            // ログインしていなければ login.php にリダイレクト
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}
$username = $_SESSION["username"];
?>
<?php
            // データベース接続 (ここから下は変更なしでOK)
            $servername = "localhost";
            $password = "";
            $username = "root"; 
            $dbname = "user_information"; 

            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // --- 変数の初期化 --- (これはそのまま残す)
            $user_name = "ユーザー名が見つかりません";
            $profile_bio = "自己紹介文が設定されていません。";
            $profile_avatar_url = "placeholder_avatar.jpg";
            $post_count = 0;

            // --- IMPORTANT: 以下の値を、データベースに実際に存在するレコードの値に設定してください ---
            // ★ここをphpMyAdminで確認した、profileテーブルの'user_name'に確実に合わせる！
            $current_profile_user_name = "ここに実際のuser_nameを入力"; 

            // ★ここをphpMyAdminで確認した、postsテーブルの'user_id'に確実に合わせる！
            $current_posts_user_id = 1; 

            // ユーザーのプロフィール情報を取得
            $stmt_profile = $conn->prepare("SELECT name, user_name, gender, self_introduction, link FROM profile WHERE user_name = ?"); 
            $stmt_profile->bind_param("s", $current_profile_user_name); 
            $stmt_profile->execute();
            $result_profile = $stmt_profile->get_result();

            if ($result_profile->num_rows > 0) {
                $profile_data = $result_profile->fetch_assoc();
                $user_name = htmlspecialchars($profile_data['user_name']); 
                $profile_bio = !empty($profile_data['self_introduction']) ? nl2br(htmlspecialchars($profile_data['self_introduction'])) : "自己紹介文が設定されていません。";
                // アバターURLの取得ロジック（もしDBにカラムがあれば）
                // if (isset($profile_data['avatar_path']) && !empty($profile_data['avatar_path'])) {
                //     $profile_avatar_url = htmlspecialchars($profile_data['avatar_path']);
                // }
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
            $stmt_posts = $conn->prepare("SELECT image_path, caption FROM posts WHERE user_id = ? ORDER BY created_at DESC");
            $stmt_posts->bind_param("i", $current_posts_user_id);
            $stmt_posts->execute();
            $result = $stmt_posts->get_result();

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<div class='post-item'>";
                    echo "<img src='" . htmlspecialchars($row["image_path"]) . "' alt='投稿画像'>";
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
        <!-- ナビゲーションボタン -->
    <div class="nav-buttons">
        <a href="mainmenu.php" class="nav-button">戻る</a>
    </div>
    </div>
</body>
</html>