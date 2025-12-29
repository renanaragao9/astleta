<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('type')->default('saida');
            $table->text('description')->nullable();
            $table->date('due_date')->nullable();
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->boolean('is_paid')->default(false);

            $table->foreignId('expense_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
