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
            $table->timestamp('end_time');
            $table->boolean('is_negative_mark_applicable')->default(false);
            $table->json('section_categories')->nullable(); // JSON for section categories: Section → exam_types → exam_sub_types: Group→Level→Subject→lesson→topics→sub_topics
            $table->json('subject_categories')->nullable(); // JSON for subject categories

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
