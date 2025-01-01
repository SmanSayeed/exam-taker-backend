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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('password');
            $table->string('profile_image')->nullable();
            $table->string('ip_address')->nullable(); // IP Address field
            $table->string('country')->nullable(); // Country field
            $table->string('country_code')->nullable(); // Country Code field (2 characters)
            $table->string('address')->nullable(); // Address field
            $table->boolean('active_status')->default(false); // Active Status field
            $table->unsignedInteger('paid_exam_quota')->default(0)->nullable(); // Quota for paid exams
            $table->unsignedInteger('exams_count')->default(0)->nullable(); // Number of exams attended
            $table->unsignedBigInteger('section_id')->nullable();
            $table->timestamps();
        });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};

