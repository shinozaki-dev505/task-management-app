<?php
// 1. データベース接続
try {
    $pdo = new PDO('mysql:host=localhost;dbname=task_manager_db;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("接続失敗: " . $e->getMessage());
}

// 2. 「完了したタスク（progressが100のもの）」のデータを取得するSQLに修正
$stmt = $pdo->prepare("SELECT id, name, priority, deadline, updated_at FROM tasks WHERE progress = 100 ORDER BY updated_at DESC");
$stmt->execute();
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. ブラウザへのヘッダー指定
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="completed_tasks_' . date('Ymd') . '.csv"');

// 4. 出力ストリームを開く
$output = fopen('php://output', 'w');

// 5. Excelの文字化け防止用BOM
fwrite($output, "\xEF\xBB\xBF");

// 6. CSVの1行目（見出し行）を出力項目に合わせて変更
fputcsv($output, ['タスクID', 'タスク名', '優先度', '期限', '完了日時']);

// 7. データを1行ずつCSVに書き込む
foreach ($tasks as $task) {
    // 優先度の数値を分かりやすい文字に変換（Taskクラスの定数構造に合わせて調整してください）
    $priorityStr = '中';
    if ($task['priority'] == 2) {
        $priorityStr = '高';
    } elseif ($task['priority'] == 0) {
        $priorityStr = '低';
    }

    fputcsv($output, [
        $task['id'],
        $task['name'],
        $priorityStr,
        $task['deadline'] ? $task['deadline'] : '未設定',
        $task['updated_at']
    ]);
}

// 8. ストリームを閉じる（ちぎれていた部分を修正）
fclose($output);
exit;
?>