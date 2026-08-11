<?php

namespace App\Services\Odoo;

use App\Services\OdooService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Exception;

/**
 * OdooAuthService
 *
 * Service untuk menangani autentikasi pengguna API terhadap Odoo 19.
 */
class OdooAuthService extends OdooService
{
    /**
     * Otentikasi pengguna menggunakan kredensial Odoo & hasilkan Bearer Token.
     */
    public function attemptLogin(string $login, string $password): ?array
    {
        try {
            // Verifikasi kredensial ke Odoo
            $uid = $this->call('common', 'authenticate', [
                $this->db,
                $login,
                $password,
                [],
            ]);

            if (!$uid || !is_numeric($uid)) {
                return null;
            }

            // Ambil informasi pengguna dari Odoo
            $userData = $this->read('res.users', (int) $uid, ['name', 'login', 'email', 'partner_id']);

            $user = [
                'id'         => (int) $uid,
                'name'       => $userData['name'] ?? $login,
                'login'      => $userData['login'] ?? $login,
                'email'      => $userData['email'] ?? null,
                'partner_id' => $userData['partner_id'] ?? null,
            ];

            // Hasilkan Token Akses acak yang aman
            $token = Str::random(64);

            // Simpan token di Cache Laravel selama 24 jam (86400 detik)
            Cache::put("api_token_{$token}", $user, 86400);

            return [
                'token'      => $token,
                'token_type' => 'Bearer',
                'expires_in' => 86400,
                'user'       => $user,
            ];
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Dapatkan data user berdasarkan Token Akses.
     */
    public function getUserByToken(string $token): ?array
    {
        return Cache::get("api_token_{$token}");
    }

    /**
     * Revoke / Hapus Token Akses (Logout).
     */
    public function revokeToken(string $token): bool
    {
        return Cache::forget("api_token_{$token}");
    }
}
