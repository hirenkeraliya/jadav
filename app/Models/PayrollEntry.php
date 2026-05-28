<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollEntry extends Model
{
    protected $fillable = [
        'company_id',
        'staff_id',
        'entry_date',
        'start_time',
        'end_time',
        'hours',
        'notes',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'hours'      => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}
