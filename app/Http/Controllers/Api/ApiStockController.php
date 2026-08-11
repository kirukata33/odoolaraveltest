<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Services\Odoo\OdooStockService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Exception;

/**
 * ApiStockController
 *
 * Controller REST API untuk mengelola Inventory / Stock dari Odoo.
 */
class ApiStockController extends Controller
{
    protected OdooStockService $stockService;

    public function __construct(OdooStockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * GET /api/stocks
     * Mengambil daftar kuantitas stok dari Odoo.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $limit  = (int) $request->query('limit', 80);
            $offset = (int) $request->query('offset', 0);
            $productId = $request->query('product_id');

            $domain = [];
            if ($productId) {
                $domain[] = ['product_id', '=', (int) $productId];
            }

            $stocks = $this->stockService->getStocks(
                domain: $domain,
                limit: $limit,
                offset: $offset
            );

            return ApiResponse::success($stocks, 'Data stok inventory berhasil diambil');
        } catch (Exception $e) {
            return ApiResponse::serverError('Gagal mengambil data stok: ' . $e->getMessage());
        }
    }

    /**
     * GET /api/stocks/{id}
     * Mengambil detail satu entri stok berdasarkan ID.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $stock = $this->stockService->getStockById($id);

            if (empty($stock)) {
                return ApiResponse::notFound("Entri stok dengan ID {$id} tidak ditemukan");
            }

            return ApiResponse::success($stock, 'Detail entri stok berhasil diambil');
        } catch (Exception $e) {
            return ApiResponse::serverError('Gagal mengambil detail stok: ' . $e->getMessage());
        }
    }

    /**
     * POST /api/stocks
     * Membuat entri stok baru di Odoo.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'product_id'  => 'required|integer',
                'location_id' => 'required|integer',
                'quantity'    => 'required|numeric',
            ]);

            $newId = $this->stockService->createStock($validated);

            return ApiResponse::created(['id' => $newId], 'Entri stok berhasil dibuat');
        } catch (ValidationException $e) {
            return ApiResponse::validationError($e->errors());
        } catch (Exception $e) {
            return ApiResponse::serverError('Gagal membuat entri stok: ' . $e->getMessage());
        }
    }

    /**
     * PUT /api/stocks/{id}
     * Perbarui entri stok di Odoo.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'quantity' => 'sometimes|required|numeric',
            ]);

            $updated = $this->stockService->updateStock($id, $validated);

            if (!$updated) {
                return ApiResponse::error('Gagal memperbarui entri stok');
            }

            return ApiResponse::success(['id' => $id], 'Entri stok berhasil diperbarui');
        } catch (ValidationException $e) {
            return ApiResponse::validationError($e->errors());
        } catch (Exception $e) {
            return ApiResponse::serverError('Gagal memperbarui entri stok: ' . $e->getMessage());
        }
    }

    /**
     * DELETE /api/stocks/{id}
     * Hapus entri stok di Odoo.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->stockService->deleteStock($id);

            if (!$deleted) {
                return ApiResponse::error('Gagal menghapus entri stok');
            }

            return ApiResponse::success(['id' => $id], 'Entri stok berhasil dihapus');
        } catch (Exception $e) {
            return ApiResponse::serverError('Gagal menghapus entri stok: ' . $e->getMessage());
        }
    }
}
