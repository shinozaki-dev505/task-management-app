<?php
declare(strict_types=1);

require_once dirname(__FILE__) . '/TaskRepository.php';
$config = require_once dirname(__FILE__) . '/config.php';

// URLからIDを取得
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    try {
        $dsn = "mysql:host={$config['db']['host']};port=3307;dbname={$config['db']['dbname']};charset=utf8mb4";
        $pdo = new PDO($dsn, $config['db']['user'], $config['db']['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);

        $repository = new TaskRepository($pdo);
        // さっき作ったメソッドを呼び出す！
        $repository->completeById($id);

    } catch (Exception $e) {
        // エラー処理
    }
}

// 終わったら一覧に戻る
header('Location: list_tasks.php');
exit;