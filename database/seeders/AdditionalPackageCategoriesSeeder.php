<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdditionalPackageCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        // You can add multiple categories to seed the table
        DB::table('additional_package_categories')->insert([
            [
                'name' => 'Category 1',
                'description' => 'Description for Category 1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Category 2',
                'description' => 'Description for Category 2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Category 3',
                'description' => 'Description for Category 3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
