<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id', 'customer_id', 'quotation_number', 'version', 'parent_id', 'revised_by',
        'date', 'valid_until', 'discount_type', 'discount_value',
        'tax_label', 'tax_rate', 'subtotal', 'tax_amount', 'discount_amount', 'total',
        'notes', 'status', 'terms_template_id',
    ];

    protected $casts = [
        'date' => 'date',
        'valid_until' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'discount_value' => 'decimal:2',
    ];

    public static function generateNumber(int $companyId): string
    {
        $company = Company::find($companyId);
        $prefix = $company?->quotation_prefix ?? 'QUO-';
        $count = static::where('company_id', $companyId)->withTrashed()->count() + 1;
        return $prefix . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class)->orderBy('sort_order');
    }

    public function termsTemplate(): BelongsTo
    {
        return $this->belongsTo(TermsTemplate::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Quotation::class, 'parent_id');
    }

    public function revisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revised_by');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(Quotation::class, 'parent_id');
    }

    public function project(): HasOne
    {
        return $this->hasOne(Project::class, 'quotation_id');
    }

    public function recalculate(): void
    {
        $subtotal      = (float) $this->items()->sum(\DB::raw('qty * unit_rate'));
        $discountValue = (float) ($this->discount_value ?? 0);
        $taxRate       = (float) ($this->tax_rate ?? 0);
        $discountAmount = $this->discount_type === 'percentage'
            ? $subtotal * ($discountValue / 100)
            : $discountValue;
        $taxable   = $subtotal - $discountAmount;
        $taxAmount = $taxable * ($taxRate / 100);
        $this->update([
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'tax_amount' => $taxAmount,
            'total' => $taxable + $taxAmount,
        ]);
    }
}
