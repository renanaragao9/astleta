<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_statistics_bookings', function (Blueprint $table) {
            $table->id();

            $table->integer('count')->default(0);

            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->foreignId('team_booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('statistic_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['team_booking_id', 'statistic_id']);
            $table->index(['team_id', 'statistic_id', 'booking_id']);
            $table->index(['statistic_id']);
            $table->index(['booking_id']);

            $table->unique(['team_booking_id', 'statistic_id', 'booking_id'], 'unique_team_statistic_booking');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_statistics_booking');
    }
};
