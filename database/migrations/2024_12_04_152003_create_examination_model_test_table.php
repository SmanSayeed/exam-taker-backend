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
        Schema::create('examination_model_test', function (Blueprint $table) {
            $table->id();
            $table->foreignId('model_test_id')->constrained('model_tests')->onDelete('cascade');
            $table->foreignId('examination_id')->constrained('examinations')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examination_model_test');
    }
};
