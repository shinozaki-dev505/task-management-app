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
    <title>完了済みタスク履歴 - タスク管理システム</title>
    <style>
        .container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 40px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            font-family: sans-serif;
        }
        .header-area {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
        }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #fcfcfc; color: #777; font-size: 0.9em; }
        .task-name-completed { color: #555; text-decoration: line-through; }
        .btn-restore { color: #007bff; text-decoration: none; font-size: 0.9em; margin-right: 15px; }
        .btn-delete { color: #d9534f; text-decoration: none; font-size: 0.9em; }
        .footer-nav { margin-top: 40px; display: flex; gap: 20px; }
        .btn-back { text-decoration: none; color: #666; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-area">
            <h2 style="margin: 0; color: #333;">✅ 完了済みタスク履歴</h2>
            <span style="color: #999; font-size: 0.9em;">これまでに達成したタスク</span>
        </div>

        <?php if (isset($error_message)): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <?php if (empty($tasks)): ?>
            <p style="text-align: center; color: #bbb; padding: 40px;">完了したタスクはまだありません。</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th style="width: 35%;">タスク名</th>
                        <th>優先度</th>
                        <th>登録日</th>
                        <th>完了日</th>
                        <th style="text-align: center;">所要日数</th>
                        <th>操作</th> 
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $task): 
                        // 登録時と完了時の「時間」も含めた計算
                        $start = new DateTime($task->getCreatedAt());
                        $end = new DateTime($task->getUpdatedAt());
                        
                        // 差分を秒単位で取得し、1日の秒数(86400秒)で割る
                        $diffSeconds = $end->getTimestamp() - $start->getTimestamp();
                        $diffDays = $diffSeconds / 86400;

                        // もし完了時間が登録時間より前（データ上の矛盾）なら0、それ以外は小数点第1位まで表示
                        $displayDays = ($diffDays > 0) ? number_format($diffDays, 1) : "0.0";
                    ?>
                        <tr>
                            <td><span class="task-name-completed"><?php echo htmlspecialchars($task->getName()); ?></span></td>
                            <td><span style="color: #888; font-size: 0.9em;"><?php echo $task->getPriorityAsString(); ?></span></td>
                            <td style="font-size: 0.85em; color: #666;"><?php echo date('m/d H:i', strtotime($task->getCreatedAt())); ?></td>
                            <td style="font-size: 0.85em; color: #666;"><?php echo date('m/d H:i', strtotime($task->getUpdatedAt())); ?></td>
                            <td style="text-align: center;">
                                <span style="font-weight: bold; color: #2e7d32;">
                                    <?php echo $displayDays; ?>日
                                </span>
                            </td>
                            <td>
                                <a href="update_progress.php?id=<?php echo $task->getId(); ?>&progress=0" class="btn-restore" onclick="return confirm('未完了に戻しますか？')">戻す</a>
                                <a href="delete_task.php?id=<?php echo $task->getId(); ?>&from=history" class="btn-delete" onclick="return confirm('完全に削除しますか？')">削除</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <div class="footer-nav">
            <a href="list_tasks.php" class="btn-back">◀ 現在のタスク一覧に戻る</a>
            <a href="index.php" class="btn-back">▶ 新規タスク登録へ</a>
        </div>
    </div>
</body>
</html>