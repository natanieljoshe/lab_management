<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class FundCategory extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'type',
        'description',
    ];

    public function transactions()
    {
        return $this->hasMany(FundTransaction::class, 'category_id');
    }
}
