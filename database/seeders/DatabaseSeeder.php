<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminPermissionsSeeder::class,
            AdminUsersSeeder::class,
            SectionSeeder::class,
            ExamTypeSeeder::class,
            ExamSubTypeSeeder::class,
            YearSeeder::class,
            GroupSeeder::class,
            LevelSeeder::class,
            SubjectSeeder::class,
            LessonSeeder::class,
            TopicSeeder::class,
            SubTopicSeeder::class,
            QuestionSeeder::class,
            PackagesTableSeeder::class,
            ModelTestsTableSeeder::class,
            StudentsTableSeeder::class,
            SubscriptionsTableSeeder::class,
            StudentPaymentsTableSeeder::class,
            TagsTableSeeder::class,
        ]);
    }
}
