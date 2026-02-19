<?php
declare(strict_types=1);

require_once dirname(__FILE__) . '/TaskRepository.php';
$config = require_once dirname(__FILE__) . '/config.php';

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$progress = isset($_POST['progress']) ? (int)$_POST['progress'] : 0;

if ($id > 0) {
    try {
        $dsn = "mysql:host={$config['db']['host']};dbname={$config['db']['dbname']};charset=utf8mb4";
        $pdo = new PDO($dsn, $config['db']['user'], $config['db']['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);

        $repository = new TaskRepository($pdo);
        $repository->updateProgress($id, $progress);

    } catch (Exception $e) {
        // エラー時は何もしない
    }
}

// 100%になったら一覧から消える仕様なので、そのまま一覧に戻せばOK
header('Location: list_tasks.php');
exit;