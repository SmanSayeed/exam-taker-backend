<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionablesTable extends Migration
{
    public function up()
    {
        Schema::create('questionables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->unique()->constrained('questions')->onDelete('cascade');
            $table->unsignedBigInteger('section_id')->nullable();
            $table->unsignedBigInteger('exam_type_id')->nullable();
            $table->unsignedBigInteger('exam_sub_type_id')->nullable();
            $table->unsignedBigInteger('group_id')->nullable();
            $table->unsignedBigInteger('level_id')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->unsignedBigInteger('lesson_id')->nullable();
            $table->unsignedBigInteger('topic_id')->nullable();
            $table->unsignedBigInteger('sub_topic_id')->nullable();
            $table->timestamps();

            // Specify a shorter name for the index
            $table->index([
                'section_id', 'exam_type_id', 'exam_sub_type_id',
                'group_id', 'level_id', 'subject_id',
                'lesson_id', 'topic_id', 'sub_topic_id'
            ], 'questionables_composite_index'); // Custom index name

            // Foreign key constraints
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('set null');
            $table->foreign('exam_type_id')->references('id')->on('exam_types')->onDelete('set null');
            $table->foreign('exam_sub_type_id')->references('id')->on('exam_sub_types')->onDelete('set null');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('set null');
            $table->foreign('level_id')->references('id')->on('levels')->onDelete('set null');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('set null');
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('set null');
            $table->foreign('topic_id')->references('id')->on('topics')->onDelete('set null');
            $table->foreign('sub_topic_id')->references('id')->on('sub_topics')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('questionables');
    }
}
