<?php
declare(strict_types=1);

Class Task{
    public const PRIORITY_LOW=0;
    public const PRIORITY_MIDDLE=1;
    public const PRIORITY_HIGH=2;

    private string $name;
    private int $priority;
    private int $progress;
    private ?int $id; // ★追加：IDを保存する変数

    /**
     * コンストラクタ（$idを追加）
     */
    public function __construct(string $name, int $priority = self::PRIORITY_MIDDLE, int $progress = 0, ?int $id = null)
    {
        $this->name = $name;
        $this->setPriority($priority);
        $this->setProgress($progress);
        $this->id = $id; // ★追加
    }

    // ★追加：IDのゲッター
    public function getId(): ?int
    {
        return $this->id;
    }

    // --- 以下、既存のメソッド（getName, setPriorityなどはそのまま） ---
    public function getName(): string { return $this->name; }
    public function setName(string $name): void { $this->name = $name; }
    public function getPriority(): int { return $this->priority; }
    public function setPriority(int $priority): void {
        if (!in_array($priority, [self::PRIORITY_LOW, self::PRIORITY_MIDDLE, self::PRIORITY_HIGH])) {
            throw new InvalidArgumentException('無効な優先度です。');
        }
        $this->priority = $priority;
    }
    public function getProgress(): int { return $this->progress; }
    public function setProgress(int $progress): void {
        if($progress < 0) $progress=0;
        elseif($progress>100) $progress=100;
        $this->progress=$progress;
    }
    public function isCompleted(): bool { return $this->progress === 100; }
    public function complete(): void { $this->progress = 100; }
    public function getPriorityAsString(): string {
        switch($this->priority){
            case self::PRIORITY_LOW : return '低';
            case self::PRIORITY_MIDDLE: return '中';
            case self::PRIORITY_HIGH: return '高';
            default: return '不明';
        }
    }
}