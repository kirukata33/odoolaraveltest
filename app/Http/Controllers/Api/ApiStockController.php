<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Services\OdooPgService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

/**
 * ApiStockController
 *
 * Controller REST API untuk mengelola Data Pergerakan Stok dari PostgreSQL Odoo.
 */
class ApiStockController extends Controller
{
    protected OdooPgService $odooPg;

    public function __construct(OdooPgService $odooPg)
    {
        $this->odooPg = $odooPg;
    }

    /**
     * GET /api/stocks
     * Mengambil data stok/pergerakan barang dari PostgreSQL Odoo.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $limit  = (int) $request->query('limit', 80);
            $offset = (int) $request->query('offset', 0);

            $stocks = $this->odooPg->getStocks(
                limit: $limit,
                offset: $offset
            );

            return ApiResponse::success($stocks, 'Data pergerakan stok berhasil diambil via Direct PostgreSQL');
        } catch (Exception $e) {
            return ApiResponse::serverError('Gagal mengambil data stok: ' . $e->getMessage());
        }
    }

    /**
     * GET /api/stocks/{id}
     * Mengambil detail satu pergerakan stok berdasarkan ID.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $stock = $this->odooPg->table('stock_picking')
                ->where('id', $id)
                ->first();

            if (!$stock) {
                return ApiResponse::notFound("Data stok dengan ID {$id} tidak ditemukan");
            }

            return ApiResponse::success($stock, 'Detail data stok berhasil diambil via Direct PostgreSQL');
        } catch (Exception $e) {
            return ApiResponse::serverError('Gagal mengambil detail data stok: ' . $e->getMessage());
        }
    }
}
