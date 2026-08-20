<?php

namespace App\Http\Controllers;

use App\Services\OdooPgService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class PurchaseOrderController extends Controller
{
    protected OdooPgService $odooPg;

    public function __construct(OdooPgService $odooPg)
    {
        $this->odooPg = $odooPg;
    }

    /**
     * Halaman laporan: daftar Purchase Order langsung dari PostgreSQL Odoo 19.
     */
    public function index(Request $request)
    {
        $error = null;
        $orders = [];
        $user = Auth::user();

        // Filter status lewat query string, misal: ?status=purchase
        $status = $request->query('status');

        try {
            $orders = $this->odooPg->getPurchaseOrders(
                state: $status,
                limit: 100
            );
        } catch (Exception $e) {
            $error = 'Gagal mengambil data dari PostgreSQL Odoo: ' . $e->getMessage();
        }

        return view('purchase-orders.index', [
            'orders' => $orders,
            'error'  => $error,
            'status' => $status,
            'user'   => $user,
        ]);
    }

    /**
     * Endpoint API JSON untuk Purchase Orders via Direct PostgreSQL.
     */
    public function apiIndex(Request $request)
    {
        try {
            $status = $request->query('status');
            $orders = $this->odooPg->getPurchaseOrders(state: $status, limit: 100);

            return response()->json([
                'success' => true,
                'source'  => 'PostgreSQL Direct',
                'data'    => $orders,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
