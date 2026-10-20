<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class LabSession extends Model
{
    use HasUuids;

    protected $fillable = [
        'schedule_id',
        'class_section_id',
        'session_type',
        'duration_min',
        'status',
        'lab_id',
        'day_of_week',
        'start_min',
        'end_min',
        'unscheduled_reason',
        'is_locked',
        'is_manual_placed',
        'is_overridden',
        'violations',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
        'is_manual_placed' => 'boolean',
        'is_overridden' => 'boolean',
        'violations' => 'array',
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

    public function classSection()
    {
        return $this->belongsTo(ClassSection::class, 'class_section_id');
    }

    public function lab()
    {
        return $this->belongsTo(Labs::class, 'lab_id');
    }
}
