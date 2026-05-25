<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id', 'project_id', 'customer_id', 'quotation_id', 'invoice_number',
        'invoice_date', 'due_date', 'discount_type', 'discount_value',
        'tax_label', 'tax_rate', 'subtotal', 'tax_amount', 'discount_amount',
        'total', 'paid_amount', 'notes', 'status', 'terms_template_id', 'template',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'discount_value' => 'decimal:2',
    ];

    public static function generateNumber(int $companyId): string
    {
        $company = Company::find($companyId);
        $prefix = $company?->invoice_prefix ?? 'INV-';
        $count = static::where('company_id', $companyId)->withTrashed()->count() + 1;
        return $prefix . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('sort_order');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function termsTemplate(): BelongsTo
    {
        return $this->belongsTo(TermsTemplate::class);
    }

    public function getBalanceDueAttribute(): float
    {
        return (float) ($this->total - $this->paid_amount);
    }

    public function recalculate(): void
    {
        $subtotal = $this->items()->sum(\DB::raw('qty * unit_rate'));
        $discountAmount = $this->discount_type === 'percentage'
            ? $subtotal * ($this->discount_value / 100)
            : $this->discount_value;
        $taxable = $subtotal - $discountAmount;
        $taxAmount = $this->items()->sum('tax_amount');
        $paidAmount = $this->payments()->sum('amount');
        $this->update([
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'tax_amount' => $taxAmount,
            'total' => $taxable + $taxAmount,
            'paid_amount' => $paidAmount,
        ]);
        $this->updateStatus();
    }

    public function updateStatus(): void
    {
        if ($this->status === 'cancelled') return;
        $total = (float) $this->total;
        $paid = (float) $this->paid_amount;
        if ($paid <= 0) {
            $status = 'sent';
        } elseif ($paid >= $total) {
            $status = 'paid';
        } else {
            $status = 'partial';
        }
        $this->update(['status' => $status]);
    }
}
