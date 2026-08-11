<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Services\Odoo\OdooProductService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Exception;

/**
 * ApiProductController
 *
 * Controller REST API untuk mengelola Produk dari Odoo.
 */
class ApiProductController extends Controller
{
    protected OdooProductService $productService;

    public function __construct(OdooProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * GET /api/products
     * Mengambil daftar produk dari Odoo.
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

            $products = $this->productService->getProducts(
                domain: $domain,
                limit: $limit,
                offset: $offset
            );

            return ApiResponse::success($products, 'Data produk berhasil diambil');
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
            $product = $this->productService->getProductById($id);

            if (empty($product)) {
                return ApiResponse::notFound("Produk dengan ID {$id} tidak ditemukan");
            }

            return ApiResponse::success($product, 'Detail produk berhasil diambil');
        } catch (Exception $e) {
            return ApiResponse::serverError('Gagal mengambil detail produk: ' . $e->getMessage());
        }
    }

    /**
     * POST /api/products
     * Membuat produk baru di Odoo.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name'           => 'required|string|max:255',
                'list_price'     => 'nullable|numeric|min:0',
                'standard_price' => 'nullable|numeric|min:0',
                'default_code'   => 'nullable|string|max:100',
            ]);

            $newId = $this->productService->createProduct($validated);

            return ApiResponse::created(['id' => $newId], 'Produk berhasil dibuat');
        } catch (ValidationException $e) {
            return ApiResponse::validationError($e->errors());
        } catch (Exception $e) {
            return ApiResponse::serverError('Gagal membuat produk: ' . $e->getMessage());
        }
    }

    /**
     * PUT /api/products/{id}
     * Perbarui produk di Odoo.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name'           => 'sometimes|required|string|max:255',
                'list_price'     => 'nullable|numeric|min:0',
                'standard_price' => 'nullable|numeric|min:0',
                'default_code'   => 'nullable|string|max:100',
            ]);

            $updated = $this->productService->updateProduct($id, $validated);

            if (!$updated) {
                return ApiResponse::error('Gagal memperbarui produk');
            }

            return ApiResponse::success(['id' => $id], 'Produk berhasil diperbarui');
        } catch (ValidationException $e) {
            return ApiResponse::validationError($e->errors());
        } catch (Exception $e) {
            return ApiResponse::serverError('Gagal memperbarui produk: ' . $e->getMessage());
        }
    }

    /**
     * DELETE /api/products/{id}
     * Hapus produk di Odoo.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->productService->deleteProduct($id);

            if (!$deleted) {
                return ApiResponse::error('Gagal menghapus produk');
            }

            return ApiResponse::success(['id' => $id], 'Produk berhasil dihapus');
        } catch (Exception $e) {
            return ApiResponse::serverError('Gagal menghapus produk: ' . $e->getMessage());
        }
    }
}
