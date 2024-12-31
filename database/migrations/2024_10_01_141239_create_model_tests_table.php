<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('model_tests', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('package_id')->constrained()->onDelete('cascade'); // Foreign key referencing packages table
            $table->string('title'); // Title of the model test
            $table->text('description')->nullable(); // Description of the model test, nullable
            $table->dateTime('start_time'); // Start time of the model test
            $table->dateTime('end_time'); // End time of the model test
            $table->boolean('is_active')->default(true); // Status indicating if the test is active
            $table->decimal('pass_mark', 8, 2); // Pass mark for the test
            $table->decimal('full_mark', 8, 2); // Full mark for the test
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_tests');
    }
};
