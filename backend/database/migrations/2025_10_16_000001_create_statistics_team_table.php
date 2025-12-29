<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('statistics_teams', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('icon')->nullable();
            $table->string('color')->nullable();

            $table->foreignId('sport_id')->constrained();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statistics_team');
    }
};
