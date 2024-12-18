<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdditionalPackageCategoriesTable extends Migration
{
    public function up(): void
    {
        Schema::create('additional_package_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps(); // Adds created_at and updated_at
            $table->softDeletes(); // Adds deleted_at for soft deletes
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('additional_package_categories');
    }
}
