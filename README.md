# タスク管理システム (Task Management App)

シンプルで使いやすい、PHP製のタスク管理システムです。  
AIを活用しながら、Repositoryパターンの採用や期限管理アラート、データ解析（所要日数計算）に加え、**実務の現場で必須となるデータ入出力（CSVエクスポート）機能**を独自に実装し、より実用的なビジネスツールを意識して開発しました。

## 🌟 2026年5月 アップデート: CSVデータ入出力機能の追加
蓄積された完了タスクデータを外部ツール（Excel等）で2次利用・解析しやすくするため、以下の機能を実装しました。
* **メモリ効率を意識したストリーム出力 (`php://output`)**: サーバー内に一時ファイルを作成せず、データベースから直接ブラウザのダウンロードストリームにデータを流し込む実務的な設計を採用。数万件のデータ増大にも耐えうる構成にしています。
* **ビジネス現場に配慮した文字化け防止 (BOM付与)**: Web標準のUTF-8データの先頭にBOM（Byte Order Mark）を自動で書き込むことで、日本のビジネス現場の標準であるExcelでそのまま開いても「日本語が文字化けしない」ユーザーフレンドリーな設計（UX）を実現しました。

## 🚀 主な機能
- **タスク登録**: タスク名、優先度に加え、**「期限日（納期）」**を指定して登録可能。
- **納期アラート**: 期限が3日以内に迫った未完了タスクを**赤色でハイライト表示**。視覚的に優先順位を通知します。
- **検索・絞り込み**: キーワード検索により、必要なタスクを瞬時に抽出。
- **進捗管理**: 0〜100%の進捗を更新し、プログレスバーで可視化。
- **完了履歴**: 完了したタスクの「登録〜完了までの所要日数」を小数点第1位まで自動計算して表示。
- **📥 完了タスクのCSV出力**: 完了したタスクの一覧（ID、タスク名、優先度、期限、完了日時）をボタン一つでExcel対応形式で一括ダウンロード。
- **レスポンシブデザイン**: 清潔感のある、広々としたUI設計。

### 📸 画面遷移図
ユーザーの動線を定義します。

<<<<<<< HEAD
* **1. HOME（新規登録画面）**: `index.php`
  タスクを素早く入力・登録する入口です。画面下部からいつでも完了タスクのCSVダウンロードが可能です。
　<img width="1912" height="914" alt="image" src="https://github.com/user-attachments/assets/15eafca0-5b16-4041-b2b3-6632f6135c66" />
  
  ↓ 登録後、自動遷移
* **2. タスク一覧画面**: `list_tasks.php`
  現在のタスク管理と操作のメイン画面です。
 <img width="890" height="488" alt="image" src="https://github.com/user-attachments/assets/f5cb69eb-6e49-4927-b6bc-bc53f67ee55e" />
 
  ・検索実行 → 同画面で絞り込み
  ・更新・完了ボタン → 処理後、同画面へリダイレクト
  ・履歴リンク → 完了履歴へ

* **3. 完了履歴画面**: `history_tasks.php`
  完了したタスクの記録と所要日数を確認できます。
  <img width="890" height="575" alt="image" src="https://github.com/user-attachments/assets/55addb3f-5781-4cc8-b1f6-0eee37df3934" />

  ・戻るボタン → タスク一覧へ


## 🛠 使用技術
- **Language**: PHP 8.x (オブジェクト指向 / 標準関数・ストリーム処理の活用)
- **Database**: MySQL 8.0 (MariaDB) (インデックスおよびタイムスタンプ管理)
- **Frontend**: HTML5, CSS3  (Flexboxを用いたモダンなレイアウト)
- **Tool**: VS Code, XAMPP, Git / GitHub

## 💡 こだわったポイント
- **現場のデータ活用を見据えたCSV設計**:
  これまでの製造・物流現場においてExcelやVBAを駆使して業務効率化を行ってきた経験から、業務システムにおける「データの2次利用性」の重要性を深く認識していました。そのため、単なる画面表示に留まらず、標準関数 `fputcsv()` を適切に用いてRFC4180に準拠した堅牢なCSV出力機能を自ら設計・実装しました。
- **現場目線のアラート機能**:
  単なる期限表示だけでなく、`DateTime` クラスを用いた動的な日付比較により、期限当日や期限超過後も完了するまで警告（ハイライト）を維持し、作業漏れを絶対に防ぐ設計にしました。
- **リポジトリパターンによる疎結合設計**:
  ビジネスロジックを `Task` クラス、DB操作を `TaskRepository` クラスに完全に分離。SQL文がメインロジックに混在しないようにし、将来的なデータベース変更にも柔軟に対応できる保守性の高いコード構成を目指しました。

## 🔧 セットアップ

### 1. データベース・ユーザーの作成
MySQLにログインし、以下のSQLを実行してください。

```sql
-- データベース作成
CREATE DATABASE IF NOT EXISTS task_manager_db CHARACTER SET utf8mb4;
USE task_manager_db;

-- テーブル作成
CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    priority TINYINT NOT NULL DEFAULT 1 COMMENT '0:低, 1:中, 2:高',
    progress INT NOT NULL DEFAULT 0,
    deadline DATE NULL, -- 期限日
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

download_csv.php およびデータベース接続用ファイル（TaskRepository.php 等）の、以下の接続設定をご自身のローカル環境（XAMPPなど）に合わせて編集してください。

```php
// 例：Windows XAMPPの標準設定（パスワード空）の場合
new PDO('mysql:host=localhost;dbname=task_manager_db;charset=utf8', 'root', '');
```


### 3. 実行
ブラウザで index.php にアクセスして実行します。
