<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_bookings', function (Blueprint $table) {
            $table->id();

            $table->string('result')->nullable();

            $table->integer('goals_home')->default(0);
            $table->integer('goals_away')->default(0);
            $table->boolean('is_friendly')->default(true);

            $table->foreignId('home_team_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('away_team_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('sport_id')->nullable()->constrained('sports')->onDelete('cascade');
            $table->foreignId('winner_id')->nullable()->constrained('teams')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['home_team_id', 'away_team_id', 'booking_id']);
            $table->index(['booking_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_booking');
    }
};
