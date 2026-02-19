<?php
declare(strict_types=1);

require_once dirname(__FILE__) . '/Task.php';
require_once dirname(__FILE__) . '/TaskRepository.php';
$config = require_once dirname(__FILE__) . '/config.php'; 

$tasks = []; 
// 検索キーワードの取得
$keyword = $_GET['keyword'] ?? '';

try {
    $dsn = "mysql:host={$config['db']['host']};dbname={$config['db']['dbname']};charset=utf8mb4";
    $pdo = new PDO($dsn, $config['db']['user'], $config['db']['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $repository = new TaskRepository($pdo);
    
    // 検索キーワードがあるかどうかで呼び出すメソッドを切り替える
    if ($keyword !== '') {
        $tasks = $repository->searchIncomplete($keyword);
    } else {
        $tasks = $repository->findIncomplete();
    }

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
        /* 白地のコンテナを広く、余白を大きく設定 */
        .container {
            max-width: 1100px;    /* 幅を広げました */
            margin: 30px auto;
            padding: 40px;        /* 内側の余白を増やしてゆとりを持たせました */
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        /* 検索フォームのスタイル */
        .search-box {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .search-box input[type="text"] {
            flex-grow: 1;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn-search {
            padding: 8px 20px;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px;
            table-layout: auto; /* コンテンツに合わせて調整 */
        }
        th, td { 
            padding: 15px;      /* セルの余白を広げました */
            border-bottom: 1px solid #eee;
            text-align: left;
            vertical-align: middle;
        }
        th { background-color: #fcfcfc; color: #666; font-size: 0.9em; }

        .progress-bar { 
            background-color: #eee; 
            border-radius: 10px;  
            width: 120px; 
            height: 12px; 
            overflow: hidden;
        }
        .progress-fill { height: 100%; background-color: #5cb85c; transition: width 0.5s; }
        
        .priority-high { color: #d9534f; font-weight: bold; }
        .priority-middle { color: #f0ad4e; }
        .priority-low { color: #999; }
        
        .update-form { 
            display: flex; 
            align-items: center; 
            gap: 5px; 
            white-space: nowrap; /* 改行を防ぐ */
        }
        .input-progress { 
            width: 55px;
            padding: 4px; 
            text-align: center;
        }
        .btn-update { padding: 4px 8px; cursor: pointer; background: #eee; border: 1px solid #ccc; border-radius: 3px; font-size: 0.8em; }

        /* 操作列 */
        th:last-child, td:last-child {
            white-space: nowrap;
            width: 130px;
            text-align: center;
        }
        
        .btn-complete { color: #5cb85c; font-weight: bold; text-decoration: none; margin-right: 10px; }
        .btn-delete { color: #d9534f; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px;">
            <h2 style="margin: 0;">現在のタスク一覧</h2>
            <a href="history_tasks.php" style="font-size: 0.9em; text-decoration: none; color: #007bff;">▶ 完了済みの履歴を見る</a>
        </div>

        <div class="search-box">
            <form action="list_tasks.php" method="GET" style="display: flex; width: 100%; gap: 10px;">
                <input type="text" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="キーワードを入力してタスクを検索...">
                <button type="submit" class="btn-search">検索</button>
                <?php if ($keyword !== ''): ?>
                    <a href="list_tasks.php" style="align-self: center; font-size: 0.8em; color: #666;">クリア</a>
                <?php endif; ?>
            </form>
        </div>

        <?php if (isset($error_message)): ?>
            <p style="color: red; padding: 10px; background: #fff1f1;"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <?php if (empty($tasks)): ?>
            <p style="text-align: center; padding: 50px; color: #999;">
                <?php echo $keyword !== '' ? '検索条件に一致するタスクはありません。' : '現在、取り組むべきタスクはありません。'; ?>
            </p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th style="width: 40%;">タスク名</th>
                        <th>優先度</th>
                        <th>登録日</th>
                        <th>進行度更新</th>
                        <th>進捗</th>
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
                            <td><strong><?php echo htmlspecialchars($task->getName()); ?></strong></td>
                            <td class="<?php echo $priority_class; ?>"><?php echo $task->getPriorityAsString(); ?></td>
                            <td style="font-size: 0.85em; color: #888;"><?php echo date('m/d H:i', strtotime($task->getCreatedAt())); ?></td>
                            <td>
                                <form action="update_progress.php" method="POST" class="update-form">
                                    <input type="hidden" name="id" value="<?php echo $task->getId(); ?>">
                                    <input type="number" name="progress" 
                                           value="<?php echo $task->getProgress(); ?>" 
                                           min="0" max="100" class="input-progress">
                                    <span>%</span>
                                    <button type="submit" class="btn-update">更新</button>
                                </form>
                            </td>
                            <td>
                                <div style="font-size: 0.8em; margin-bottom: 4px;"><?php echo $task->getProgress(); ?>%</div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo $task->getProgress(); ?>%;"></div>
                                </div>
                            </td>
                            <td>
                                <a href="complete_task.php?id=<?php echo $task->getId(); ?>" class="btn-complete">完了</a>
                                <a href="delete_task.php?id=<?php echo $task->getId(); ?>" class="btn-delete" 
                                   onclick="return confirm('このタスクを削除してもよろしいですか？')">削除</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <div style="margin-top: 40px; border-top: 1px solid #eee; padding-top: 20px;">
            <a href="index.php" style="text-decoration: none; color: #666;">← 新規タスク登録へ戻る</a>
        </div>
    </div>
</body>
</html>