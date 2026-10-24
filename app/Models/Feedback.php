<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feedback extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'feedbacks';

    public const STATUS_OPEN = 1;
    public const STATUS_IN_PROGRESS = 2;
    public const STATUS_RESOLVED = 3;
    public const STATUS_CLOSED = 4;
    public const STATUS_REJECTED = 5;

    public static function statusLabels(): array
    {
        return [
            self::STATUS_OPEN => 'Open',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_RESOLVED => 'Resolved',
            self::STATUS_CLOSED => 'Closed',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status] ?? 'Unknown';
    }

    protected $fillable = [
        'ticket_number',
        'type',
        'subject',
        'description',
        'reporter_id',
        'lab_id',
        'status',
        'period_id',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $feedback) {
            if (empty($feedback->ticket_number)) {
                $feedback->ticket_number = 'FB-'.now()->format('Ymd').'-'.str_pad(
                    self::withTrashed()->whereDate('created_at', today())->count() + 1,
                    3,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function lab()
    {
        return $this->belongsTo(Labs::class, 'lab_id');
    }

    public function period()
    {
        return $this->belongsTo(Period::class, 'period_id');
    }

    public function responses()
    {
        return $this->hasMany(FeedbackResponse::class, 'feedback_id');
    }
}
