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
        Schema::create('examinations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['mcq', 'creative', 'normal']);
            $table->boolean('is_paid')->default(false);
            $table->unsignedBigInteger('created_by');
            $table->enum('created_by_role', ['admin', 'student']);
            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable();
            $table->timestamp('student_ended_at')->nullable();
            $table->float('time_limit', 8, 2)->nullable();
            $table->boolean('is_negative_mark_applicable')->default(false);
            $table->json('section_id')->nullable();
            $table->json('exam_type_id')->nullable();
            $table->json('exam_sub_type_id')->nullable();
            $table->json('group_id')->nullable();
            $table->json('subject_id')->nullable();
            $table->json('level_id')->nullable();
            $table->json('lesson_id')->nullable();
            $table->json('topic_id')->nullable();
            $table->json('sub_topic_id')->nullable();
            $table->boolean('is_optional')->default(false)->nullable();
            $table->boolean('is_active')->default(false)->nullable();
            $table->string('model_test_id')->nullable();
            $table->json('questions'); // JSON for storing question IDs
            $table->boolean('is_reviewed')->nullable()->default(false);
            $table->softDeletes(); // For soft delete
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examinations');
    }
};
