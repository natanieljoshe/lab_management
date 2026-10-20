<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CustomPath extends Model
{
    use HasUuids;

    protected $fillable = [
        'period_id',
        'semester',
        'name',
        'section_ids_json',
    ];

    protected $casts = [
        'section_ids_json' => 'array',
    ];

    public function period()
    {
        return $this->belongsTo(Period::class, 'period_id');
    }
}
