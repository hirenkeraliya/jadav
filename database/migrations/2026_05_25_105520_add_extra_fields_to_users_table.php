<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('avatar')->nullable()->after('phone');
            $table->boolean('is_super_admin')->default(false)->after('avatar');
            $table->unsignedBigInteger('active_company_id')->nullable()->after('is_super_admin');
            $table->unsignedBigInteger('impersonating_id')->nullable()->after('active_company_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'avatar', 'is_super_admin', 'active_company_id', 'impersonating_id']);
        });
    }
};
