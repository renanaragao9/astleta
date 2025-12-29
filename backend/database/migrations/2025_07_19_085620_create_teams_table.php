<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('name');
            $table->string('nickname')->nullable();
            $table->string('stadium_name')->nullable();
            $table->string('primary_color', 7)->nullable();
            $table->string('secondary_color', 7)->nullable();
            $table->string('shield_path')->nullable();
            $table->string('website')->nullable();
            $table->text('description')->nullable();
            $table->text('welcome_email')->nullable();
            $table->date('founded_date')->nullable();
            $table->integer('max_members')->default(30);
            $table->boolean('is_public')->default(true);

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('sport_id')->constrained('sports')->onDelete('cascade');
            $table->foreignId('team_type_id')->constrained('team_types')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
