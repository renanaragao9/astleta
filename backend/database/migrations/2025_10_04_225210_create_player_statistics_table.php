<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_statistics', function (Blueprint $table) {
            $table->id();

            $table->integer('count')->default(0);

            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('booking_participant_id')->constrained('booking_participants')->onDelete('cascade');
            $table->foreignId('statistic_id')->constrained('statistics')->onDelete('cascade');
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['booking_participant_id', 'statistic_id']);
            $table->index(['user_id', 'statistic_id', 'booking_id']);
            $table->index(['statistic_id']);
            $table->index(['booking_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_statistics');
    }
};
