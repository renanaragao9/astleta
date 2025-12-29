<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique()->index();
            $table->string('name');
            $table->string('status')->default('pendente');
            $table->string('type');
            $table->string('cnpj')->nullable()->index();
            $table->string('cpf', 11)->nullable()->index();
            $table->string('phone')->unique();
            $table->string('image_path')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_open')->default(false);
            $table->boolean('is_free')->default(false);
            $table->bigInteger('asaas_customer_id')->nullable()->unique();
            $table->bigInteger('asaas_sub_account_id')->nullable()->index();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
