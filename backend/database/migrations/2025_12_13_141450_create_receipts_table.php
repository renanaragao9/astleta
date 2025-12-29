<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->string('status')->default('pendente');
            $table->string('file_path')->nullable();
            $table->text('description')->nullable();
            $table->date('issued_at');
            $table->date('paymente_date')->nullable();
            $table->decimal('amount', 10, 2);
            $table->bigInteger('asaas_payment_id')->nullable();
            $table->bigInteger('asaas_customer_id')->nullable();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
