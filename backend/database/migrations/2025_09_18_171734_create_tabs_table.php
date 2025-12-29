<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tabs', function (Blueprint $table) {
            $table->id();

            $table->string('code')->unique();
            $table->string('customer_name');
            $table->string('status')->default('aberto');

            $table->timestamp('opened_at')->useCurrent();
            $table->timestamp('closed_at')->nullable();

            $table->decimal('total_amount', 12, 2)->nullable()->default(0);

            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_form_id')->nullable()->constrained('payment_forms')->onDelete('restrict');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tabs');
    }
};
