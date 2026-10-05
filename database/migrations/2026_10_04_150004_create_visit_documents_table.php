<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime_type', 100);
            $table->unsignedInteger('file_size')->comment('Ukuran file dalam bytes');
            $table->string('caption')->nullable();
            $table->timestamps();

            $table->index('visit_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_documents');
    }
};
