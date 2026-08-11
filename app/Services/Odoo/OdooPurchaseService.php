<?php

namespace App\Services\Odoo;

use App\Services\OdooService;
use Exception;

/**
 * OdooPurchaseService
 *
 * Service untuk menangani operasi CRUD Purchase Order dari Odoo.
 * Berkomunikasi dengan model Odoo: purchase.order
 */
class OdooPurchaseService extends OdooService
{
    protected string $model = 'purchase.order';

    /**
     * Ambil semua data purchase order dari Odoo.
     */
    public function getPurchases(array $domain = [], array $fields = [], int $limit = 80, int $offset = 0): array
    {
        $defaultFields = [
            'name',
            'partner_id',
            'date_order',
            'amount_untaxed',
            'amount_tax',
            'amount_total',
            'state',
            'user_id',
        ];

        $fieldsToFetch = !empty($fields) ? $fields : $defaultFields;

        return $this->searchRead(
            model: $this->model,
            domain: $domain,
            fields: $fieldsToFetch,
            limit: $limit,
            offset: $offset
        );
    }

    /**
     * Ambil detail satu purchase order berdasarkan ID dari Odoo.
     */
    public function getPurchaseById(int $id, array $fields = []): array
    {
        $defaultFields = [
            'name',
            'partner_id',
            'date_order',
            'amount_untaxed',
            'amount_tax',
            'amount_total',
            'state',
            'user_id',
            'order_line',
        ];

        $fieldsToFetch = !empty($fields) ? $fields : $defaultFields;

        return $this->read($this->model, $id, $fieldsToFetch);
    }

    /**
     * Buat Purchase Order baru di Odoo.
     */
    public function createPurchase(array $data): int
    {
        $uid = $this->authenticate();

        return (int) $this->call('object', 'execute_kw', [
            $this->db,
            $uid,
            $this->apiKey,
            $this->model,
            'create',
            [$data],
        ]);
    }

    /**
     * Update Purchase Order di Odoo.
     */
    public function updatePurchase(int $id, array $data): bool
    {
        $uid = $this->authenticate();

        return (bool) $this->call('object', 'execute_kw', [
            $this->db,
            $uid,
            $this->apiKey,
            $this->model,
            'write',
            [[$id], $data],
        ]);
    }

    /**
     * Hapus Purchase Order di Odoo.
     */
    public function deletePurchase(int $id): bool
    {
        $uid = $this->authenticate();

        return (bool) $this->call('object', 'execute_kw', [
            $this->db,
            $uid,
            $this->apiKey,
            $this->model,
            'unlink',
            [[$id]],
        ]);
    }
}
