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
        Schema::create('pdf_subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('pdf_id')->constrained()->onDelete('cascade');
            $table->enum('payment_method', ['bkash', 'nagad', 'rocket']);

            $table->string('mobile_number')->nullable(); // Mobile banking number, nullable if not always required
            $table->string('transaction_id')->unique(); // Unique transaction ID
            $table->decimal('amount', 10, 2); // Amount paid
            $table->string('coupon')->nullable(); // Coupon code field, optional
            $table->boolean('verified')->default(false); // Admin approval status
            $table->dateTime('verified_at')->nullable(); // Approval timestamp, nullable
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pdf_subscription_payments');
    }
};
