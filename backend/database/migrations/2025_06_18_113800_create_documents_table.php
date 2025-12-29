<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->foreignId('document_type_id')->constrained()->onDelete('cascade');
            $table->string('file_path')->nullable();

            $table->string('status')->default('pendente');
            $table->text('description')->nullable();

            $table->morphs('documentable');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
}
