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
        Schema::create('model_test_question', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->foreignId('model_test_id')->constrained()->onDelete('cascade'); // Foreign Key to Model Test
            $table->foreignId('question_id')->constrained()->onDelete('cascade'); // Foreign Key to Question Bank
            $table->timestamps(); // created_at and updated_at

            // Add unique constraint to prevent duplicate model_test_id & question_id pairs
            $table->unique(['model_test_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_test_question');
    }
};
