<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fields', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image_path')->nullable();
            $table->text('description')->nullable();
            $table->decimal('price_per_hour', 8, 2)->default(0);
            $table->decimal('extra_hour_price', 8, 2)->default(0)->nullable();
            $table->boolean('is_allows_extra_hour')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('field_type_id')->constrained('field_types');
            $table->foreignId('field_surface_id')->constrained('field_surfaces');
            $table->foreignId('field_size_id')->constrained('field_sizes');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fields');
    }
};
