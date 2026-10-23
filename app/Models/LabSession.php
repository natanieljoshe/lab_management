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

    protected $appends = [
        'room_id',
        'day',
        'type',
        'status',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (!isset($model->attributes['duration_min'])) {
                if (!empty($model->attributes['start_min']) && !empty($model->attributes['end_min'])) {
                    $model->attributes['duration_min'] = $model->attributes['end_min'] - $model->attributes['start_min'];
                } else {
                    $model->attributes['duration_min'] = 100;
                }
            }
        });
    }

    public function section()
    {
        return $this->belongsTo(ClassSection::class, 'class_section_id');
    }

    public function room()
    {
        return $this->belongsTo(Labs::class, 'lab_id');
    }

    public function getRoomIdAttribute()
    {
        return $this->lab_id;
    }

    public function setRoomIdAttribute($value): void
    {
        $this->attributes['lab_id'] = $value;
    }

    public function getTypeAttribute(): string
    {
        return strtolower($this->session_type ?? 'praktikum');
    }

    public function setTypeAttribute($value): void
    {
        $this->attributes['session_type'] = strtoupper($value);
    }

    public function getStatusAttribute(): string
    {
        $val = strtoupper($this->attributes['status'] ?? 'UNSCHEDULED');
        return $val === 'SCHEDULED' ? 'placed' : 'unscheduled';
    }

    public function setStatusAttribute($value): void
    {
        $val = strtolower($value ?? 'unscheduled');
        $this->attributes['status'] = in_array($val, ['placed', 'scheduled']) ? 'SCHEDULED' : 'UNSCHEDULED';
    }

    public function getDayAttribute(): ?string
    {
        if ($this->day_of_week === null) return null;
        $days = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 0 => 'Minggu'];
        return $days[$this->day_of_week] ?? null;
    }

    public function setDayAttribute($value): void
    {
        if ($value === null) {
            $this->attributes['day_of_week'] = null;
            return;
        }
        $days = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 0];
        $this->attributes['day_of_week'] = is_numeric($value) ? (int)$value : ($days[$value] ?? null);
    }

    public function getPeriodIdAttribute()
    {
        return $this->schedule?->period_id ?? $this->section?->period_id;
    }

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
