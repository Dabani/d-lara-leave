<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveComment extends Model
{
    use HasFactory;

    protected $table = 'leave_comments';

    protected $fillable = [
        'leave_request_id',
        'user_id',
        'body',
        'type',
        'visibility',
    ];

    public function leaveRequest(): BelongsTo
    {
        return $this->belongsTo(LeaveRequest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Is this a system-generated rejection notice? */
    public function isRejectionNotice(): bool
    {
        return $this->type === 'rejection_notice';
    }
}
