<?php
declare(strict_types=1);

require_once dirname(__FILE__) . '/Task.php';

//タスク用紙クラス
class TaskSheet
{
    //タスクの配列
    private array $tasks = []; // privateと型宣言を追加

    //タスクを追加するメソッド
    public function addTask(Task $task):void
    {
        $this->tasks[] = $task;
        // Taskクラスのプロパティがprivateになったため、ゲッターを使用
        echo $task->getName(), 'を追加しました。','<br>',"\n"; 
    }

    //タスクリストを表示するメソッド
    public function show(): void // 戻り値の型を追加
    {
        foreach($this->tasks as $task){
            // isCompletes() を isCompleted() に変更
            if($task->isCompleted()){
                echo '<b>', $task->getName(), '</b>','<br>',"\n";
            }else{
                echo $task->getName(), '（', $task->getProgress(), '%）','<br>',"\n";
            }
        }
    }

}