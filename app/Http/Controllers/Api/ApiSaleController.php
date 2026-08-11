<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Services\Odoo\OdooSaleService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Exception;

/**
 * ApiSaleController
 *
 * Controller REST API untuk mengelola Sales Order dari Odoo.
 */
class ApiSaleController extends Controller
{
    protected OdooSaleService $saleService;

    public function __construct(OdooSaleService $saleService)
    {
        $this->saleService = $saleService;
    }

    /**
     * GET /api/sales
     * Mengambil daftar Sales Order dari Odoo.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $limit  = (int) $request->query('limit', 80);
            $offset = (int) $request->query('offset', 0);
            $status = $request->query('status');

            $domain = [];
            if ($status) {
                $domain[] = ['state', '=', $status];
            }

            $sales = $this->saleService->getSales(
                domain: $domain,
                limit: $limit,
                offset: $offset
            );

            return ApiResponse::success($sales, 'Data Sales Order berhasil diambil');
        } catch (Exception $e) {
            return ApiResponse::serverError('Gagal mengambil data Sales Order: ' . $e->getMessage());
        }
    }

    /**
     * GET /api/sales/{id}
     * Mengambil detail satu Sales Order berdasarkan ID.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $sale = $this->saleService->getSaleById($id);

            if (empty($sale)) {
                return ApiResponse::notFound("Sales Order dengan ID {$id} tidak ditemukan");
            }

            return ApiResponse::success($sale, 'Detail Sales Order berhasil diambil');
        } catch (Exception $e) {
            return ApiResponse::serverError('Gagal mengambil detail Sales Order: ' . $e->getMessage());
        }
    }

    /**
     * POST /api/sales
     * Membuat Sales Order baru di Odoo.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'partner_id' => 'required|integer',
            ]);

            $newId = $this->saleService->createSale($validated);

            return ApiResponse::created(['id' => $newId], 'Sales Order berhasil dibuat');
        } catch (ValidationException $e) {
            return ApiResponse::validationError($e->errors());
        } catch (Exception $e) {
            return ApiResponse::serverError('Gagal membuat Sales Order: ' . $e->getMessage());
        }
    }

    /**
     * PUT /api/sales/{id}
     * Perbarui Sales Order di Odoo.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $data = $request->all();
            $updated = $this->saleService->updateSale($id, $data);

            if (!$updated) {
                return ApiResponse::error('Gagal memperbarui Sales Order');
            }

            return ApiResponse::success(['id' => $id], 'Sales Order berhasil diperbarui');
        } catch (Exception $e) {
            return ApiResponse::serverError('Gagal memperbarui Sales Order: ' . $e->getMessage());
        }
    }

    /**
     * DELETE /api/sales/{id}
     * Hapus Sales Order di Odoo.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->saleService->deleteSale($id);

            if (!$deleted) {
                return ApiResponse::error('Gagal menghapus Sales Order');
            }

            return ApiResponse::success(['id' => $id], 'Sales Order berhasil dihapus');
        } catch (Exception $e) {
            return ApiResponse::serverError('Gagal menghapus Sales Order: ' . $e->getMessage());
        }
    }
}
