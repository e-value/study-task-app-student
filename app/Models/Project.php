<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_archived',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
    ];

    /**
     * プロジェクトのユーザー（中間テーブル経由でユーザーを取得）
     * 
     * 返り値: User モデルのコレクション（pivot情報付き）
     * 使用例: $project->users()->get() // 各ユーザーの $user->pivot->role で役割を取得
     * 
     * 特徴:
     * - 中間テーブルを経由してユーザー情報を取得
     * - User モデルのプロパティ（id, name, email）に直接アクセス可能
     * - 中間テーブルの情報（role）にアクセスするには: $user->pivot->role
     * - メンバー一覧表示など、ユーザー情報が必要な場合に使用（推奨）
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'memberships')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * プロジェクトのタスク
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
