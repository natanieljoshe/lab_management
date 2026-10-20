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

    public function period()
    {
        return $this->belongsTo(Period::class, 'period_id');
    }

    public function matkul()
    {
        return $this->belongsTo(Matkul::class, 'matkul_id');
    }
}
