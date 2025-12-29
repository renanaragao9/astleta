<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('athlete_profiles', function (Blueprint $table) {
            $table->id();

            $table->string('dominant_side')->nullable();
            $table->text('bio')->nullable();

            $table->decimal('height', 4, 2)->nullable();
            $table->decimal('weight', 5, 2)->nullable();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('sport_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('position_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subposition_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->foreignId('feature_id')->nullable()->constrained('features')->nullOnDelete();
            $table->foreignId('subfeature_id')->nullable()->constrained('features')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('athlete_profiles');
    }
};
