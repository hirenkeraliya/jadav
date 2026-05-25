<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceEntry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id', 'project_id', 'type', 'finance_entry_type_id',
        'payment_type_id', 'amount', 'date', 'reference_number', 'remarks', 'recorded_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function entryType(): BelongsTo
    {
        return $this->belongsTo(FinanceEntryType::class, 'finance_entry_type_id');
    }

    public function paymentType(): BelongsTo
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function files(): HasMany
    {
        return $this->hasMany(FinanceFile::class);
    }

    public function customFieldValues(): MorphMany
    {
        return $this->morphMany(CustomFieldValue::class, 'record');
    }
}
