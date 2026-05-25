<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TermsTemplate extends Model
{
    protected $fillable = [
        'company_id', 'name', 'content', 'is_default_quotation', 'is_default_invoice',
    ];

    protected $casts = [
        'is_default_quotation' => 'boolean',
        'is_default_invoice' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
