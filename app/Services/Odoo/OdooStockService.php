<?php

namespace App\Services\Odoo;

use App\Services\OdooService;
use Exception;

/**
 * OdooStockService
 *
 * Service untuk menangani operasi CRUD Inventory / Stock dari Odoo.
 * Berkomunikasi dengan model Odoo: stock.quant
 */
class OdooStockService extends OdooService
{
    protected string $model = 'stock.quant';

    /**
     * Ambil semua data kuantitas stok dari Odoo.
     */
    public function getStocks(array $domain = [], array $fields = [], int $limit = 80, int $offset = 0): array
    {
        $defaultFields = [
            'product_id',
            'location_id',
            'quantity',
            'reserved_quantity',
            'available_quantity',
            'inventory_date',
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
     * Ambil detail satu entri stok berdasarkan ID dari Odoo.
     */
    public function getStockById(int $id, array $fields = []): array
    {
        $defaultFields = [
            'product_id',
            'location_id',
            'quantity',
            'reserved_quantity',
            'available_quantity',
            'inventory_date',
            'product_categ_id',
        ];

        $fieldsToFetch = !empty($fields) ? $fields : $defaultFields;

        return $this->read($this->model, $id, $fieldsToFetch);
    }

    /**
     * Buat/sesuaikan entri stok baru di Odoo.
     */
    public function createStock(array $data): int
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
     * Update entri stok di Odoo.
     */
    public function updateStock(int $id, array $data): bool
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
     * Hapus entri stok di Odoo.
     */
    public function deleteStock(int $id): bool
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
