<?php
// app/Http/Resources/ProjectMemberResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectMemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            
            // 中間テーブルのデータ
            'role' => $this->whenPivotLoaded('memberships', function () {
                return $this->pivot->role;
            }),
            'joined_at' => $this->whenPivotLoaded('memberships', function () {
                return $this->pivot->created_at?->format('Y-m-d H:i:s');
            }),
        ];
    }
}