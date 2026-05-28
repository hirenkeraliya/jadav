<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectCompletionPayment extends Model
{
    protected $fillable = ['completion_id', 'amount', 'date', 'reference', 'notes', 'recorded_by'];

    protected $casts = [
        'date'   => 'date',
        'amount' => 'decimal:2',
    ];

    public function completion(): BelongsTo
    {
        return $this->belongsTo(ProjectCompletion::class, 'completion_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
