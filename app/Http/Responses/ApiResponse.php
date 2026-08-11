<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

/**
 * ApiResponse
 *
 * Helper class untuk format respons JSON yang konsisten di seluruh API.
 *
 * Format sukses:
 * {
 *   "success": true,
 *   "message": "...",
 *   "data": {}
 * }
 *
 * Format error:
 * {
 *   "success": false,
 *   "message": "...",
 *   "errors": {}
 * }
 */
class ApiResponse
{
    /**
     * Kirim respons sukses.
     *
     * @param mixed  $data    Data yang dikembalikan ke client
     * @param string $message Pesan sukses
     * @param int    $status  HTTP status code (default 200)
     */
    public static function success(
        mixed $data = null,
        string $message = 'Success',
        int $status = 200
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    /**
     * Kirim respons sukses dengan pagination.
     *
     * @param mixed  $data    Data yang dikembalikan
     * @param int    $total   Total semua record
     * @param int    $page    Halaman saat ini
     * @param int    $limit   Jumlah per halaman
     * @param string $message Pesan sukses
     */
    public static function paginate(
        mixed $data,
        int $total,
        int $page = 1,
        int $limit = 20,
        string $message = 'Success'
    ): JsonResponse {
        return response()->json([
            'success'    => true,
            'message'    => $message,
            'data'       => $data,
            'pagination' => [
                'total'        => $total,
                'per_page'     => $limit,
                'current_page' => $page,
                'total_pages'  => (int) ceil($total / $limit),
            ],
        ], 200);
    }

    /**
     * Kirim respons error umum.
     *
     * @param string $message Pesan error
     * @param mixed  $errors  Detail error (opsional)
     * @param int    $status  HTTP status code (default 400)
     */
    public static function error(
        string $message = 'Terjadi kesalahan',
        mixed $errors = null,
        int $status = 400
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $status);
    }

    /**
     * 401 Unauthorized — token tidak ada atau tidak valid.
     *
     * @param string $message Pesan error
     */
    public static function unauthorized(
        string $message = 'Unauthorized. Token tidak valid atau sudah expired.'
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => null,
        ], 401);
    }

    /**
     * 403 Forbidden — tidak punya akses ke resource ini.
     *
     * @param string $message Pesan error
     */
    public static function forbidden(
        string $message = 'Forbidden. Anda tidak memiliki akses.'
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => null,
        ], 403);
    }

    /**
     * 404 Not Found — data tidak ditemukan.
     *
     * @param string $message Pesan error
     */
    public static function notFound(
        string $message = 'Data tidak ditemukan.'
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => null,
        ], 404);
    }

    /**
     * 422 Unprocessable Entity — validasi gagal.
     *
     * @param mixed  $errors  Detail error validasi
     * @param string $message Pesan error
     */
    public static function validationError(
        mixed $errors,
        string $message = 'Validasi gagal. Periksa kembali input Anda.'
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], 422);
    }

    /**
     * 500 Internal Server Error — kesalahan di sisi server.
     *
     * @param string $message Pesan error (jangan tampilkan stack trace ke client)
     */
    public static function serverError(
        string $message = 'Terjadi kesalahan pada server. Silakan coba lagi.'
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => null,
        ], 500);
    }

    /**
     * 201 Created — resource berhasil dibuat.
     *
     * @param mixed  $data    Data yang baru dibuat
     * @param string $message Pesan sukses
     */
    public static function created(
        mixed $data = null,
        string $message = 'Data berhasil dibuat.'
    ): JsonResponse {
        return self::success($data, $message, 201);
    }
}
