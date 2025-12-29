<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tab_items', function (Blueprint $table) {
            $table->id();

            $table->string('observation')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('total', 12, 2);

            $table->foreignId('tab_id')->constrained('tabs')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tab_items');
    }
};
