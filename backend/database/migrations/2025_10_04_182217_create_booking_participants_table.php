<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_participants', function (Blueprint $table) {
            $table->id();

            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('status')->default('pendente');

            $table->datetime('paid_at')->nullable();
            $table->datetime('confirmed_at')->nullable();

            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->boolean('is_paid')->default(false);

            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('added_by_user_id')->constrained('users')->onDelete('restrict');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['booking_id', 'status']);
            $table->index(['user_id']);
            $table->index(['added_by_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_participants');
    }
};
