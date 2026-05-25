<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CustomFieldValue extends Model
{
    protected $fillable = ['custom_field_id', 'record_type', 'record_id', 'value'];

    public function customField(): BelongsTo
    {
        return $this->belongsTo(CustomField::class);
    }

    public function record(): MorphTo
    {
        return $this->morphTo();
    }
}
