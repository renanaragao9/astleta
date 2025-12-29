<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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

    /**
     * Convert a validation exception into a JSON response.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    protected function invalidJson($request, ValidationException $exception): JsonResponse
    {
        $errors = $exception->errors();
        $firstError = collect($errors)->flatten()->first();

        // Count additional errors
        $totalErrors = collect($errors)->flatten()->count();
        $additionalErrors = $totalErrors - 1;

        $message = $firstError;

        if ($additionalErrors > 0) {
            $moreErrorsText = trans_choice('system.and_more_errors', $additionalErrors, ['count' => $additionalErrors]);
            $message .= ' '.$moreErrorsText;
        }

        return response()->json([
            'message' => $message,
            'errors' => $errors,
        ], $exception->status);
    }
}
