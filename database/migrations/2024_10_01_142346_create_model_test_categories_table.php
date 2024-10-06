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
        Schema::create('model_test_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('model_test_id'); // Foreign key to ModelTest
            $table->unsignedBigInteger('group_id')->nullable();
            $table->unsignedBigInteger('level_id')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->unsignedBigInteger('lesson_id')->nullable();
            $table->unsignedBigInteger('topic_id')->nullable();
            $table->unsignedBigInteger('sub_topic_id')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('model_test_id')->references('id')->on('model_tests')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('set null');
            $table->foreign('level_id')->references('id')->on('levels')->onDelete('set null');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('set null');
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('set null');
            $table->foreign('topic_id')->references('id')->on('topics')->onDelete('set null');
            $table->foreign('sub_topic_id')->references('id')->on('sub_topics')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_test_categories');
    }
};
