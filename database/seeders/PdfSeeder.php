<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PdfSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pdfs')->insert([
            [
                'title' => 'Laravel Documentation',
                'file_path' => 'pdfs/laravel_documentation.pdf',
                'file_link' => 'https://laravel.com/docs',
                'mime_type' => 'application/pdf',
                'description' => 'Official Laravel documentation in PDF format.',
                'pdfable_id' => null,
                'pdfable_type' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'PHP Best Practices',
                'file_path' => 'pdfs/php_best_practices.pdf',
                'file_link' => null,
                'mime_type' => 'application/pdf',
                'description' => 'A collection of PHP coding best practices.',
                'pdfable_id' => null,
                'pdfable_type' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'API Development Guide',
                'file_path' => null,
                'file_link' => 'https://example.com/api-guide.pdf',
                'mime_type' => 'application/pdf',
                'description' => 'Comprehensive API development guide.',
                'pdfable_id' => null,
                'pdfable_type' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
