<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Services\OdooPgService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

/**
 * ApiPurchaseController
 *
 * Controller REST API untuk membaca Purchase Order langsung dari PostgreSQL Odoo 19.
 */
class ApiPurchaseController extends Controller
{
    protected OdooPgService $odooPg;

    public function __construct(OdooPgService $odooPg)
    {
        $this->odooPg = $odooPg;
    }

    /**
     * GET /api/purchases
     * Mengambil daftar Purchase Order dari PostgreSQL Odoo.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $limit  = (int) $request->query('limit', 80);
            $offset = (int) $request->query('offset', 0);
            $status = $request->query('status');

            $purchases = $this->odooPg->getPurchaseOrders(
                state: $status,
                limit: $limit,
                offset: $offset
            );

            return ApiResponse::success($purchases, 'Data Purchase Order berhasil diambil via Direct PostgreSQL');
        } catch (Exception $e) {
            return ApiResponse::serverError('Gagal mengambil data Purchase Order: ' . $e->getMessage());
        }
    }

    /**
     * GET /api/purchases/{id}
     * Mengambil detail satu Purchase Order berdasarkan ID dari PostgreSQL.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $purchase = $this->odooPg->getPurchaseOrderById($id);

            if (empty($purchase)) {
                return ApiResponse::notFound("Purchase Order dengan ID {$id} tidak ditemukan");
            }

            return ApiResponse::success($purchase, 'Detail Purchase Order berhasil diambil via Direct PostgreSQL');
        } catch (Exception $e) {
            return ApiResponse::serverError('Gagal mengambil detail Purchase Order: ' . $e->getMessage());
        }
    }
}
