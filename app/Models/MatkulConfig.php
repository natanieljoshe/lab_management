<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MatkulConfig extends Model
{
    use HasUuids;

    protected $fillable = [
        'period_id',
        'matkul_id',
        'scheduling_semester',
        'practicum_hours',
        'responsi_hours',
        'is_manual',
    ];

    protected $casts = [
        'is_manual' => 'boolean',
        'practicum_hours' => 'decimal:2',
        'responsi_hours' => 'decimal:2',
    ];

    protected $appends = [
        'course_id',
        'schedule_semester',
    ];

    public function getCourseIdAttribute()
    {
        return $this->attributes['matkul_id'] ?? null;
    }

    public function setCourseIdAttribute($value): void
    {
        $this->attributes['matkul_id'] = $value;
    }

    public function getScheduleSemesterAttribute(): ?int
    {
        return $this->scheduling_semester ? (int) $this->scheduling_semester : null;
    }

    public function setScheduleSemesterAttribute($value): void
    {
        $this->attributes['scheduling_semester'] = $value;
    }

    public function course()
    {
        return $this->belongsTo(Matkul::class, 'matkul_id');
    }

    public function period()
    {
        return $this->belongsTo(Period::class, 'period_id');
    }

    public function matkul()
    {
        return $this->belongsTo(Matkul::class, 'matkul_id');
    }
}
