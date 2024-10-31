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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id(); // Primary key (id)
            $table->foreignId('student_id')->constrained()->onDelete('cascade'); // Foreign key to students
            $table->foreignId('package_id')->constrained()->onDelete('cascade'); // Foreign key to packages
            $table->date('subscribed_at'); // When the subscription was created
            $table->date('expires_at')->nullable(); // When the subscription expires
            $table->boolean('is_active')->default(false); // Whether the subscription is active or not
            $table->timestamps(); // Automatically adds created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
