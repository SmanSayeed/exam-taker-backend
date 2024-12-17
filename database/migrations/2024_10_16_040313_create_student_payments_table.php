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
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->enum('payment_method', ['bkash', 'nagad', 'rocket']); // Enum for payment method
            $table->string('mobile_number')->nullable(); // Mobile banking number, consider making it nullable if needed
            $table->string('transaction_id')->unique(); // Unique transaction ID, ensure it's unique as per your logic
            $table->decimal('amount', 10, 2); // Amount paid
            $table->string('coupon')->nullable(); // Coupon code field
            $table->boolean('verified')->default(false); // Admin approval status
            $table->dateTime('verified_at')->nullable(); // Nullable approval timestamp
            $table->enum('resource_type', ['pdf', 'package']); // Enum for resource type (pdf or package)
            $table->foreignId('resource_id'); // Foreign key for item (pdf/package)
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
