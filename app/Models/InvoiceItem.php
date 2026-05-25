<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id', 'name', 'description', 'qty', 'unit',
        'unit_rate', 'tax_rate', 'tax_amount', 'amount', 'sort_order',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'unit_rate' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
