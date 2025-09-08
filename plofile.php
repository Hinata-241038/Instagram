<?php
session_start();

// ログインしていなければ login.php にリダイレクト
if (!isset($_SESSION["username"]) || !isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
$username = $_SESSION["username"];
$user_id = $_SESSION["user_id"];

// データベース接続
$servername = "localhost";
$password = "";
$db_username = "root"; 
$dbname = "user_information"; 

$conn = new mysqli($servername, $db_username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ユーザーのプロフィール情報を取得
$self_introduction = "自己紹介文が設定されていません。";
$avatar_path = "placeholder_avatar.jpg";
$post_count = 0;

// ★修正: user_idを使ってプロフィール情報を取得
$stmt_profile = $conn->prepare("SELECT self_introduction, avatar_path FROM profile_1 WHERE user_id = ?"); 
$stmt_profile->bind_param("i", $user_id); 
$stmt_profile->execute();
$result_profile = $stmt_profile->get_result();

if ($result_profile->num_rows > 0) {
    $profile_data = $result_profile->fetch_assoc();
    $self_introduction = !empty($profile_data['self_introduction']) ? nl2br(htmlspecialchars($profile_data['self_introduction'])) : "自己紹介文が設定されていません。";
    $avatar_path = !empty($profile_data['avatar_path']) ? htmlspecialchars($profile_data['avatar_path'], ENT_QUOTES, 'UTF-8') : "placeholder_avatar.jpg";
}
$stmt_profile->close();

// 投稿数を取得
$stmt_posts_count = $conn->prepare("SELECT COUNT(*) AS post_count FROM posts WHERE user_id = ?");
$stmt_posts_count->bind_param("i", $user_id); 
$stmt_posts_count->execute();
$result_posts_count = $stmt_posts_count->get_result();
if ($result_posts_count->num_rows > 0) {
    $row_posts_count = $result_posts_count->fetch_assoc();
    $post_count = $row_posts_count['post_count'];
}
$stmt_posts_count->close();

// データベースから投稿を取得
$stmt_posts = $conn->prepare("SELECT id, image_path, caption FROM posts WHERE user_id = ? ORDER BY created_at DESC");
$stmt_posts->bind_param("i", $user_id);
$stmt_posts->execute();
$result = $stmt_posts->get_result();
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
                    <img src="<?php echo $avatar_path; ?>" alt="アカウントアイコン" class="profile-avatar">
                </div>
                <div class="profile-stats">
                    <h1><?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?></h1>
                    <div class="stats-numbers">
                        <div><strong><?php echo $post_count; ?></strong> 投稿</div>
                        <div><strong>500</strong> フォロワー</div>
                        <div><strong>300</strong> フォロー中</div>
                    </div>
                    <p class="profile-bio">
                        <?php echo $self_introduction; ?>
                    </p>
                    <a href="#" class="nav-button">プロフィールを編集</a>
                </div>
            </div>
        </header>

        <form action="delete_posts.php" method="post" onsubmit="return confirm('選択した投稿を本当に削除しますか？');">
            <div class="profile-posts-grid">
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<div class='post-item'>";
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
            <button type="submit" class="delete-button nav-button">選択した投稿を削除</button>
        </form>

        <div class="nav-buttons">
            <a href="mainmenu.php" class="nav-button">戻る</a>
        </div>
    </div>
</body>
</html>