<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_ratings', function (Blueprint $table) {
            $table->id();

            $table->text('comment')->nullable();

            $table->tinyInteger('rating')->unsigned();
            $table->tinyInteger('technical_rating')->unsigned();
            $table->tinyInteger('tactical_rating')->unsigned();
            $table->tinyInteger('physical_rating')->unsigned();
            $table->tinyInteger('mental_rating')->unsigned();
            $table->tinyInteger('teamwork_rating')->unsigned();

            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('booking_participant_id')->constrained('booking_participants')->onDelete('cascade');
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['booking_participant_id']);
            $table->index(['user_id', 'booking_id']);
            $table->index(['booking_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_ratings');
    }
};
