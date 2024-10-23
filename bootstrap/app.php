<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
  ->withRouting(
    web: __DIR__ . '/../routes/web.php',
    api: __DIR__ . '/../routes/api.php',
    commands: __DIR__ . '/../routes/console.php',
    health: '/up',
  )
  ->withMiddleware(function (Middleware $middleware) {
    //
  })
  ->withExceptions(function (Exceptions $exceptions) {
    $exceptions->render(function (NotFoundHttpException $e, Request $request) {
      if ($request->is('api/*')) {
        return response()->json([
          'message' => 'Resource not found.'
        ], 404);
      }
    });

    $exceptions->render(function (ValidationException $e, Request $request) {
      if ($request->is('api/*')) {
        return response()->json([
          'message' => 'Validation failed.',
          'errors' => $e->errors(),
        ], 422);
      }
    });

    $exceptions->render(function (AuthenticationException $e, Request $request) {
      if ($request->is('api/*')) {
        return response()->json([
          'message' => 'Unauthenticated.'
        ], 403);
      }
    });

    $exceptions->render(function (HttpException $e, Request $request) {
      if ($request->is('api/*')) {
        return response()->json([
          'message' => $e->getMessage(),
        ], $e->getStatusCode());
      }
    });

    $exceptions->render(function (ModelNotFoundException $e, Request $request) {
      if ($request->is('api/*')) {
        return response()->json([
          'message' => 'Resource not found.'
        ], 404);
      }
    });

    $exceptions->render(function (AuthorizationException $e, Request $request) {
      if ($request->is('api/*')) {
        return response()->json([
          'message' => 'Unauthorized.'
        ], 401);
      }
    });

    $exceptions->render(function (FileNotFoundException $e, Request $request) {
      if ($request->is('api/*')) {
        return response()->json([
          'message' => 'File not found.'
        ], 404);
      }
    });

    $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
      if ($request->is('api/*')) {
        return response()->json([
          'message' => 'Method not allowed.'
        ], 405);
      }
    });

    $exceptions->render(function (QueryException $e, Request $request) {
      if ($request->is('api/*')) {
        if (config('app.debug')) {
          return response()->json([
            'message' => 'Database error.',
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTrace(),
          ], 500);
        }

        return response()->json([
          'message' => 'Database error.'
        ], 500);
      }
    });

    $exceptions->render(function (Throwable $e, Request $request) {
      if ($request->is('api/*')) {
        if (config('app.debug')) {
          return response()->json([
            'message' => 'Server Error.',
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTrace(),
          ], 500);
        }

        return response()->json([
          'message' => 'Server Error.'
        ], 500);
      }
    });
  })->create();