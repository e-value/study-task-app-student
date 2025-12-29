<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectMemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // $this は User モデルを指しますが、pivot 属性に Membership の情報が入っています
        return [
            'id' => $this->id, // ユーザーID
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->pivot->role, // 中間テーブルの role
            'joined_at' => $this->pivot->created_at, // 中間テーブルの作成日
            'membership_id' => $this->pivot->id, // 必要であれば中間テーブル自体のID
        ];
    }
}
