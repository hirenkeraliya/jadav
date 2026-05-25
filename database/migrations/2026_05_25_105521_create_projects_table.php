<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('project_code')->unique();
            $table->string('name');
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('project_type_id')->nullable()->constrained()->nullOnDelete();
            $table->string('location')->nullable();
            $table->string('site_address')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->foreignId('lead_by')->nullable()->constrained('users')->nullOnDelete();
            $table->longText('scope_of_work')->nullable();
            $table->decimal('estimated_amount', 15, 2)->default(0);
            $table->enum('status', ['quotation', 'pending', 'running', 'on_hold', 'delayed', 'completed', 'invoiced', 'cancelled'])->default('pending');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->text('internal_notes')->nullable();
            $table->unsignedBigInteger('quotation_id')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
