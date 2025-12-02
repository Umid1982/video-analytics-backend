<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Detection extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'frame_number',
        'class_name',
        'confidence',
        'bbox_x',
        'bbox_y',
        'bbox_width',
        'bbox_height',
        'track_id',
        'detected_at',
    ];

    protected $casts = [
        'detected_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(VideoSession::class, 'session_id');
    }
}

