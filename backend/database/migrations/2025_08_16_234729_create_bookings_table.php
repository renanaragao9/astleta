<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('public_id');

            $table->string('booking_number')->unique();
            $table->string('payment_type')->default('online');
            $table->string('booking_status')->default('pendente');

            $table->text('notes')->nullable();
            $table->text('cancellation_reason')->nullable();

            $table->date('booking_date');
            $table->date('booking_end_date')->nullable();
            $table->time('start_time');
            $table->time('end_time');

            $table->decimal('base_price', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);

            $table->integer('duration_minutes');
            $table->boolean('is_extra_hour')->default(false);

            $table->bigInteger('asaas_payment_id')->nullable();
            $table->bigInteger('asaas_customer_id')->nullable();

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('field_id')->constrained('fields')->onDelete('cascade');
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->nullOnDelete();
            $table->foreignId('payment_form_id')->constrained('payment_forms')->onDelete('restrict');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['booking_date', 'field_id']);
            $table->index(['user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
