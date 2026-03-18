<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
            'statement' => $this->statement,
            'is_completed' => $this->is_completed,
            'task_date' => $this->task_date,
            'priority' => $this->priority,
            'position' => $this->position,
        ];
    }
}
