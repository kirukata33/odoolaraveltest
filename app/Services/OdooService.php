<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

/**
 * Service untuk komunikasi dengan Odoo lewat JSON-RPC.
 *
 * Odoo menyediakan endpoint /jsonrpc yang menerima format:
 * {
 *   "jsonrpc": "2.0",
 *   "method": "call",
 *   "params": { "service": ..., "method": ..., "args": [...] }
 * }
 *
 * Dua service utama yang dipakai:
 * - "common" -> untuk authenticate (login)
 * - "object" -> untuk execute_kw (CRUD data: search_read, create, write, unlink)
 */
class OdooService
{
    protected string $url;
    protected string $db;
    protected string $username;
    protected string $apiKey;
    protected int $timeout;
    protected ?int $uid = null;

    public function __construct()
    {
        $this->url      = rtrim(config('odoo.url'), '/');
        $this->db       = config('odoo.db');
        $this->username = config('odoo.username');
        $this->apiKey   = config('odoo.api_key');
        $this->timeout  = config('odoo.timeout');
    }

    /**
     * Kirim request JSON-RPC generik ke Odoo.
     */
    protected function call(string $service, string $method, array $args): mixed
    {
        $payload = [
            'jsonrpc' => '2.0',
            'method'  => 'call',
            'params'  => [
                'service' => $service,
                'method'  => $method,
                'args'    => $args,
            ],
            'id' => (int) (microtime(true) * 1000),
        ];

        $response = Http::timeout($this->timeout)
            ->acceptJson()
            ->post("{$this->url}/jsonrpc", $payload);

        if ($response->failed()) {
            throw new Exception('Gagal menghubungi server Odoo: HTTP ' . $response->status());
        }

        $data = $response->json();

        if (isset($data['error'])) {
            $message = $data['error']['data']['message']
                ?? $data['error']['message']
                ?? 'Unknown Odoo error';
            throw new Exception('Odoo error: ' . $message);
        }

        return $data['result'] ?? null;
    }

    /**
     * Login ke Odoo dan simpan uid (user id) untuk dipakai di request selanjutnya.
     * Password di sini adalah API Key, bukan password akun biasa.
     */
    public function authenticate(): int
    {
        if ($this->uid) {
            return $this->uid;
        }

        $uid = $this->call('common', 'authenticate', [
            $this->db,
            $this->username,
            $this->apiKey,
            [], // context tambahan, kosongkan saja
        ]);

        if (!$uid) {
            throw new Exception('Autentikasi Odoo gagal. Cek ODOO_DB, ODOO_USERNAME, dan ODOO_API_KEY di .env');
        }

        $this->uid = $uid;

        return $uid;
    }

    /**
     * Ambil data (search_read) dari model Odoo apapun.
     *
     * @param string $model  Nama model, misal 'purchase.order'
     * @param array  $domain Filter ala Odoo, misal [['state', '=', 'purchase']]
     * @param array  $fields Field yang mau diambil, misal ['name', 'partner_id', 'amount_total', 'state']
     * @param int    $limit  Batas jumlah data
     * @param int    $offset Untuk pagination
     */
    public function searchRead(string $model, array $domain = [], array $fields = [], int $limit = 80, int $offset = 0): array
    {
        $uid = $this->authenticate();

        return $this->call('object', 'execute_kw', [
            $this->db,
            $uid,
            $this->apiKey,
            $model,
            'search_read',
            [$domain],
            [
                'fields' => $fields,
                'limit'  => $limit,
                'offset' => $offset,
            ],
        ]);
    }

    /**
     * Hitung jumlah record yang match domain tertentu (berguna untuk pagination).
     */
    public function searchCount(string $model, array $domain = []): int
    {
        $uid = $this->authenticate();

        return $this->call('object', 'execute_kw', [
            $this->db,
            $uid,
            $this->apiKey,
            $model,
            'search_count',
            [$domain],
        ]);
    }

    /**
     * Ambil satu record spesifik berdasarkan ID.
     */
    public function read(string $model, int $id, array $fields = []): array
    {
        $result = $this->searchRead($model, [['id', '=', $id]], $fields, 1);

        return $result[0] ?? [];
    }
}
