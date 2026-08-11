<?php

namespace App\Services\Odoo;

use App\Services\OdooService;
use Exception;

/**
 * OdooProductService
 *
 * Service untuk menangani operasi CRUD Product dari Odoo.
 * Berkomunikasi dengan model Odoo: product.template
 */
class OdooProductService extends OdooService
{
    protected string $model = 'product.template';

    /**
     * Ambil semua data produk dari Odoo.
     */
    public function getProducts(array $domain = [], array $fields = [], int $limit = 80, int $offset = 0): array
    {
        $defaultFields = [
            'name',
            'default_code',
            'list_price',
            'standard_price',
            'qty_available',
            'type',
            'categ_id',
            'active',
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
     * Ambil detail satu produk berdasarkan ID dari Odoo.
     */
    public function getProductById(int $id, array $fields = []): array
    {
        $defaultFields = [
            'name',
            'default_code',
            'list_price',
            'standard_price',
            'qty_available',
            'type',
            'categ_id',
            'active',
            'description',
        ];

        $fieldsToFetch = !empty($fields) ? $fields : $defaultFields;

        return $this->read($this->model, $id, $fieldsToFetch);
    }

    /**
     * Buat produk baru di Odoo.
     */
    public function createProduct(array $data): int
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
     * Update produk di Odoo.
     */
    public function updateProduct(int $id, array $data): bool
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
     * Hapus produk di Odoo.
     */
    public function deleteProduct(int $id): bool
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
