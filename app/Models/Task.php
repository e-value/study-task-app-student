<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'status',
        'created_by',
    ];

    /**
     * タスクが属するプロジェクト
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * タスクを作成したユーザー
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * タスクが未着手（todo）か判定
     */
    public function isTodo(): bool
    {
        return $this->status === 'todo';
    }

    /**
     * タスクが作業中（doing）か判定
     */
    public function isDoing(): bool
    {
        return $this->status === 'doing';
    }

    /**
     * タスクが完了（done）か判定
     */
    public function isDone(): bool
    {
        return $this->status === 'done';
    }
}
