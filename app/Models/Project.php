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
     * プロジェクトのメンバーシップ（中間テーブルのレコード）
     * 
     * ⚠️ 非推奨: 通常は users() リレーションを使用してください
     * 
     * 返り値: Membership モデルのコレクション
     * 使用例: $project->memberships()->where('role', 'project_owner')->get()
     * 
     * 特徴:
     * - 中間テーブル（memberships）のレコード自体を取得
     * - Membership モデルのプロパティ（id, project_id, user_id, role）に直接アクセス可能
     * - ユーザー情報にアクセスするには: $membership->user
     * 
     * 使用するべき場面:
     * - メンバーシップIDが必要な特殊なケース（現在は使用していない）
     * - 通常は users() リレーションと detach() を使用してメンバー管理を行う
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

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
