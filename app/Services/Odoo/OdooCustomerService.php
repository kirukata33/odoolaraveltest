<?php

namespace App\Services\Odoo;

use App\Services\OdooService;
use Exception;

/**
 * OdooCustomerService
 *
 * Service untuk menangani operasi CRUD Customer dari Odoo.
 * Berkomunikasi dengan model Odoo: res.partner
 */
class OdooCustomerService extends OdooService
{
    protected string $model = 'res.partner';

    /**
     * Ambil semua data customer dari Odoo.
     */
    public function getCustomers(array $domain = [], array $fields = [], int $limit = 80, int $offset = 0): array
    {
        $defaultFields = [
            'name',
            'email',
            'phone',
            'street',
            'city',
            'is_company',
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
     * Ambil detail satu customer berdasarkan ID dari Odoo.
     */
    public function getCustomerById(int $id, array $fields = []): array
    {
        $defaultFields = [
            'name',
            'email',
            'phone',
            'street',
            'street2',
            'city',
            'zip',
            'is_company',
            'active',
            'comment',
        ];

        $fieldsToFetch = !empty($fields) ? $fields : $defaultFields;

        return $this->read($this->model, $id, $fieldsToFetch);
    }

    /**
     * Buat customer baru di Odoo.
     */
    public function createCustomer(array $data): int
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
     * Update customer di Odoo.
     */
    public function updateCustomer(int $id, array $data): bool
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
     * Hapus customer di Odoo.
     */
    public function deleteCustomer(int $id): bool
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
