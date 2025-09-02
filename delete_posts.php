<?php
session_start();

// ログインしていなければリダイレクト
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// POSTリクエストかどうか確認
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['post_ids'])) {
    // データベース接続情報
    $servername = "localhost";
    $username_db = "root";
    $password_db = "";
    $dbname = "user_information";

    // データベース接続
    $conn = new mysqli($servername, $username_db, $password_db, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $user_id = $_SESSION["user_id"];
    $post_ids_to_delete = $_POST['post_ids'];

    // 削除する投稿IDをSQLクエリ用にフォーマット
    $placeholders = implode(',', array_fill(0, count($post_ids_to_delete), '?'));
    
    // SQLクエリを作成
    // user_idも条件に含めることで、他のユーザーの投稿を削除できないようにする
    $sql = "DELETE FROM posts WHERE id IN ($placeholders) AND user_id = ?";

    // プリペアドステートメントの準備
    $stmt = $conn->prepare($sql);
    
    // バインドパラメータの型指定
    // 'i'は整数、'i'はuser_id用
    $types = str_repeat('i', count($post_ids_to_delete)) . 'i';
    $bind_params = array_merge([$types], $post_ids_to_delete, [$user_id]);
    
    // パラメータをバインド
    $stmt->bind_param(...$bind_params);

    // クエリを実行
    if ($stmt->execute()) {
        // データベースから投稿を削除後、関連する画像ファイルも削除する
        // 削除する画像のパスを取得
        $image_paths = [];
        $sql_select = "SELECT image_path FROM posts WHERE id IN ($placeholders)";
        $stmt_select = $conn->prepare($sql_select);
        $types_select = str_repeat('i', count($post_ids_to_delete));
        $stmt_select->bind_param($types_select, ...$post_ids_to_delete);
        $stmt_select->execute();
        $result_select = $stmt_select->get_result();
        while ($row = $result_select->fetch_assoc()) {
            if (file_exists($row['image_path'])) {
                unlink($row['image_path']);
            }
        }
        $stmt_select->close();

        // 削除成功メッセージ
        $_SESSION['message'] = '選択した投稿を削除しました。';
    } else {
        $_SESSION['message'] = '投稿の削除に失敗しました。';
    }

    $stmt->close();
    $conn->close();
} else {
    // POSTリクエストでない場合、または何も選択されていない場合
    $_SESSION['message'] = '削除する投稿が選択されていません。';
}

// プロフィール画面にリダイレクト
header("Location: plofile.php");
exit;
?>