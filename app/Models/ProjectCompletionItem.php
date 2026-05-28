<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectCompletionItem extends Model
{
    protected $fillable = ['completion_id', 'description', 'qty', 'rate', 'amount', 'sort_order'];

    protected $casts = [
        'qty'    => 'decimal:2',
        'rate'   => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function completion(): BelongsTo
    {
        return $this->belongsTo(ProjectCompletion::class, 'completion_id');
    }
}
