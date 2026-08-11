<?php

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use App\Services\Odoo\OdooAuthService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ApiTokenMiddleware
 *
 * Middleware untuk memproteksi endpoint REST API.
 * Memvalidasi Bearer Token pada header Authorization request.
 */
class ApiTokenMiddleware
{
    protected OdooAuthService $authService;

    public function __construct(OdooAuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return ApiResponse::unauthorized('Token otentikasi (Bearer Token) tidak ditemukan pada header Request');
        }

        $user = $this->authService->getUserByToken($token);

        if (!$user) {
            return ApiResponse::unauthorized('Token otentikasi tidak valid atau sudah kedaluwarsa');
        }

        // Simpan data user ke dalam atribut request
        $request->attributes->set('authenticated_user', $user);

        return $next($request);
    }
}
