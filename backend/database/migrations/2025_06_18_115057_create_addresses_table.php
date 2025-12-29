<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('zipcode');
            $table->string('country');
            $table->string('state');
            $table->string('city');
            $table->string('district')->nullable();
            $table->string('street');
            $table->string('number')->nullable();
            $table->string('complement')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->foreignId('address_type_id')->nullable()->constrained()->nullOnDelete();
            $table->morphs('addressable');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
}
