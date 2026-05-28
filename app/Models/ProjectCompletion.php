<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectCompletion extends Model
{
    protected $fillable = [
        'project_id', 'company_id', 'invoice_number',
        'notes', 'subtotal', 'total', 'paid_amount', 'payment_status', 'created_by',
    ];

    protected $casts = [
        'subtotal'     => 'decimal:2',
        'total'        => 'decimal:2',
        'paid_amount'  => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProjectCompletionItem::class, 'completion_id')->orderBy('sort_order');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ProjectCompletionPayment::class, 'completion_id')->latest('date');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getDueAmountAttribute(): float
    {
        return max(0, (float) $this->total - (float) $this->paid_amount);
    }

    public function recalculate(): void
    {
        $subtotal = $this->items()->sum(\DB::raw('qty * rate'));
        $paid     = $this->payments()->sum('amount');
        $status   = match(true) {
            $paid <= 0              => 'unpaid',
            $paid >= $subtotal      => 'paid',
            default                 => 'partial',
        };
        $this->update([
            'subtotal'       => $subtotal,
            'total'          => $subtotal,
            'paid_amount'    => $paid,
            'payment_status' => $status,
        ]);
    }

    public static function generateNumber(int $companyId): string
    {
        $company = Company::find($companyId);
        $prefix  = $company?->invoice_prefix ?? 'INV-';
        $count   = static::where('company_id', $companyId)->count() + 1;
        return $prefix . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
