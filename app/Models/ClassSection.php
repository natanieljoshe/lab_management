<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ClassSection extends Model
{
    use HasUuids;

    protected $fillable = [
        'period_id',
        'matkul_id',
        'label',
        'enrollment',
        'lecturer',
        'class_name',
        'name',
    ];

    protected $appends = [
        'class_name',
        'name',
        'course_id',
    ];

    public function getCourseIdAttribute()
    {
        return $this->attributes['matkul_id'] ?? null;
    }

    public function setCourseIdAttribute($value)
    {
        $this->attributes['matkul_id'] = $value;
    }

    public function getClassNameAttribute()
    {
        return $this->attributes['label'] ?? null;
    }

    public function setClassNameAttribute($value)
    {
        $this->attributes['label'] = $value;
    }

    public function getNameAttribute()
    {
        return $this->attributes['label'] ?? null;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['label'] = $value;
    }

    public function course()
    {
        return $this->belongsTo(Matkul::class, 'matkul_id');
    }

    public function meetings()
    {
        return $this->hasMany(ClassMeeting::class, 'class_section_id');
    }

    public function sessions()
    {
        return $this->hasMany(LabSession::class, 'class_section_id');
    }

    public function period()
    {
        return $this->belongsTo(Period::class, 'period_id');
    }

    public function matkul()
    {
        return $this->belongsTo(Matkul::class, 'matkul_id');
    }

    public function classMeetings()
    {
        return $this->hasMany(ClassMeeting::class, 'class_section_id');
    }

    public function labSessions()
    {
        return $this->hasMany(LabSession::class, 'class_section_id');
    }
}
