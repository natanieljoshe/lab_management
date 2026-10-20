<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class FeedbackResponse extends Model
{
    use HasUuids;

    protected $fillable = [
        'feedback_id',
        'responder_id',
        'message',
        'status_change',
    ];

    public function feedback()
    {
        return $this->belongsTo(Feedback::class, 'feedback_id');
    }

    public function responder()
    {
        return $this->belongsTo(User::class, 'responder_id');
    }
}
