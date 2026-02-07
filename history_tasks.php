<?php
declare(strict_types=1);

require_once dirname(__FILE__) . '/Task.php';
require_once dirname(__FILE__) . '/TaskRepository.php';
$config = require_once dirname(__FILE__) . '/config.php'; 

$tasks = []; 

try {
    $dsn = "mysql:host={$config['db']['host']};port=3307;dbname={$config['db']['dbname']};charset=utf8mb4";
    $pdo = new PDO($dsn, $config['db']['user'], $config['db']['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $repository = new TaskRepository($pdo);
    
    // 【重要】前回作成した「完了済みだけを取得するメソッド」を呼び出します
    $tasks = $repository->findCompleted();

} catch (PDOException $e) {
    $error_message = 'データベース接続エラー：' . $e->getMessage();
} catch (Throwable $e) {
    $error_message = '予期せぬエラーが発生しました：' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>タスク管理システム - 完了済み履歴</title>
    <link rel="stylesheet" href="style.css"> 
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        /* 完了済み専用のスタイル：少し文字を薄くして、達成感を出す */
        .completed-row { background-color: #f9f9f9; color: #666; }
        .status-badge { 
            background-color: #5cb85c; 
            color: white; 
            padding: 2px 8px; 
            border-radius: 10px; 
            font-size: 0.8em; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>完了済みタスク履歴</h2>
        <p>これまでに完了したタスクの一覧です。</p>

        <?php if (isset($error_message)): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <?php if (empty($tasks)): ?>
            <p>完了したタスクはまだありません。</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>タスク名</th>
                        <th>優先度</th>
                        <th>ステータス</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $task): ?>
                        <tr class="completed-row">
                            <td><del><?php echo htmlspecialchars($task->getName()); ?></del></td>
                            <td><?php echo $task->getPriorityAsString(); ?></td>
                            <td><span class="status-badge">完了(100%)</span></td>
                            <td>
                                <a href="delete_task.php?id=<?php echo $task->getId(); ?>" 
                                   style="color: #d9534f;" 
                                   onclick="return confirm('履歴から完全に削除してもよろしいですか？')">削除</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <div style="margin-top: 30px;">
            <p><a href="list_tasks.php">◀ 現在のタスク一覧に戻る</a></p>
            <p><a href="index.html">▶ 新規タスク登録へ</a></p>
        </div>
    </div>
</body>
</html>