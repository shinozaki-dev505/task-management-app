# タスク管理システム (Task Management App)

シンプルで使いやすい、PHP製のタスク管理システムです。  
AIを活用しながら、Repositoryパターンの採用やデータ解析（所要日数計算）など、実務に近い構造を意識して開発しました。

## 🚀 主な機能
- **タスク登録**: タスク名、優先度を指定して素早く登録。
- **検索・絞り込み**: 大量のタスクからキーワードで瞬時に検索可能。
- **進捗管理**: 0〜100%の進捗を更新し、プログレスバーで可視化。
- **完了履歴**: 完了したタスクの「登録〜完了までの所要日数」を小数点第1位まで自動計算して表示。
- **レスポンシブデザイン**: 清潔感のある、広々としたUI設計。
### 📸 画面遷移図
ユーザーの動線を定義します。

* **1.HOME（新規登録画面）**: `index.php`
  タスクを素早く入力・登録する入口です。
  <img width="638" src="https://github.com/user-attachments/assets/e2f1129a-8494-433f-933a-6184914cdaf2" />
↓ 登録後、自動遷移
* **2.タスク一覧画面**: `list_tasks.php`
  現在のタスク管理と操作のメイン画面です。
  <img width="890" src="https://github.com/user-attachments/assets/9e64da8c-81c0-43ee-b8b5-96f863708725" />
  
  ・検索実行 → 同画面で絞り込み
　・更新・完了ボタン → 処理後、同画面へリダイレクト
　・履歴リンク → 完了履歴へ

* **3.完了履歴画面**: history_tasks.php
  完了したタスクの記録と所要日数を確認できます。
　<img width="890" height="575" alt="image" src="https://github.com/user-attachments/assets/55addb3f-5781-4cc8-b1f6-0eee37df3934" />

  ・戻るボタン → タスク一覧へ

## 🛠 使用技術
- **Language**: PHP 8.x
- **Database**: MySQL 8.0 (MariaDB) / ポート 3306 (標準)
- **Frontend**: HTML5, CSS3 (Vanilla JSなしのピュアな操作感)

## 💡 こだわったポイント
- **データ可視化**: 単なる履歴表示ではなく、DateTimeクラスを用いて「何日で終わらせたか」を0.1日単位で計算。生産性の振り返りを可能にしました。
- **設計思想**: ビジネスロジックを `Task` クラス、DB操作を `TaskRepository` クラスに集約。保守性の高いコード構成を目指しました。
- **実務への配慮**: ポート番号の競合回避や、外部ファイルでのDB接続情報管理など、開発環境の構築しやすさを考慮。

## 🔧 セットアップ
標準的なMySQL環境（ポート 3306）での構築を前提としています。

### 1. データベース・ユーザーの作成
MySQLにログインし、以下のSQLを実行してください。

```sql
-- データベース作成
CREATE DATABASE IF NOT EXISTS task_manager_db CHARACTER SET utf8mb4;
USE task_manager_db;

-- 実行ユーザー作成と権限付与
CREATE USER IF NOT EXISTS 'task_user'@'localhost' IDENTIFIED BY 'task_pass';
GRANT ALL PRIVILEGES ON task_manager_db.* TO 'task_user'@'localhost';
FLUSH PRIVILEGES;

-- テーブル作成
CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    priority TINYINT NOT NULL DEFAULT 1 COMMENT '1:低, 2:中, 3:高',
    progress INT NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    completed_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 2. 環境設定
config.php（またはDB接続箇所）を、ご自身の環境に合わせて編集してください。

Host: localhost

Port: 3306

User: (設定したユーザー名)

Password: (設定したパスワード)

### 3. 実行
ブラウザで index.php にアクセスして実行します。

