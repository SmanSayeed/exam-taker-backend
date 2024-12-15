<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageTagSeeder extends Seeder
{
    public function run(): void
    {
        $packageTags = [
            ['package_id' => 1, 'tag_id' => 1],
            ['package_id' => 1, 'tag_id' => 2],
            ['package_id' => 2, 'tag_id' => 1],
            ['package_id' => 2, 'tag_id' => 3],
            ['package_id' => 3, 'tag_id' => 2],
        ];

        DB::table('package_tags')->insert($packageTags);
    }
}
