<?php

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
                'is_archived' => $this-> is_archived,
                'created_at' => $this->created_at,
                'updated_at' => $this-> updated_at,
                'tasks' =>TaskResource::collection($this->tasks),
                'user' => UserResource::collection($this->user),
                'role' => $this->whenPivotLoaded('role', function () {
                    return $this->pivot->role;
                }),
    
        ];
    }
}
