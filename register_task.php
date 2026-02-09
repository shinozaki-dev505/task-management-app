<?php
declare(strict_types=1);

// 必要なファイルを読み込みます
require_once dirname(__FILE__) . '/Task.php';
require_once dirname(__FILE__) . '/TaskRepository.php';

// データベース設定を読み込みます
$config = require_once dirname(__FILE__) . '/config.php';

// POSTリクエストかどうかを確認
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // POSTリクエスト以外の場合は、登録ページにリダイレクト
    header('Location: index.php');
    exit;
}

//////////////////////////////////////////////////////////////////
// フォームデータの取得（デバッグ版）
$task_name = $_POST['task_name'] ?? '';

// 一時的にキャスト(int)を外して、何が届いているか確認します
$priority_raw = $_POST['priority'] ?? '未受信'; 

// 画面に表示して一時停止
echo "デバッグ: フォームから届いた生の値は「" . $priority_raw . "」です。<br>";
echo "デバッグ: これを(int)に変換すると「" . (int)$priority_raw . "」になります。<br>";
exit; // ここで処理を止める
//////////////////////////////////////////////////////////////////

// フォームデータの取得
$task_name = $_POST['task_name'] ?? '';
$priority_int = (int)($_POST['priority'] ?? Task::PRIORITY_MIDDLE); // int型にキャスト
$progress_int = (int)($_POST['progress'] ?? 0); // int型にキャスト

// 簡単なバリデーション
if (empty($task_name)) {
    // エラーメッセージを表示するか、index.phpに戻すなど
    $error_message = 'タスク名が入力されていません。';
    // 実際には、より詳細なエラー処理を行うべきです
} else {
    try {
        // 1. データベース接続を確立
        $dsn = "mysql:host={$config['db']['host']};port=3307;dbname={$config['db']['dbname']};charset=utf8mb4";
        $pdo = new PDO($dsn, $config['db']['user'], $config['db']['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);

        // 2. TaskRepositoryをインスタンス化
        $repository = new TaskRepository($pdo);

        // 3. フォームデータから新しいTaskオブジェクトを生成
        // Taskクラスのコンストラクタ内で、優先度と進行度のバリデーションが行われる
        $newTask = new Task($task_name, $priority_int, $progress_int);
        
        // 4. TaskRepositoryのsaveメソッドを使用してデータベースに登録
        if ($repository->save($newTask)) {
            // 登録成功: タスク一覧ページにリダイレクト
            header('Location: list_tasks.php');
            exit;
        } else {
            $error_message = 'タスクの登録に失敗しました。';
        }

    } catch (InvalidArgumentException $e) {
        // Taskコンストラクタやセッターでのバリデーションエラー
        $error_message = '入力されたデータが無効です: ' . $e->getMessage();
    } catch (PDOException $e) {
        // データベース接続または操作エラーを捕捉
        // 本番環境では詳細なエラーメッセージは表示すべきではありません
        $error_message = 'データベースエラーが発生しました。詳細: ' . $e->getMessage();
    } catch (Throwable $e) {
        $error_message = '予期せぬエラーが発生しました。';
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>タスク登録結果</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>タスク登録結果</h2>
        
        <?php if (isset($error_message)): ?>
            <p style="color: red;">🚨 登録エラー: <?php echo htmlspecialchars($error_message); ?></p>
            <p>前のページに戻り、再度お試しください。</p>
        <?php else: ?>
            <p>タスク「<?php echo htmlspecialchars($task_name); ?>」が正常に登録されました。</p>
        <?php endif; ?>

        <p><a href="index.php">? 新規タスク登録へ戻る</a></p>
        <p><a href="list_tasks.php">? タスク一覧へ</a></p>
    </div>
</body>
</html>