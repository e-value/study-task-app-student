<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Membership extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'user_id',
        'role',
    ];

    /**
     * メンバーシップが属するプロジェクト
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * メンバーシップが属するユーザー
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
