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
    ];

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
