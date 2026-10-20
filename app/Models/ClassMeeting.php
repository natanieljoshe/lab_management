<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ClassMeeting extends Model
{
    use HasUuids;

    protected $fillable = [
        'class_section_id',
        'day_of_week',
        'start_min',
        'end_min',
        'lab_id',
        'room_label',
        'raw_text',
    ];

    public function classSection()
    {
        return $this->belongsTo(ClassSection::class, 'class_section_id');
    }

    public function lab()
    {
        return $this->belongsTo(Labs::class, 'lab_id');
    }
}
