<?php

namespace App\Exceptions;

use App\Helpers\ApiResponseHelper;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        // if ($request->expectsJson()) {
            return ApiResponseHelper::error('Unauthenticated.', 401);
        // }

        // return redirect()->guest(route('login'));
    }

    public function render($request, Throwable $e)
    {
        // Handle ModelNotFoundException as JSON response
        if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
            Log::error($e);
            return ApiResponseHelper::error('Resource not found. ' . $e->getMessage(), 404);
        }

        // Handle other exceptions as needed
        Log::error($e);

        // Handle AuthenticationException for JSON responses
        if ($e instanceof AuthenticationException) {
            return ApiResponseHelper::error($e->getMessage(), 401);
        }
        // Ensure that the response is always in JSON format
        if ($request->expectsJson() || $request->is('api/*')) {
            return ApiResponseHelper::error('An unexpected error occurred. ' . $e->getMessage(), 500);
        }

        // For other exceptions, fall back to the default Laravel behavior
        // return parent::render($request, $e);
        // Handle other exceptions
        return ApiResponseHelper::error('An unexpected error occurred. ' . $e->getMessage(), 500);


    }


}
