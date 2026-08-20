<?php

namespace App\Http\Controllers;

use App\Services\OdooPgService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class SalesOrderController extends Controller
{
    protected OdooPgService $odooPg;

    public function __construct(OdooPgService $odooPg)
    {
        $this->odooPg = $odooPg;
    }

    /**
     * Halaman laporan: daftar Sales Order langsung dari PostgreSQL Odoo 19.
     */
    public function index(Request $request)
    {
        $error = null;
        $orders = [];
        $user = Auth::user();

        // Filter status via query string: ?status=sale
        $status = $request->query('status');

        try {
            $orders = $this->odooPg->getSalesOrders(
                state: $status,
                limit: 100
            );
        } catch (Exception $e) {
            $error = 'Gagal mengambil data Sales Order dari PostgreSQL Odoo: ' . $e->getMessage();
        }

        return view('sales-orders.index', [
            'orders' => $orders,
            'error'  => $error,
            'status' => $status,
            'user'   => $user,
        ]);
    }
}
