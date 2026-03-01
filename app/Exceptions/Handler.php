<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Throwable;

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

        // Handle file upload size exceeded (when file is larger than post_max_size)
        $this->renderable(function (PostTooLargeException $e, $request) {
            return back()->withErrors([
                'file' => 'The uploaded file is too large. Maximum file size is 2MB. Please compress your file or choose a smaller one and try again.',
                'medical_certificate' => 'The uploaded file exceeds the maximum size of 2MB.',
                'supporting_document' => 'The uploaded file exceeds the maximum size of 2MB.',
            ])->withInput();
        });
    }
}
