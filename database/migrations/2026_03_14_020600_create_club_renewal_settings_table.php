<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('club_renewal_settings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('club_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('season_label')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->decimal('fee_amount', 8, 2)->nullable();
            $table->string('fee_currency', 8)->default('SEK');
            $table->date('renewal_deadline')->nullable();
            $table->text('payment_details')->nullable();
            $table->boolean('is_open')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('club_renewal_settings');
    }
};