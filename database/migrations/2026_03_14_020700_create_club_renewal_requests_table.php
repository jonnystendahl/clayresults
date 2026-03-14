<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('club_renewal_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('club_id')->constrained()->cascadeOnDelete();
            $table->foreignId('club_membership_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('season_label')->nullable();
            $table->string('status')->default('pending');
            $table->text('note')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamp('submitted_at');
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();

            $table->index(['club_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('club_renewal_requests');
    }
};