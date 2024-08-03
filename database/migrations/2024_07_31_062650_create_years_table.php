<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYearsTable extends Migration
{
    public function up()
    {
        Schema::create('years', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            $table->string('title')->unique();
            $table->text('details')->nullable();
            $table->string('image')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('years');
    }
}
