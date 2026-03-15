<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('club_memberships', function (Blueprint $table): void {
            $table->boolean('is_club_admin')->default(false)->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('club_memberships', function (Blueprint $table): void {
            $table->dropColumn('is_club_admin');
        });
    }
};