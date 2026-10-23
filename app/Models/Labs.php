<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Labs extends Model
{
    use HasUuids;

    protected $fillable = [
        'id',
        'name',
        'location',
        'capacity',
        'manual_only',
        'is_active',
        'sort_order',
        'notes',
    ];

    protected $casts = [
        'manual_only' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'code',
        'type',
        'is_manual_only',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function getCodeAttribute(): string
    {
        $name = strtoupper($this->name ?? '');
        if (str_contains($name, 'SISTEM INFORMASI') || $name === 'SI') return 'SI';
        if (str_contains($name, 'PEMROGRAMAN') || $name === 'PG') return 'PG';
        if (str_contains($name, 'MOBILE') || $name === 'MOBDEV') return 'MOBDEV';
        if (str_contains($name, 'STUDIO')) return 'STUDIO';
        if (str_contains($name, 'JARINGAN') || $name === 'JK') return 'JK';
        if (str_contains($name, 'CERDAS') || $name === 'SC') return 'SC';
        if (str_contains($name, 'VIRTUAL') || $name === 'VR') return 'VR';
        if (str_contains($name, 'MULTIMEDIA') || $name === 'MM') return 'MM';
        if (str_contains($name, 'PA')) return 'PA';
        if (str_contains($name, 'PB')) return 'PB';
        if (str_contains($name, 'PC')) return 'PC';
        if (str_contains($name, 'PD')) return 'PD';
        return $this->location ?: substr($this->name, 0, 8);
    }

    public function getTypeAttribute(): string
    {
        $code = $this->code;
        return in_array($code, ['PA', 'PB', 'PC', 'PD']) ? 'external' : 'internal';
    }

    public function getIsManualOnlyAttribute(): bool
    {
        return (bool) ($this->manual_only ?? false);
    }

    public function setIsManualOnlyAttribute($value): void
    {
        $this->attributes['manual_only'] = (bool) $value;
    }

    public function desks()
    {
        return $this->hasMany(Desks::class, 'lab_id');
    }

    public function items()
    {
        return $this->hasMany(Items::class, 'lab_id');
    }

    public function softwares()
    {
        return $this->belongsToMany(
            Software::class,
            'lab_softwares',
            'lab_id',
            'software_id'
        )
            ->withTimestamps();
    }

    public function bookings()
    {
        return $this->morphToMany(
            Booking::class,
            'bookable',
            'bookings_items',
            'bookable_id',
            'booking_id'
        )
            ->withPivot(['id', 'type', 'returner_id', 'returned_at', 'returned_status', 'returned_detail'])
            ->using(Bookings_item::class);
    }

    public function classMeetings()
    {
        return $this->hasMany(ClassMeeting::class, 'lab_id');
    }

    public function labSessions()
    {
        return $this->hasMany(LabSession::class, 'lab_id');
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class, 'lab_id');
    }

    public function fundTransactions()
    {
        return $this->hasMany(FundTransaction::class, 'lab_id');
    }
}
