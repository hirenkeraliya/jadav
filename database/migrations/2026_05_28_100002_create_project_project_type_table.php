<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('project_project_type')) {
            Schema::create('project_project_type', function (Blueprint $table) {
                $table->foreignId('project_id')->constrained()->cascadeOnDelete();
                $table->foreignId('project_type_id')->constrained()->cascadeOnDelete();
                $table->primary(['project_id', 'project_type_id']);
            });
        }

        // Migrate existing single type to pivot
        if (Schema::hasColumn('projects', 'project_type_id')) {
            DB::table('projects')
                ->whereNotNull('project_type_id')
                ->get(['id', 'project_type_id'])
                ->each(fn($p) => DB::table('project_project_type')->insertOrIgnore([
                    'project_id'      => $p->id,
                    'project_type_id' => $p->project_type_id,
                ]));
        }

        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['project_type_id']);
            $table->dropColumn('project_type_id');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('project_type_id')->nullable()->constrained()->nullOnDelete();
        });

        Schema::dropIfExists('project_project_type');
    }
};
