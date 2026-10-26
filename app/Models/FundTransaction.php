<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FundTransaction extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'category_id',
        'period_id',
        'lab_id',
        'amount',
        'description',
        'transaction_date',
        'receipt_number',
        'receipt_file',
        'recorded_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
        'approved_at' => 'datetime',
    ];

    protected $appends = ['type'];

    public function category()
    {
        return $this->belongsTo(FundCategory::class, 'category_id');
    }

    public function period()
    {
        return $this->belongsTo(Period::class, 'period_id');
    }

    public function lab()
    {
        return $this->belongsTo(Labs::class, 'lab_id');
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getTypeAttribute(){
        return $this->category?->type;
    }
}
