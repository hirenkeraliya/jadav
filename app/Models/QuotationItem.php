<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationItem extends Model
{
    protected $fillable = [
        'quotation_id', 'name', 'description', 'qty', 'unit', 'unit_rate', 'amount', 'sort_order',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'unit_rate' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }
}
