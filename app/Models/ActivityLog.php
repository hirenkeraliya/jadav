<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'company_id', 'user_id', 'action', 'module', 'record_id',
        'description', 'old_values', 'new_values', 'ip_address', 'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public static function record(string $action, string $module, int $recordId, string $description, array $old = [], array $new = []): void
    {
        $user = auth()->user();
        static::create([
            'company_id' => $user?->active_company_id,
            'user_id'    => $user?->id,
            'action'     => $action,
            'module'     => $module,
            'record_id'  => $recordId,
            'description' => $description,
            'old_values' => $old ?: null,
            'new_values' => $new ?: null,
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
