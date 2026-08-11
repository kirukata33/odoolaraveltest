<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Services\Odoo\OdooCustomerService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Exception;

/**
 * ApiCustomerController
 *
 * Controller REST API untuk mengelola Customer / Partner dari Odoo.
 */
class ApiCustomerController extends Controller
{
    protected OdooCustomerService $customerService;

    public function __construct(OdooCustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * GET /api/customers
     * Mengambil daftar customer dari Odoo.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $limit  = (int) $request->query('limit', 80);
            $offset = (int) $request->query('offset', 0);
            $search = $request->query('search');

            $domain = [];
            if ($search) {
                $domain[] = ['name', 'ilike', $search];
            }

            $customers = $this->customerService->getCustomers(
                domain: $domain,
                limit: $limit,
                offset: $offset
            );

            return ApiResponse::success($customers, 'Data customer berhasil diambil');
        } catch (Exception $e) {
            return ApiResponse::serverError('Gagal mengambil data customer: ' . $e->getMessage());
        }
    }

    /**
     * GET /api/customers/{id}
     * Mengambil detail satu customer berdasarkan ID.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $customer = $this->customerService->getCustomerById($id);

            if (empty($customer)) {
                return ApiResponse::notFound("Customer dengan ID {$id} tidak ditemukan");
            }

            return ApiResponse::success($customer, 'Detail customer berhasil diambil');
        } catch (Exception $e) {
            return ApiResponse::serverError('Gagal mengambil detail customer: ' . $e->getMessage());
        }
    }

    /**
     * POST /api/customers
     * Membuat customer baru di Odoo.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name'       => 'required|string|max:255',
                'email'      => 'nullable|email|max:255',
                'phone'      => 'nullable|string|max:50',
                'street'     => 'nullable|string|max:255',
                'city'       => 'nullable|string|max:100',
                'is_company' => 'nullable|boolean',
            ]);

            $newId = $this->customerService->createCustomer($validated);

            return ApiResponse::created(['id' => $newId], 'Customer berhasil dibuat');
        } catch (ValidationException $e) {
            return ApiResponse::validationError($e->errors());
        } catch (Exception $e) {
            return ApiResponse::serverError('Gagal membuat customer: ' . $e->getMessage());
        }
    }

    /**
     * PUT /api/customers/{id}
     * Perbarui data customer di Odoo.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name'       => 'sometimes|required|string|max:255',
                'email'      => 'nullable|email|max:255',
                'phone'      => 'nullable|string|max:50',
                'street'     => 'nullable|string|max:255',
                'city'       => 'nullable|string|max:100',
                'is_company' => 'nullable|boolean',
            ]);

            $updated = $this->customerService->updateCustomer($id, $validated);

            if (!$updated) {
                return ApiResponse::error('Gagal memperbarui customer');
            }

            return ApiResponse::success(['id' => $id], 'Customer berhasil diperbarui');
        } catch (ValidationException $e) {
            return ApiResponse::validationError($e->errors());
        } catch (Exception $e) {
            return ApiResponse::serverError('Gagal memperbarui customer: ' . $e->getMessage());
        }
    }

    /**
     * DELETE /api/customers/{id}
     * Hapus customer di Odoo.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->customerService->deleteCustomer($id);

            if (!$deleted) {
                return ApiResponse::error('Gagal menghapus customer');
            }

            return ApiResponse::success(['id' => $id], 'Customer berhasil dihapus');
        } catch (Exception $e) {
            return ApiResponse::serverError('Gagal menghapus customer: ' . $e->getMessage());
        }
    }
}
