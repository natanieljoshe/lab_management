<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'period_id',
        'pic_id',
        'name',
        'is_active',
        'notes',
        'snapshot_json',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'snapshot_json' => 'array',
    ];

    public function getIsPublishedAttribute(): bool
    {
        $snap = $this->snapshot_json ?? [];
        return isset($snap['is_published']) ? (bool) $snap['is_published'] : (bool) $this->is_active;
    }

    public function setIsPublishedAttribute($value): void
    {
        $snap = $this->snapshot_json ?? [];
        $snap['is_published'] = (bool) $value;
        $this->snapshot_json = $snap;
    }

    public function getPublicSlugAttribute(): ?string
    {
        $snap = $this->snapshot_json ?? [];
        return $snap['public_slug'] ?? null;
    }

    public function setPublicSlugAttribute($value): void
    {
        $snap = $this->snapshot_json ?? [];
        $snap['public_slug'] = $value;
        $this->snapshot_json = $snap;
    }

    public function sessions()
    {
        return $this->hasMany(LabSession::class, 'schedule_id');
    }

    public function period()
    {
        return $this->belongsTo(Period::class, 'period_id');
    }

    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    public function labSessions()
    {
        return $this->hasMany(LabSession::class, 'schedule_id');
    }
}
