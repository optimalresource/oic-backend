<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            return response()->json(['message' => $e->getMessage()]);
        });
    }

    public function report(Throwable $e)
    {
        return response()->json(['message' => $e->getMessage()]);
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof \Illuminate\Auth\AuthenticationException) {
            return response()->json(['message' => $e->getMessage()], 401);
        }

        if ($request->wantsJson()) {
            $response = [
                'errors' => 'Sorry, something went wrong.'
            ];

            if (config('app.debug')) {
                $response['exception'] = get_class($e); // Reflection might be better here
                $response['message'] = $e->getMessage();
                $response['trace'] = $e->getTrace();
            }

            $status = 400;

            return response()->json($response, $status);
        }else {
            return response()->json([
                'type' => get_class($e),
                'message' => $e->getMessage()
            ]);
        }
        return parent::render($request, $e);
    }
}
