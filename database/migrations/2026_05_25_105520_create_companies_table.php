<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('logo')->nullable();
            $table->string('primary_color')->default('#6366f1');
            $table->string('secondary_color')->default('#f59e0b');
            $table->string('currency', 10)->default('USD');
            $table->string('currency_symbol', 10)->default('$');
            $table->string('tax_label')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('website')->nullable();
            $table->string('invoice_prefix')->default('INV-');
            $table->string('quotation_prefix')->default('QUO-');
            $table->tinyInteger('financial_year_start')->default(1)->comment('Month number 1-12');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
