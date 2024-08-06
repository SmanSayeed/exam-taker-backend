<?php

namespace App\Providers;

use App\Repositories\Admin\AdminRepositoryInterface;
use App\Repositories\Admin\AdminAuthRepository;
use App\Repositories\Admin\StudentCRUDRepository\StudentCRUDRepository;
use App\Repositories\Admin\StudentCRUDRepository\StudentCRUDRepositoryInterface;
use App\Repositories\QuestionRepository\QueBaseRepository;
use App\Repositories\QuestionRepository\QuestionBaseRepository;
use App\Repositories\QuestionRepository\QuestionBaseRepositoryInterface;
use App\Repositories\QuestionRepository\SectionRepository;
use App\Repositories\QuestionRepository\SectionRepositoryInterface;
use App\Services\Question\QuestionBaseService;
use Illuminate\Support\ServiceProvider;
use App\Repositories\QuestionRepository\QuestionRepositoryInterface;
use App\Repositories\QuestionRepository\QuestionRepository;
use App\Repositories\QuestionRepository\McqQuestionRepository;
use App\Repositories\QuestionRepository\NormalTextQuestionRepository;
use App\Repositories\QuestionRepository\CreativeQuestionRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AdminRepositoryInterface::class, AdminAuthRepository::class);
        $this->app->bind(StudentCRUDRepositoryInterface::class, StudentCRUDRepository::class);

        $this->app->bind(QuestionBaseRepositoryInterface::class, QuestionBaseRepository::class);

        // $this->app->bind(QuestionRepositoryInterface::class, QuestionRepository::class);
        // $this->app->bind(QuestionRepositoryInterface::class, McqQuestionRepository::class);
        // $this->app->bind(QuestionRepositoryInterface::class, NormalTextQuestionRepository::class);

        $this->app->bind(QuestionRepositoryInterface::class, QueBaseRepository::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
