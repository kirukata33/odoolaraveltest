<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Services\OdooPgService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

/**
 * ApiCustomerController
 *
 * Controller REST API untuk mengelola Customer langsung dari PostgreSQL Odoo.
 */
class ApiCustomerController extends Controller
{
    protected OdooPgService $odooPg;

    public function __construct(OdooPgService $odooPg)
    {
        $this->odooPg = $odooPg;
    }

    /**
     * GET /api/customers
     * Mengambil daftar customer dari PostgreSQL Odoo.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $limit  = (int) $request->query('limit', 80);
            $offset = (int) $request->query('offset', 0);

            $customers = $this->odooPg->getCustomers(
                limit: $limit,
                offset: $offset
            );

            return ApiResponse::success($customers, 'Data customer berhasil diambil via Direct PostgreSQL');
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
            $customer = $this->odooPg->table('res_partner')
                ->where('id', $id)
                ->first();

            if (!$customer) {
                return ApiResponse::notFound("Customer dengan ID {$id} tidak ditemukan");
            }

            return ApiResponse::success($customer, 'Detail customer berhasil diambil via Direct PostgreSQL');
        } catch (Exception $e) {
            return ApiResponse::serverError('Gagal mengambil detail customer: ' . $e->getMessage());
        }
    }
}
