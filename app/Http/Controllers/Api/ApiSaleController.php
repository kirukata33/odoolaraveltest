<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Services\OdooPgService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

/**
 * ApiSaleController
 *
 * Controller REST API untuk mengelola Sales Order dari PostgreSQL Odoo.
 */
class ApiSaleController extends Controller
{
    protected OdooPgService $odooPg;

    public function __construct(OdooPgService $odooPg)
    {
        $this->odooPg = $odooPg;
    }

    /**
     * GET /api/sales
     * Mengambil daftar Sales Order dari PostgreSQL Odoo.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $limit  = (int) $request->query('limit', 80);
            $offset = (int) $request->query('offset', 0);
            $status = $request->query('status');

            $sales = $this->odooPg->getSalesOrders(
                state: $status,
                limit: $limit,
                offset: $offset
            );

            return ApiResponse::success($sales, 'Data Sales Order berhasil diambil via Direct PostgreSQL');
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
            $sale = $this->odooPg->table('sale_order')
                ->where('id', $id)
                ->first();

            if (!$sale) {
                return ApiResponse::notFound("Sales Order dengan ID {$id} tidak ditemukan");
            }

            return ApiResponse::success($sale, 'Detail Sales Order berhasil diambil via Direct PostgreSQL');
        } catch (Exception $e) {
            return ApiResponse::serverError('Gagal mengambil detail Sales Order: ' . $e->getMessage());
        }
    }
}
