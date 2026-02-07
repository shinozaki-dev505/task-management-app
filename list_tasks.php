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
    // 未完了のタスクだけを取得する
    $tasks = $repository->findIncomplete();

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
    <title>タスク管理システム - タスク一覧</title>
    <link rel="stylesheet" href="style.css"> 
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; vertical-align: middle; }
        th { background-color: #f2f2f2; }
        .progress-bar { background-color: #eee; border-radius: 4px; overflow: hidden; width: 100px; height: 15px; }
        .progress-fill { height: 100%; background-color: #5cb85c; transition: width 0.5s; }
        .priority-high { color: red; font-weight: bold; }
        .priority-middle { color: orange; }
        .priority-low { color: gray; }
        
        /* 更新フォーム用のスタイル */
        .update-form { display: flex; align-items: center; gap: 5px; margin: 0; }
        .input-progress { width: 50px; padding: 2px; }
        .btn-update { font-size: 0.8em; cursor: pointer; padding: 2px 5px; }

        /* 操作列の幅を固定し、折り返しを防ぐ */
        th:last-child, td:last-child {
            white-space: nowrap; /* 文字を折り返さない */
            width: 120px;       /* 余裕を持った幅に固定 */
            text-align: center;  /* ボタンを中央に寄せる */
        }

        /* リンクボタンの余白を少し調整 */
        td:last-child a {
            display: inline-block;
            margin: 0 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2>現在のタスク一覧</h2>
            <a href="history_tasks.php" style="font-weight: bold;">▶ 完了済みの履歴を見る</a>
        </div>

        <?php if (isset($error_message)): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <?php if (empty($tasks)): ?>
            <p>現在、取り組むべきタスクはありません。新しく追加しましょう！</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>タスク名</th>
                        <th>優先度</th>
                        <th>進行度（更新）</th>
                        <th>進捗バー</th>
                        <th>操作</th> 
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $task): 
                        $priority_class = match($task->getPriority()) {
                            Task::PRIORITY_HIGH => 'priority-high',
                            Task::PRIORITY_MIDDLE => 'priority-middle',
                            default => 'priority-low',
                        };
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($task->getName()); ?></td>
                            <td class="<?php echo $priority_class; ?>"><?php echo $task->getPriorityAsString(); ?></td>
                            <td>
                                <form action="update_progress.php" method="POST" class="update-form">
                                    <input type="hidden" name="id" value="<?php echo $task->getId(); ?>">
                                    <input type="number" name="progress" 
                                           value="<?php echo $task->getProgress(); ?>" 
                                           min="0" max="100" step="1" class="input-progress">
                                    <span>%</span>
                                    <button type="submit" class="btn-update">更新</button>
                                </form>
                            </td>
                            <td>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo $task->getProgress(); ?>%;"></div>
                                </div>
                            </td>
                            <td>
                                <a href="complete_task.php?id=<?php echo $task->getId(); ?>" 
                                   style="color: #5cb85c; margin-right: 15px; font-weight: bold;">完了</a>

                                <a href="delete_task.php?id=<?php echo $task->getId(); ?>" 
                                   style="color: #d9534f;" 
                                   onclick="return confirm('このタスクを削除してもよろしいですか？')">削除</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <p style="margin-top: 30px;"><a href="index.html">← 新規タスク登録へ戻る</a></p>
    </div>
</body>
</html>