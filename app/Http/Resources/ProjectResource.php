<?php
// app/Http/Resources/ProjectResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
            'is_archived' => $this->is_archived,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            
            // リレーション
            'members' => ProjectMemberResource::collection($this->whenLoaded('users')),
            'tasks' => ProjectTaskResource::collection($this->whenLoaded('tasks')),
            
        ];
    }
}