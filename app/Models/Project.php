<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id', 'project_code', 'name', 'customer_id', 'project_type_id',
        'location', 'site_address', 'start_date', 'end_date', 'lead_by',
        'scope_of_work', 'estimated_amount', 'status', 'priority',
        'internal_notes', 'quotation_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'estimated_amount' => 'decimal:2',
    ];

    public static function generateCode(int $companyId): string
    {
        $last = static::where('company_id', $companyId)
            ->withTrashed()
            ->where('project_code', 'like', 'PRJ-%')
            ->orderByRaw("CAST(SUBSTR(project_code, 5) AS INTEGER) DESC")
            ->value('project_code');

        $next = $last ? ((int) substr($last, 4)) + 1 : 1;

        // Ensure uniqueness including soft-deleted rows (unique constraint applies to all rows)
        while (static::withTrashed()->where('project_code', 'PRJ-' . str_pad($next, 4, '0', STR_PAD_LEFT))->exists()) {
            $next++;
        }

        return 'PRJ-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function projectType(): BelongsTo
    {
        return $this->belongsTo(ProjectType::class);
    }

    public function leadBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lead_by');
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(ProjectFile::class);
    }

    public function financeEntries(): HasMany
    {
        return $this->hasMany(FinanceEntry::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function customFieldValues(): MorphMany
    {
        return $this->morphMany(CustomFieldValue::class, 'record');
    }

    public function getTotalReceivedAttribute(): float
    {
        return (float) $this->financeEntries()->where('type', 'credit')->sum('amount');
    }

    public function getTotalExpenseAttribute(): float
    {
        return (float) $this->financeEntries()->where('type', 'debit')->sum('amount');
    }

    public function getProfitLossAttribute(): float
    {
        return $this->total_received - $this->total_expense;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'quotation'  => '#f59e0b',
            'pending'    => '#6366f1',
            'running'    => '#10b981',
            'on_hold'    => '#f97316',
            'delayed'    => '#ef4444',
            'completed'  => '#3b82f6',
            'invoiced'   => '#8b5cf6',
            'cancelled'  => '#6b7280',
            default      => '#6b7280',
        };
    }
}
