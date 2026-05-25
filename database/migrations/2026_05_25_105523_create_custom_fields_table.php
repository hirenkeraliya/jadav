<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('module')->comment('projects, customers, finance_entries');
            $table->string('label');
            $table->string('field_key');
            $table->enum('type', ['text', 'textarea', 'number', 'date', 'toggle', 'select', 'multiselect', 'file', 'url', 'color']);
            $table->json('options')->nullable()->comment('For select/multiselect types');
            $table->string('placeholder')->nullable();
            $table->string('default_value')->nullable();
            $table->boolean('is_required')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_fields');
    }
};
