<?php

use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

if (!function_exists('handleException')) {
  /**
   * Return the given value, optionally passed through the given callback.
   *
   * @template TValue
   * @template TReturn
   *
   * @param Throwable $exception
   * @return object
   */
  function handleException(\Throwable $exception)
  {
    $statusCode = 500;
    $data = [];
    $message = 'Server Error';


    if ($exception instanceof ValidationException) {
      $statusCode = 422;
      $message = $exception->getMessage();
      $data = $exception->errors();
    } elseif ($exception instanceof AuthenticationException) {
      $statusCode = 401;
      $message = 'Unauthorized';
    } elseif ($exception instanceof ModelNotFoundException) {
      $statusCode = 404;
      $message = 'Resource not found';
    } elseif ($exception instanceof QueryException) {
      dd($exception);
    }

    return response()->json([
      'error' => [
        'message' => $message,
        'status_code' => $statusCode,
        'data' => $data
      ],
    ], $statusCode);
  }
}
