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
        Schema::create('exam_quota_subscriptions', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('student_id'); // Foreign key for the student
            $table->string('mobile_number'); // Mobile number of the student
            $table->string('payment_method'); // Payment method used
            $table->string('transaction_id'); // Transaction ID of the payment
            $table->string('coupon')->nullable(); // Optional coupon code
            $table->boolean('verified')->default(false); // Verification status
            $table->timestamp('verified_at')->nullable(); // Verification timestamp
            $table->timestamps(); // created_at and updated_at

            // Foreign key constraints
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_quota_subscriptions');
    }
};
