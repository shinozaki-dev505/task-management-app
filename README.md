# タスク管理システム (Task Management App)

シンプルで使いやすいタスク管理システムです。  
学習の一環として、PHPとMySQLを使用して基本的なCRUD（作成・読み取り・更新・削除）機能を実装しました。

### 🚀 主な機能
- **タスク登録**: タスク名、期限、優先度を指定して登録できます。
- **一覧表示**: 登録されたタスクをメイン画面で確認できます。
- **進捗管理**: 各タスクの進捗状況（%）をリアルタイムに更新できます。
- **タスク完了/削除**: 完了したタスクのステータス変更や、不要なタスクの削除が可能です。
- **履歴表示**: 完了したタスクを別画面の履歴一覧で確認できます。

### 🛠 使用技術
- **Language**: PHP 8.x
- **Database**: MySQL (MariaDB)
- **Frontend**: HTML5, CSS3
- **Server**: XAMPP (Local Environment)

### 💡 こだわったポイント
- **セキュリティ**: データベース接続情報（`config.php`）を分離し、`.gitignore` を用いて機密情報が公開されないよう配慮しました。
- **保守性**: `TaskRepository` クラスなど、データ操作を共通化することでコードの整理を行いました。
- **操作性**: ユーザーが直感的に現在の進捗を把握できるよう、レイアウトを工夫しました。

### 🔧 セットアップ
本アプリケーションを動作させるためには、以下の設定を行ってください。  
※開発環境の競合回避のため、MySQLポートを **3307** に設定しています。

#### 1. データベース・ユーザーの作成
MySQLにログインし、以下のSQLを実行してください。

```sql
-- データベース作成
CREATE DATABASE IF NOT EXISTS task_manager_db CHARACTER SET utf8mb4;
USE task_manager_db;

-- 実行ユーザー作成と権限付与
CREATE USER IF NOT EXISTS 'task_user'@'localhost' IDENTIFIED BY 'task_pass';
GRANT ALL PRIVILEGES ON task_manager_db.* TO 'task_user'@'localhost';
FLUSH PRIVILEGES;

#### 2. テーブルの作成
```sql
CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_name VARCHAR(255) NOT NULL,
    priority INT DEFAULT 2,    -- 1:高, 2:中, 3:低
    progress INT DEFAULT 0,    -- 0～100
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

##### 3. 環境設定
config.php を作成し、自身の環境（ホスト、ユーザー、パスワード）を記述します。

XAMPP等の環境でポート番号が異なる場合は、config.php 内のポート指定を適宜変更してください。

ブラウザで index.php（または index.html）にアクセスして実行します。
