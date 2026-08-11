<?php

namespace App\Services\Odoo;

use App\Services\OdooService;
use Exception;

/**
 * OdooSaleService
 *
 * Service untuk menangani operasi CRUD Sales Order dari Odoo.
 * Berkomunikasi dengan model Odoo: sale.order
 */
class OdooSaleService extends OdooService
{
    protected string $model = 'sale.order';

    /**
     * Ambil semua data sales order dari Odoo.
     */
    public function getSales(array $domain = [], array $fields = [], int $limit = 80, int $offset = 0): array
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
     * Ambil detail satu sales order berdasarkan ID dari Odoo.
     */
    public function getSaleById(int $id, array $fields = []): array
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
     * Buat Sales Order baru di Odoo.
     */
    public function createSale(array $data): int
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
     * Update Sales Order di Odoo.
     */
    public function updateSale(int $id, array $data): bool
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
     * Hapus Sales Order di Odoo.
     */
    public function deleteSale(int $id): bool
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
