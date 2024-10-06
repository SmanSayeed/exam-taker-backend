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
        Schema::create('package_plans', function (Blueprint $table) {
            $table->id(); // Primary key (id)
            $table->foreignId('package_id')->constrained()->onDelete('cascade'); // Foreign key to packages table
            $table->integer('duration_days'); // Duration of the package in days
            $table->decimal('price', 8, 2); // Price of the package
            $table->boolean('is_active')->default(true); // Whether the package is active or not
            $table->timestamps(); // Automatically adds created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_plans');
    }
};
