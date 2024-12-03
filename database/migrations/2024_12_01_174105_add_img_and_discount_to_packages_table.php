<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImgAndDiscountToPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->string('img')->nullable()->after('description'); // Add image column
            $table->decimal('discount', 8, 2)->nullable()->after('img'); // Add discount column
            $table->enum('discount_type', ['percentage', 'amount'])->nullable()->after('discount'); // Add discount type column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['img', 'discount', 'discount_type']);
        });
    }
}
