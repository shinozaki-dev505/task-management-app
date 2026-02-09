<?php
declare(strict_types=1);

require_once dirname(__FILE__) . '/Task.php';

class TaskRepository
{
    private PDO $pdo;
    private string $table_name = 'tasks';

    /**
     * コンストラクタ: データベース接続を注入する
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * タスクをデータベースに保存 (新規登録または更新)
     * @param Task $task
     * @return bool 成功したか
     */
    public function save(Task $task): bool
    {
        // 実際には、TaskオブジェクトにIDを持たせ、IDの有無でINSERT/UPDATEを切り替えます
        // 今回はシンプルに新規登録（INSERT）のみを実装します。

        // SQLインジェクション対策としてプリペアドステートメントを使用
        $sql = "INSERT INTO {$this->table_name} (name, priority, progress) VALUES (:name, :priority, :progress)";
        
        $stmt = $this->pdo->prepare($sql);

        // Taskオブジェクトから値を取得（ゲッターを使用）
        return $stmt->execute([
            ':name' => $task->getName(),
            // privateなpriorityプロパティを外部から取得するためのゲッターが必要です
            ':priority' => $task->getPriority(), 
            ':progress' => $task->getProgress(),
        ]);
    }
    
    /**
     * すべてのタスクを取得する
     * @return Task[]
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM {$this->table_name} ORDER BY priority DESC, id ASC";
        $stmt = $this->pdo->query($sql);
        
        $tasks = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // データベースの行データをTaskオブジェクトにマッピング
            $task = new Task(
                $row['name'],
                (int)$row['priority'],
                (int)$row['progress'],
                (int)$row['id'] // ★ここを追加！
            );
            // 実際にはIDもTaskオブジェクトに設定する必要があります
            $tasks[] = $task;
        }
        return $tasks;
    }
    
    // Taskクラスにpriorityのゲッターがないため、一旦プライベートヘルパーとして定義
    // ベストプラクティスとしてはTaskクラスに public function getPriorityValue(): int を追加すべきです
    private function getPriorityValue(Task $task): int 
    {
        // リフレクションを使ってprivateプロパティにアクセスする
        // ※これは緊急的な措置で、本来はTask::getPriorityValue()を定義すべき
        $reflection = new ReflectionClass($task);
        $property = $reflection->getProperty('priority');
        $property->setAccessible(true);
        return $property->getValue($task);
    }

    // 
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table_name} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * 指定したIDの進行度を 100% に更新する
     * @param int $id 更新したいタスクのID
     * @return bool 成功したかどうか
     */
    public function completeById(int $id): bool
    {
        // SQLのUPDATE文の正しい書き方
        // SET で項目を指定し、WHERE でどの行かを指定します
        $sql = "UPDATE {$this->table_name} SET progress = 100 WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        
        // 実行して結果を返します
        return $stmt->execute([':id' => $id]);
    }

    /**
     * 未完了（100%未満）のタスクのみを取得する
     */
    public function findIncomplete(): array
    {
        // 進行度が100より小さいものを取得
        $sql = "SELECT * FROM {$this->table_name} WHERE progress < 100 ORDER BY priority DESC, id ASC";
        $stmt = $this->pdo->query($sql);
        
        $tasks = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tasks[] = new Task(
                $row['name'],
                (int)$row['priority'],
                (int)$row['progress'],
                (int)$row['id']
            );
        }
        return $tasks;
    }
    
    /**
     * 完了済み（100%）のタスクのみを取得する
     */
    public function findCompleted(): array
    {
        // 進行度がちょうど100のものを取得します
        $sql = "SELECT * FROM {$this->table_name} WHERE progress = 100 ORDER BY id DESC";
        $stmt = $this->pdo->query($sql);
        
        $tasks = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tasks[] = new Task(
                $row['name'],
                (int)$row['priority'],
                (int)$row['progress'],
                (int)$row['id']
            );
        }
        return $tasks;
    }
    /**
     * 指定したIDの進行度を指定した数値に更新する
     * @param int $id タスクID
     * @param int $progress 新しい進行度(0-100)
     */
    public function updateProgress(int $id, int $progress): bool
    {
        // 入力値が0-100の範囲に収まるようにガード
        if ($progress < 0) $progress = 0;
        if ($progress > 100) $progress = 100;

        $sql = "UPDATE {$this->table_name} SET progress = :progress WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':progress' => $progress
        ]);
    }
}
