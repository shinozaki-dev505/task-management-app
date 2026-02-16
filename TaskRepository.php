<?php
declare(strict_types=1);

require_once dirname(__FILE__) . '/Task.php';

class TaskRepository {
    private PDO $pdo;
    private string $table_name = 'tasks';

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * 未完了タスクを検索（キーワードがあれば絞り込み）
     */
    public function searchIncomplete(string $keyword): array {
        $sql = "SELECT id, name, priority, progress, created_at, updated_at, completed_at 
                FROM {$this->table_name} 
                WHERE progress < 100 AND name LIKE :keyword 
                ORDER BY created_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':keyword' => '%' . $keyword . '%']);
        
        return $this->fetchAll($stmt);
    }

    /**
     * 通常の未完了タスク取得
     */
    public function findIncomplete(): array {
        $sql = "SELECT id, name, priority, progress, created_at, updated_at, completed_at 
                FROM {$this->table_name} 
                WHERE progress < 100 
                ORDER BY created_at DESC";
        $stmt = $this->pdo->query($sql);
        return $this->fetchAll($stmt);
    }

    /**
     * 重複を避けるための共通取得メソッド
     */
    private function fetchAll($stmt): array {
        $tasks = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tasks[] = new Task(
                $row['name'],
                (int)$row['priority'],
                (int)$row['progress'],
                (int)$row['id'],
                $row['created_at'],
                $row['updated_at'],
                $row['completed_at']
            );
        }
        return $tasks;
    }

    /**
     * 完了済み（100%）のタスクのみを取得する
     */
    public function findCompleted(): array
    {
        $sql = "SELECT id, name, priority, progress, created_at, updated_at, completed_at 
                FROM {$this->table_name} 
                WHERE progress = 100 
                ORDER BY updated_at DESC"; // 完了したのが新しい順に並べる
        
        $stmt = $this->pdo->query($sql);
        
        $tasks = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tasks[] = new Task(
                $row['name'],
                (int)$row['priority'],
                (int)$row['progress'],
                (int)$row['id'],
                $row['created_at'],
                $row['updated_at'],
                $row['completed_at']
            );
        }
        return $tasks;
    }


    public function updateProgress(int $id, int $progress): bool {
        if ($progress < 0) $progress = 0;
        if ($progress > 100) $progress = 100;
        $sql = "UPDATE {$this->table_name} SET progress = :progress WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id, ':progress' => $progress]);
    }

    public function delete(int $id): bool {
        $sql = "DELETE FROM {$this->table_name} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function completeById(int $id): bool {
        $sql = "UPDATE {$this->table_name} SET progress = 100 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * 新しいタスクをデータベースに保存する
     */
    public function save(Task $task): bool {
        $sql = "INSERT INTO {$this->table_name} (name, priority, progress, created_at, updated_at) 
                VALUES (:name, :priority, :progress, NOW(), NOW())";
        
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            ':name'     => $task->getName(),
            ':priority' => $task->getPriority(),
            ':progress' => $task->getProgress(),
        ]);
    }
}