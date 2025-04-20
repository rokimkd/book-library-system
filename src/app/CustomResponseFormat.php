<?php

namespace App;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class CustomResponseFormat
{
    public static function badRequestFormat(string|object $exception, int $status = 400, string $error = "Bad Request"): JsonResponse
    {
        return Response::json([
            "timestamp" => date('Y-m-d\TH:i:s.vP'),
            "status" => $check['code'] ?? $status,
            "error" => $error,
            "message" => is_string($exception) ? $exception : $exception->getMessage(),
            "path" => request()->path()
        ], $status);
    }

    public static function successFormat(array|string $message = [], array|object|null $data = [], int $status = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'status' => $status,
            'type' => 'general_success'
        ]);
    }
}
