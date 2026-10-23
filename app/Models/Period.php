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

    public function getNameAttribute(): string
    {
        return ucfirst(strtolower($this->semester ?? 'Gasal')) . ' ' . ($this->academic_year ?? '');
    }

    public function setNameAttribute($value): void
    {
        if (preg_match('/^(Gasal|Genap)\s+(.+)$/i', trim($value), $m)) {
            $this->attributes['semester'] = strtoupper($m[1]);
            $this->attributes['academic_year'] = trim($m[2]);
        }
    }

    public function getIsActiveAttribute(): bool
    {
        return (bool) ($this->active ?? false);
    }

    public function setIsActiveAttribute($value): void
    {
        $this->attributes['active'] = (bool) $value;
    }

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
