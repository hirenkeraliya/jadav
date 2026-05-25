<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_column_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('module');
            $table->json('columns')->nullable();
            $table->json('filters')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'company_id', 'module']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_column_preferences');
    }
};
