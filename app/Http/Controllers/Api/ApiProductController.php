<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Services\OdooPgService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

/**
 * ApiProductController
 *
 * Controller REST API untuk mengelola Produk langsung dari PostgreSQL Odoo.
 */
class ApiProductController extends Controller
{
    protected OdooPgService $odooPg;

    public function __construct(OdooPgService $odooPg)
    {
        $this->odooPg = $odooPg;
    }

    /**
     * GET /api/products
     * Mengambil daftar produk dari PostgreSQL Odoo.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $limit  = (int) $request->query('limit', 80);
            $offset = (int) $request->query('offset', 0);

            $products = $this->odooPg->getProducts(
                limit: $limit,
                offset: $offset
            );

            return ApiResponse::success($products, 'Data produk berhasil diambil via Direct PostgreSQL');
        } catch (Exception $e) {
            return ApiResponse::serverError('Gagal mengambil data produk: ' . $e->getMessage());
        }
    }

    /**
     * GET /api/products/{id}
     * Mengambil detail satu produk berdasarkan ID.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $product = $this->odooPg->table('product_template')
                ->where('id', $id)
                ->first();

            if (!$product) {
                return ApiResponse::notFound("Produk dengan ID {$id} tidak ditemukan");
            }

            return ApiResponse::success($product, 'Detail produk berhasil diambil via Direct PostgreSQL');
        } catch (Exception $e) {
            return ApiResponse::serverError('Gagal mengambil detail produk: ' . $e->getMessage());
        }
    }
}
