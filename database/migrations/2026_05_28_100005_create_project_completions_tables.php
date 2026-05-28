<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_number')->unique();
            $table->text('notes')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('project_completion_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('completion_id')->constrained('project_completions')->cascadeOnDelete();
            $table->string('description');
            $table->decimal('qty', 10, 2)->default(1);
            $table->decimal('rate', 15, 2)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('project_completion_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('completion_id')->constrained('project_completions')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->string('reference', 100)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_completion_payments');
        Schema::dropIfExists('project_completion_items');
        Schema::dropIfExists('project_completions');
    }
};
