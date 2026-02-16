<?php
// config.php
return [
    'db' => [
        'host' => '127.0.0.1', 
        'dbname' => 'task_manager_db',
        
        // GitHub公開用にダミー値を設定
        // 実際に動かす際は、ご自身のDB環境に合わせて書き換えてください
        'user' => 'root',        
        'password' => ''         // または 'your_password' など
    ]
];