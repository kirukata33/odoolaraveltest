<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Services\Odoo\OdooPurchaseService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Exception;

/**
 * ApiPurchaseController
 *
 * Controller REST API untuk mengelola Purchase Order dari Odoo.
 */
class ApiPurchaseController extends Controller
{
    protected OdooPurchaseService $purchaseService;

    public function __construct(OdooPurchaseService $purchaseService)
    {
        $this->purchaseService = $purchaseService;
    }

    /**
     * GET /api/purchases
     * Mengambil daftar Purchase Order dari Odoo.
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

            $purchases = $this->purchaseService->getPurchases(
                domain: $domain,
                limit: $limit,
                offset: $offset
            );

            return ApiResponse::success($purchases, 'Data Purchase Order berhasil diambil');
        } catch (Exception $e) {
            return ApiResponse::serverError('Gagal mengambil data Purchase Order: ' . $e->getMessage());
        }
    }

    /**
     * GET /api/purchases/{id}
     * Mengambil detail satu Purchase Order berdasarkan ID.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $purchase = $this->purchaseService->getPurchaseById($id);

            if (empty($purchase)) {
                return ApiResponse::notFound("Purchase Order dengan ID {$id} tidak ditemukan");
            }

            return ApiResponse::success($purchase, 'Detail Purchase Order berhasil diambil');
        } catch (Exception $e) {
            return ApiResponse::serverError('Gagal mengambil detail Purchase Order: ' . $e->getMessage());
        }
    }

    /**
     * POST /api/purchases
     * Membuat Purchase Order baru di Odoo.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'partner_id' => 'required|integer',
            ]);

            $newId = $this->purchaseService->createPurchase($validated);

            return ApiResponse::created(['id' => $newId], 'Purchase Order berhasil dibuat');
        } catch (ValidationException $e) {
            return ApiResponse::validationError($e->errors());
        } catch (Exception $e) {
            return ApiResponse::serverError('Gagal membuat Purchase Order: ' . $e->getMessage());
        }
    }

    /**
     * PUT /api/purchases/{id}
     * Perbarui Purchase Order di Odoo.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $data = $request->all();
            $updated = $this->purchaseService->updatePurchase($id, $data);

            if (!$updated) {
                return ApiResponse::error('Gagal memperbarui Purchase Order');
            }

            return ApiResponse::success(['id' => $id], 'Purchase Order berhasil diperbarui');
        } catch (Exception $e) {
            return ApiResponse::serverError('Gagal memperbarui Purchase Order: ' . $e->getMessage());
        }
    }

    /**
     * DELETE /api/purchases/{id}
     * Hapus Purchase Order di Odoo.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->purchaseService->deletePurchase($id);

            if (!$deleted) {
                return ApiResponse::error('Gagal menghapus Purchase Order');
            }

            return ApiResponse::success(['id' => $id], 'Purchase Order berhasil dihapus');
        } catch (Exception $e) {
            return ApiResponse::serverError('Gagal menghapus Purchase Order: ' . $e->getMessage());
        }
    }
}
