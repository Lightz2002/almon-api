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
    } elseif ($exception instanceof QueryException || $exception instanceof InvalidArgumentException) {
      dd($exception);
    } else {
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



if (!function_exists('getValidationMessage')) {
  /**
   * Return the custom validation message
   *
   * @template TValue
   * @template TReturn
   *
   * @return object
   */
  function getValidationMessage()
  {
    $requiredMessage = ' wajib diisi';
    $numericFormat = ' harus merupakan angka';
    return [
      'password.required' => 'Password' . $requiredMessage,
      'email.required' => 'Email' . $requiredMessage,
      'email.email' => 'Email harus menggunakan format yang sesuai',
      'username.required' => 'Username' . $requiredMessage,
      'security_question_answer.required' => 'Jawaban keamanan' . $requiredMessage,
      'security_question_id.required' => 'Pertanyaan keamanan wajib dipilih',
      'security_question_id.uuid' => 'Nilai pertanyaan keamanan yang dipilih tidak sesuai',
      'device_name.required' => 'Model perangkat tidak diketahui',
      'date.required' => 'Tanggal' . $requiredMessage,
      'date.date_format:Y-m-d' => 'Tanggal harus menggunakan format sesuai contohnya 2023-01-31',
      'amount.required' => 'Jumlah' . $requiredMessage,
      'amount.numeric' => 'Jumlah' . $numericFormat,
      'expense_category_id.required' => 'Kategori pengeluaran' . $requiredMessage,
      'monthly_salary.required' => 'Gaji' . $requiredMessage,
      'monthly_salary.numeric' => 'Gaji' . $numericFormat,
      'token.size' => 'Token harus berupa 6 karakter',
    ];
  }
}
