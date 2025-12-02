<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class VideoSession extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'session_id',
        'status',
        'source_type',
        'source_path',
        'total_frames',
        'total_people',
        'peak_people_count',
        'average_stay_time',
        'start_time',
        'end_time',
        'metadata',
        'error_message',
    ];

    protected $casts = [
        'metadata' => 'array',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(AnalysisReport::class, 'session_id');
    }

    public function detections(): HasMany
    {
        return $this->hasMany(Detection::class, 'session_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'session_id');
    }
}

