<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pdfs', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // The title of the PDF document
            $table->string('file_path')->nullable(); // Path to the stored PDF file (local) or URL
            $table->string('file_link')->nullable(); // Optional external URL to the PDF file
            $table->string('mime_type')->default('application/pdf'); // Default MIME type for PDF
            $table->text('description')->nullable(); // Optional description for the document

            // Polymorphic relationship columns
            $table->nullableMorphs('pdfable'); // Creates `pdfable_id` and `pdfable_type` for polymorphic relations

            $table->timestamps();
            $table->softDeletes(); // Soft delete for PDFs
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pdfs');
    }
};
