<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'task_name' => $this->task_name,
            'description' => $this->description,
            'position' => $this->position,
            'project_id' => $this->project_id,
            'status_id' => $this->status_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Relations chargées
            'project' => [
                'id' => $this->project->id,
                'project_name' => $this->project->project_name,
            ],

            'status' => [
                'id' => $this->status->id,
                'status_name' => $this->status->status_name,
                'color' => $this->status->color,
            ]
        ];
    }
}
