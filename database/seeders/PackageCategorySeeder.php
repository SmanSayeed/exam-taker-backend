<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('package_categories')->insert([
            [
                'package_id' => 1,
                'section_id' => 2,
                'exam_type_id' => 3,
                'exam_sub_type_id' => 4,
                'additional_package_category_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'package_id' => 2,
                'section_id' => 1,
                'exam_type_id' => 5,
                'exam_sub_type_id' => 6,
                'additional_package_category_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Add more records as needed
        ]);
    }
}
