<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectCompletion extends Model
{
    protected $fillable = [
        'project_id', 'company_id', 'invoice_number',
        'notes', 'terms_template_id',
        'subtotal', 'total', 'paid_amount', 'payment_status', 'created_by',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function termsTemplate(): BelongsTo
    {
        return $this->belongsTo(TermsTemplate::class);
    }

    // Live-derived: sum of credit finance entries on the project.
    public function getPaidAmountAttribute(): float
    {
        return (float) FinanceEntry::where('project_id', $this->project_id)
            ->where('type', 'credit')
            ->sum('amount');
    }

    public function getPaymentStatusAttribute(): string
    {
        $paid  = $this->paid_amount;
        $total = (float) $this->attributes['total'] ?? 0;
        return match(true) {
            $paid <= 0          => 'unpaid',
            $paid >= $total     => 'paid',
            default             => 'partial',
        };
    }

    public function getDueAmountAttribute(): float
    {
        return max(0, (float) $this->total - $this->paid_amount);
    }

    public function recalculate(): void
    {
        $subtotal = (float) $this->items()->sum(\DB::raw('qty * rate'));
        $this->update([
            'subtotal' => $subtotal,
            'total'    => $subtotal,
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
