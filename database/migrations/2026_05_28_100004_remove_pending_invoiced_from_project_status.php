<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Convert any existing projects with removed statuses to sensible alternatives
        DB::table('projects')->where('status', 'pending')->update(['status' => 'running']);
        DB::table('projects')->where('status', 'invoiced')->update(['status' => 'completed']);
    }

    public function down(): void
    {
        // No rollback — we can't know which projects were originally pending/invoiced
    }
};
