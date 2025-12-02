<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SessionResource extends JsonResource
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
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'session_id' => $this->session_id,
            'status' => $this->status,
            'source_type' => $this->source_type,
            'source_path' => $this->source_path,
            'total_frames' => $this->total_frames,
            'total_people' => $this->total_people,
            'peak_people_count' => $this->peak_people_count,
            'average_stay_time' => $this->average_stay_time,
            'start_time' => $this->start_time?->toISOString(),
            'end_time' => $this->end_time?->toISOString(),
            'error_message' => $this->error_message,
        ];
    }
}

