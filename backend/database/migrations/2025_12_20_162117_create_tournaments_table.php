<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('status');
            $table->string('description')->nullable();
            $table->string('awards')->nullable();
            $table->string('welcome_email')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('max_participants');
            $table->boolean('is_public')->default(false);
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
