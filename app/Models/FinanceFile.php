<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinanceFile extends Model
{
    protected $fillable = ['finance_entry_id', 'original_name', 'path', 'mime_type', 'size'];

    public function financeEntry(): BelongsTo
    {
        return $this->belongsTo(FinanceEntry::class);
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }
}
