<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();

            $table->string('name');
            $table->string('username')->unique();
            $table->date('date');
            $table->string('gender')->nullable();

            $table->string('cpf')->nullable()->unique();
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('password')->nullable();

            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();

            $table->integer('qtd_login')->default(0);
            $table->timestamp('last_login')->nullable();
            $table->enum('type', ['normal', 'test', 'demo'])->default('normal');
            $table->string('lang', 5)->default('pt');

            $table->timestamp('email_verified_at')->nullable()->index();
            $table->timestamp('phone_verified_at')->nullable();

            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();

            $table->string('asaas_customer_id')->nullable();

            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(false);

            $table->foreignId('profile_id')->nullable()->constrained('profiles');

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
