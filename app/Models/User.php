<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * ユーザーが所属するプロジェクト（中間テーブル経由でプロジェクトを取得）
     * 
     * 返り値: Project モデルのコレクション（pivot情報付き）
     * 使用例: $user->projects()->get() // 各プロジェクトの $project->pivot->role で役割を取得
     * 
     * 特徴:
     * - 中間テーブルを経由してプロジェクト情報を取得
     * - Project モデルのプロパティ（id, name, is_archived）に直接アクセス可能
     * - 中間テーブルの情報（role）にアクセスするには: $project->pivot->role
     * - プロジェクト一覧表示など、プロジェクト情報が必要な場合に使用（推奨）
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'memberships')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * ユーザーが作成したタスク
     */
    public function createdTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'created_by');
    }
}
