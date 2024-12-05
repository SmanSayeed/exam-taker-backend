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
        Schema::create('student_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade'); // Relates to the subscription
            $table->foreignId('package_id')->constrained()->onDelete('cascade');
            $table->string('payment_method'); // Payment method (bkash, nagad, rocket)
            $table->string('mobile_number')->nullable(); // Mobile banking number, consider making it nullable if needed
            $table->string('transaction_id')->unique(); // Unique transaction ID, ensure it's unique as per your logic
            $table->decimal('amount', 10, 2); // Amount paid
            $table->string('coupon')->nullable(); // Coupon code field
            $table->boolean('verified')->default(false); // Admin approval status
            $table->dateTime('verified_at')->nullable(); // Nullable approval timestamp
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_payments');
    }
};
