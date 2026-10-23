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

    protected $appends = [
        'room_id',
        'day',
    ];

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

    public function getDayAttribute(): string
    {
        $days = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 0 => 'Minggu'];
        return $days[$this->day_of_week] ?? 'Senin';
    }

    public function setDayAttribute($value): void
    {
        $days = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 0];
        $this->attributes['day_of_week'] = is_numeric($value) ? (int)$value : ($days[$value] ?? 1);
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
