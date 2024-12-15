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
        //need duration and price
        Schema::create('packages', function (Blueprint $table) {
            $table->id(); // Primary key (id)
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true); // Whether the package is active or not
            $table->integer('duration_days')->nullable(); // Duration of the package
            $table->float('price')->nullable(); // Price of the package
            $table->string('img')->nullable(); // Add image column
            $table->decimal('discount', 8, 2)->nullable(); // Add discount column
            $table->enum('discount_type', ['percentage', 'amount'])->nullable(); // Add discount type column
            $table->timestamps(); // Automatically adds created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
