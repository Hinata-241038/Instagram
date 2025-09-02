<?php
// ---------------------------
// plofile.php
// ---------------------------
session_start();

// ログインチェック（先頭で必ず行う）
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
$login_username = $_SESSION['username'];
$login_user_id  = $_SESSION['user_id'] ?? null; // login で user_id を set していれば利用

// 表示用の初期値
$display_name       = $login_username;
$profile_bio        = "自己紹介文が設定されていません。";
$profile_avatar_url = "placeholder_avatar.jpg";
$post_count         = 0;
$posts_result       = null;

// DBを使う場合の設定（必要なければ $use_db を false に）
$use_db = true;
if ($use_db) {
    $db_host = "localhost";
    $db_user = "root";       // DB接続用の変数名（$username と衝突させない）
    $db_pass = "";
    $db_name = "user_information";

    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");

    // プロフィール取得（user_id があれば優先）
    if ($login_user_id !== null) {
        $stmt = $conn->prepare("SELECT name, user_name, gender, self_introduction, link, avatar_path FROM profile WHERE user_id = ? LIMIT 1");
        $stmt->bind_param("i", $login_user_id);
    } else {
        $stmt = $conn->prepare("SELECT name, user_name, gender, self_introduction, link, avatar_path FROM profile WHERE user_name = ? LIMIT 1");
        $stmt->bind_param("s", $login_username);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        if (!empty($row['user_name'])) {
            $display_name = $row['user_name'];
        }
        if (!empty($row['self_introduction'])) {
            // 表示用にエスケープ＋改行対応
            $profile_bio = nl2br(htmlspecialchars($row['self_introduction'], ENT_QUOTES, 'UTF-8'));
        }
        if (!empty($row['avatar_path'])) {
            $profile_avatar_url = $row['avatar_path'];
        }
    }
    $stmt->close();

    // 投稿数取得
    if ($login_user_id !== null) {
        $stmt = $conn->prepare("SELECT COUNT(*) AS post_count FROM posts WHERE user_id = ?");
        $stmt->bind_param("i", $login_user_id);
    } else {
        $stmt = $conn->prepare("SELECT COUNT(*) AS post_count FROM posts WHERE user_name = ?");
        $stmt->bind_param("s", $login_username);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    if ($r = $res->fetch_assoc()) {
        $post_count = (int)$r['post_count'];
    }
    $stmt->close();

    // 投稿一覧取得
    if ($login_user_id !== null) {
        $stmt = $conn->prepare("SELECT image_path, caption FROM posts WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $login_user_id);
    } else {
        $stmt = $conn->prepare("SELECT image_path, caption FROM posts WHERE user_name = ? ORDER BY created_at DESC");
        $stmt->bind_param("s", $login_username);
    }
    $stmt->execute();
    $posts_result = $stmt->get_result();
    // stmt はループ後に閉じます（下で）
}
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
                    <img src="<?php echo htmlspecialchars($profile_avatar_url, ENT_QUOTES, 'UTF-8'); ?>" alt="アカウントアイコン" class="profile-avatar">
                </div>
                <div class="profile-stats">
                    <h1><?php echo htmlspecialchars($display_name, ENT_QUOTES, 'UTF-8'); ?></h1>
                    <div class="stats-numbers">
                        <div><strong><?php echo (int)$post_count; ?></strong> 投稿</div>
                        <div><strong>500</strong> フォロワー</div>
                        <div><strong>300</strong> フォロー中</div>
                    </div>
                    <p class="profile-bio">
                        <?php echo $profile_bio; ?>
                    </p>
                </div>
            </div>
        </header>

        <div class="profile-posts-grid">
            <?php
            if ($use_db && $posts_result):
                if ($posts_result->num_rows > 0):
                    while ($row = $posts_result->fetch_assoc()):
            ?>
                        <div class="post-item">
                            <img src="<?php echo htmlspecialchars($row['image_path'], ENT_QUOTES, 'UTF-8'); ?>" alt="投稿画像">
                            <?php if (!empty($row['caption'])): ?>
                                <div class="post-caption">
                                    <?php echo nl2br(htmlspecialchars($row['caption'], ENT_QUOTES, 'UTF-8')); ?>
                                </div>
                            <?php endif; ?>
                        </div>
            <?php
                    endwhile;
                else:
                    echo "<p>まだ投稿がありません。</p>";
                endif;
            else:
                echo "<p>（デモ）DB未接続のため投稿は表示していません。</p>";
            endif;
            ?>
        </div>

        <!-- ナビゲーションボタン -->
        <div class="nav-buttons">
            <a href="mainmenu.php" class="nav-button">戻る</a>
        </div>
    </div>

<?php
// 後始末
if (isset($stmt) && $stmt instanceof mysqli_stmt) { $stmt->close(); }
if (isset($conn)  && $conn  instanceof mysqli)      { $conn->close(); }
?>
</body>
</html>
