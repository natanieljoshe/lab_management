<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Period extends Model
{
    use HasUuids;

    protected $fillable = [
        'id',
        'academic_year',
        'semester',
        'active',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'period_id');
    }

    public function repairs()
    {
        return $this->hasMany(Repair::class, 'period_id');
    }
    
    public static function getCurrentPeriod()
    {
        return self::where('active', true)->first();
    }

    public function matkulConfigs()
    {
        return $this->hasMany(MatkulConfig::class, 'period_id');
    }

    public function classSections()
    {
        return $this->hasMany(ClassSection::class, 'period_id');
    }

    public function customPaths()
    {
        return $this->hasMany(CustomPath::class, 'period_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'period_id');
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class, 'period_id');
    }

    public function fundTransactions()
    {
        return $this->hasMany(FundTransaction::class, 'period_id');
    }
}
