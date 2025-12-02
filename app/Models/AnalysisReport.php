<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalysisReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'report_type',
        'analytics_data',
        'heatmap_data',
        'ai_generated_summary',
        'generated_at',
    ];

    protected $casts = [
        'analytics_data' => 'array',
        'heatmap_data' => 'array',
        'generated_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(VideoSession::class, 'session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

