<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubjectsTable extends Migration
{
    public function up()
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('level_id')->constrained('levels')->onDelete('cascade');
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
            $table->enum('part', ['1st', '2nd']);
            $table->string('title')->unique();
            $table->text('details')->nullable();
            $table->string('image')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('subjects');
    }
}
