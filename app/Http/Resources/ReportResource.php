<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
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
            'session_id' => $this->session_id,
            'report_type' => $this->report_type,
            'analytics_data' => $this->analytics_data,
            'heatmap_data' => $this->heatmap_data,
            'ai_generated_summary' => $this->ai_generated_summary,
            'generated_at' => $this->generated_at?->toISOString(),
            'session' => new SessionResource($this->whenLoaded('session')),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}

