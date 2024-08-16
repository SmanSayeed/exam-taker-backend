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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('description')->nullable();
            $table->string('images')->nullable();
            $table->boolean('is_paid');
            $table->boolean('is_featured');
            $table->enum('type', ['mcq', 'creative', 'normal']);
            $table->integer('mark');
            $table->boolean('status')->default(true);
            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};

