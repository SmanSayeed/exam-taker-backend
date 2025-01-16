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
        Schema::create('m_t_answer_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('exam_id');
            $table->string('file_url');
            $table->string('original_filename');
            $table->string('mime_type');
            $table->integer('file_size'); // in bytes
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students');
            $table->foreign('exam_id')->references('id')->on('examinations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_t_answer_files');
    }
};
