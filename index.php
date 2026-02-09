<?php 
// Task.php を読み込み、定数を参照できるようにします
require_once dirname(__FILE__) . '/Task.php'; 
//declare(strict_types=1);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>タスク管理システム - 新規タスク登録</title>
    
    <link rel="stylesheet" href="style.css"> 
    
</head>
<body>
    <div class="container">
        <h2>新規タスク登録</h2>
        
        <form action="register_task.php" method="POST">
            
            <label for="task_name">タスク名</label>
            <input type="text" id="task_name" name="task_name" required placeholder="例：PHP資格試験の勉強">

            <label for="priority">優先度</label>
            <select id="priority" name="priority" required>
                <option value="<?php echo Task::PRIORITY_HIGH; ?>">高</option>
                <option value="<?php echo Task::PRIORITY_MIDDLE; ?>" selected>中</option>
                <option value="<?php echo Task::PRIORITY_LOW; ?>">低</option>
            </select>

            <label for="progress">進行度</label>
            <div class="progress-group">
                <input type="range" id="progress" name="progress" min="0" max="100" value="0" step="1">
                <span class="progress-value" id="progress-output-detail">0%</span>
            </div>
            
            <button type="submit">タスクを登録</button>
        </form>
        
        <p><a href="list_tasks.php">タスク一覧へ</a></p>

    </div>

    <script>
        const progressSlider = document.getElementById('progress');
        const progressOutputDetail = document.getElementById('progress-output-detail');

        progressSlider.oninput = function() {
            // スライダーが動くたびに、隣の <span> の内容を更新
            progressOutputDetail.textContent = this.value + '%';
        }
    </script>
</body>
</html>