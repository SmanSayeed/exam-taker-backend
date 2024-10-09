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
        Schema::create('package_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('package_id'); // Foreign key to Package
            $table->unsignedBigInteger('section_id')->nullable(); // Foreign key to Section
            $table->unsignedBigInteger('exam_type_id')->nullable(); // Foreign key to Exam Type
            $table->unsignedBigInteger('exam_sub_type_id')->nullable(); // Foreign key to Exam Sub-Type
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('set null');
            $table->foreign('exam_type_id')->references('id')->on('exam_types')->onDelete('set null');
            $table->foreign('exam_sub_type_id')->references('id')->on('exam_sub_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_categories');
    }
};
