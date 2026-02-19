<?php
declare(strict_types=1);

require_once dirname(__FILE__) . '/TaskRepository.php';
$config = require_once dirname(__FILE__) . '/config.php';

// URLパラメータからIDを取得 (例: delete_task.php?id=5)
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: list_tasks.php');
    exit;
}

try {
    // データベース接続
    $dsn = "mysql:host={$config['db']['host']};dbname={$config['db']['dbname']};charset=utf8mb4";
    $pdo = new PDO($dsn, $config['db']['user'], $config['db']['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $repository = new TaskRepository($pdo);
    
    // 削除実行
    $repository->delete($id);

} catch (Exception $e) {
    // エラー時はログ等に出力するのが望ましいですが、一旦一覧に戻します
}

// 完了後、一覧画面にリダイレクト
header('Location: list_tasks.php');
exit;