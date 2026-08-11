<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Services\Odoo\OdooAuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Exception;

/**
 * ApiAuthController
 *
 * Menangani endpoint autentikasi REST API:
 * - POST /api/login
 * - POST /api/logout
 * - GET  /api/me
 */
class ApiAuthController extends Controller
{
    protected OdooAuthService $authService;

    public function __construct(OdooAuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * POST /api/login
     * Login dengan kredensial Odoo dan dapatkan Bearer Token.
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'login'    => 'required|string',
                'password' => 'required|string',
            ]);

            $authResult = $this->authService->attemptLogin(
                $validated['login'],
                $validated['password']
            );

            if (!$authResult) {
                return ApiResponse::unauthorized('Kredensial login tidak valid atau pengguna tidak ditemukan di Odoo');
            }

            return ApiResponse::success($authResult, 'Login berhasil');
        } catch (ValidationException $e) {
            return ApiResponse::validationError($e->errors());
        } catch (Exception $e) {
            return ApiResponse::serverError('Terjadi kesalahan saat login: ' . $e->getMessage());
        }
    }

    /**
     * GET /api/me
     * Ambil informasi user yang sedang aktif berdasarkan Token.
     */
    public function me(Request $request): JsonResponse
    {
        $token = $request->bearerToken();

        if (!$token) {
            return ApiResponse::unauthorized('Token otentikasi tidak ditemukan');
        }

        $user = $this->authService->getUserByToken($token);

        if (!$user) {
            return ApiResponse::unauthorized('Token tidak valid atau sudah kedaluwarsa');
        }

        return ApiResponse::success($user, 'Profil pengguna berhasil diambil');
    }

    /**
     * POST /api/logout
     * Batalkan Token Akses (Logout).
     */
    public function logout(Request $request): JsonResponse
    {
        $token = $request->bearerToken();

        if ($token) {
            $this->authService->revokeToken($token);
        }

        return ApiResponse::success(null, 'Berhasil logout');
    }

    /**
     * POST /api/refresh
     */
    public function refresh(Request $request): JsonResponse
    {
        return ApiResponse::success(null, 'Refresh token tidak diperlukan (token berlaku 24 jam)');
    }
}
