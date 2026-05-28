<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Remap legacy values onto the new 3-value set.
        DB::table('quotations')->whereIn('status', ['draft', 'accepted'])->update(['status' => 'sent']);
        DB::table('quotations')->where('status', 'expired')->update(['status' => 'rejected']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE quotations MODIFY status ENUM('sent', 'rejected', 'converted') NOT NULL DEFAULT 'sent'");
        }
        // SQLite stores enums as TEXT with no real check constraint — application validation enforces the set.
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE quotations MODIFY status ENUM('draft', 'sent', 'accepted', 'rejected', 'expired', 'converted') NOT NULL DEFAULT 'draft'");
        }
    }
};
