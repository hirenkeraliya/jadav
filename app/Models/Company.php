<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'address', 'logo',
        'primary_color', 'secondary_color', 'currency', 'currency_symbol',
        'tax_label', 'tax_number', 'website',
        'invoice_prefix', 'quotation_prefix', 'financial_year_start', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'financial_year_start' => 'integer',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('is_default')->withTimestamps();
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function projectTypes(): HasMany
    {
        return $this->hasMany(ProjectType::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function paymentTypes(): HasMany
    {
        return $this->hasMany(PaymentType::class);
    }

    public function financeEntryTypes(): HasMany
    {
        return $this->hasMany(FinanceEntryType::class);
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    public function termsTemplates(): HasMany
    {
        return $this->hasMany(TermsTemplate::class);
    }

    public function customFields(): HasMany
    {
        return $this->hasMany(CustomField::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }
}
