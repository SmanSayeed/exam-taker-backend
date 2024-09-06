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
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('question_id');
            $table->enum('question_type', ['mcq', 'creative', 'normal']);
            $table->boolean('is_answer_submitted')->default(false);
            $table->boolean('is_exam_time_out')->default(false);
            $table->json('mcq_answers')->nullable(); // JSON for storing MCQ answers
            $table->json('creative_answers')->nullable(); // For Creative type

            $table->json('normal_answers')->nullable(); // For Normal type
            $table->decimal('obtained_mark', 8, 2)->nullable();
            $table->timestamp('exam_start_time')->nullable();
            $table->timestamp('submission_time')->nullable();
            $table->boolean('is_second_timer')->default(false)->nullable(); // For admission test
            $table->boolean('status')->default(true); // Managed by admin
            $table->softDeletes(); // For soft delete
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
