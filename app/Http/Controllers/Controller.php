<?php

namespace App\Http\Controllers;

use App\Supports\HandleJsonResponses;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

abstract class Controller
{
    use AuthorizesRequests, ValidatesRequests, HandleJsonResponses;

    /**
     * @param array $data
     * @return JsonResponse
     */
    protected function response(array $data): JsonResponse
    {
        return response()->json($data);
    }

    /**
     * Make API call with exception handling.
     * This allows to gracefully catch all possible exceptions and handle them properly.
     *
     * @param $callback
     *
     * @return mixed
     */
    protected function withErrorHandling($callback): mixed
    {
        try {
            return $callback();
        } catch (Throwable $e) {
            Log::debug($e);
            return $this->message(__('An unexpected error occurred. Please try again later.'))
                ->respondBadRequest();
        }
    }}
