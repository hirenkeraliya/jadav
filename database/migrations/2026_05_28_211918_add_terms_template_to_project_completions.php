<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_completions', function (Blueprint $table) {
            $table->foreignId('terms_template_id')->nullable()->after('notes')
                ->constrained('terms_templates')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('project_completions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('terms_template_id');
        });
    }
};
