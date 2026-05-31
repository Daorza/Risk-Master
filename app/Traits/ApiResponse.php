<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function success(mixed $data, $message = 'OK', $statusCode = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    protected function created(mixed $data, string $message = 'Data berhasil dibuat.'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

    protected function error(string $message, int $code = 400, ?array $errors = null): JsonResponse
    {
        $body = [
            'status'  => 'error',
            'message' => $message,
        ];

        if ($errors !== null) {
            $body['errors'] = $errors;
        }

        return response()->json($body, $code);
    }

    /** 401 Unauthenticated */
    protected function unauthorized(string $message = 'Anda belum login.'): JsonResponse
    {
        return $this->error($message, 401);
    }

    /** 403 Forbidden */
    protected function forbidden(string $message = 'Anda tidak memiliki akses.'): JsonResponse
    {
        return $this->error($message, 403);
    }

    /** 404 Not Found */
    protected function notFound(string $message = 'Data tidak ditemukan.'): JsonResponse
    {
        return $this->error($message, 404);
    }

    /** 422 Unprocessable — validasi gagal dengan detail per field */
    protected function validationFailed(array $errors, string $message = 'Data tidak valid.'): JsonResponse
    {
        return $this->error($message, 422, $errors);
    }
}
