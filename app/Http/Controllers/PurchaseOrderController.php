<?php

namespace App\Http\Controllers;

use App\Services\OdooService;
use Illuminate\Http\Request;
use Exception;

class PurchaseOrderController extends Controller
{
    protected OdooService $odoo;

    public function __construct(OdooService $odoo)
    {
        $this->odoo = $odoo;
    }

    /**
     * Halaman laporan: daftar Purchase Order dari Odoo.
     */
    public function index(Request $request)
    {
        $error = null;
        $orders = [];

        // Filter opsional lewat query string, misal: ?status=purchase
        $status = $request->query('status');
        $domain = $status ? [['state', '=', $status]] : [];

        try {
            $orders = $this->odoo->searchRead(
                model: 'purchase.order',
                domain: $domain,
                fields: [
                    'name',            // Nomor PO, misal P00001
                    'partner_id',      // Vendor/supplier
                    'date_order',      // Tanggal order
                    'amount_total',    // Total nilai
                    'state',           // Status: draft, sent, purchase, done, cancel
                    'user_id',         // Yang membuat/bertanggung jawab
                ],
                limit: 100,
            );
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        return view('purchase-orders.index', [
            'orders' => $orders,
            'error'  => $error,
            'status' => $status,
        ]);
    }

    /**
     * Endpoint API JSON (kalau butuh dikonsumsi frontend lain, bukan Blade).
     */
    public function apiIndex(Request $request)
    {
        try {
            $orders = $this->odoo->searchRead(
                model: 'purchase.order',
                domain: [],
                fields: ['name', 'partner_id', 'date_order', 'amount_total', 'state'],
                limit: 100,
            );

            return response()->json([
                'success' => true,
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
