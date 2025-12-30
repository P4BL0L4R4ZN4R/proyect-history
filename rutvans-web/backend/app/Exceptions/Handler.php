<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    // ...

    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson()) {
            $status = 500;
            if ($exception instanceof HttpExceptionInterface) {
                $status = $exception->getStatusCode();
            }
            return response()->json([
                'message' => $exception->getMessage(),
            ], $status);
        }

        return parent::render($request, $exception);
    }
}
