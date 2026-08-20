<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

/**
 * Service untuk mengakses database Odoo 19 secara langsung via PostgreSQL (pgAdmin 4).
 *
 * Memberikan akses READ data berkecepatan tinggi tanpa overhead HTTP JSON-RPC API.
 */
class OdooPgService
{
    protected string $connection = 'odoo_pg';

    /**
     * Dapatkan instance Query Builder untuk tabel Odoo tertentu.
     */
    public function table(string $tableName): Builder
    {
        return DB::connection($this->connection)->table($tableName);
    }

    /**
     * Mengambil daftar Purchase Orders langsung dari PostgreSQL.
     */
    public function getPurchaseOrders(?string $state = null, int $limit = 80, int $offset = 0): array
    {
        $query = $this->table('purchase_order as po')
            ->leftJoin('res_partner as partner', 'po.partner_id', '=', 'partner.id')
            ->select([
                'po.id',
                'po.name',
                'po.date_order',
                'po.date_approve',
                'po.amount_untaxed',
                'po.amount_tax',
                'po.amount_total',
                'po.state',
                'po.partner_id',
                'partner.name as partner_name',
            ]);

        if ($state && $state !== 'all') {
            $query->where('po.state', $state);
        }

        $orders = $query->orderBy('po.id', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        return $orders->map(function ($po) {
            return [
                'id' => $po->id,
                'name' => $po->name,
                'partner_id' => [$po->partner_id, $po->partner_name ?? 'N/A'],
                'date_order' => $po->date_order,
                'amount_untaxed' => (float) $po->amount_untaxed,
                'amount_tax' => (float) $po->amount_tax,
                'amount_total' => (float) $po->amount_total,
                'state' => $po->state,
            ];
        })->toArray();
    }

    /**
     * Mengambil detail Purchase Order berdasarkan ID beserta order lines dan data Vendor.
     */
    public function getPurchaseOrderById(int $id): ?array
    {
        $po = $this->table('purchase_order as po')
            ->leftJoin('res_partner as partner', 'po.partner_id', '=', 'partner.id')
            ->select([
                'po.id',
                'po.name',
                'po.date_order',
                'po.date_approve',
                'po.amount_untaxed',
                'po.amount_tax',
                'po.amount_total',
                'po.state',
                'po.partner_id',
                'partner.name as partner_name',
                'partner.email as partner_email',
                'partner.phone as partner_phone',
                'partner.street as partner_street',
                'partner.city as partner_city',
            ])
            ->where('po.id', $id)
            ->first();

        if (!$po) {
            return null;
        }

        $lines = $this->table('purchase_order_line as pol')
            ->leftJoin('product_product as pp', 'pol.product_id', '=', 'pp.id')
            ->leftJoin('product_template as pt', 'pp.product_tmpl_id', '=', 'pt.id')
            ->select([
                'pol.id',
                'pol.name as description',
                'pol.product_qty',
                'pol.price_unit',
                'pol.price_subtotal',
                'pol.price_total',
                'pt.name as product_name',
            ])
            ->where('pol.order_id', $id)
            ->get();

        return [
            'id' => $po->id,
            'name' => $po->name,
            'partner_id' => [$po->partner_id, $po->partner_name ?? 'N/A'],
            'partner_email' => $po->partner_email ?? '-',
            'partner_phone' => $po->partner_phone ?? '-',
            'partner_street' => $po->partner_street ?? '-',
            'partner_city' => $po->partner_city ?? '-',
            'date_order' => $po->date_order,
            'date_approve' => $po->date_approve,
            'amount_untaxed' => (float) $po->amount_untaxed,
            'amount_tax' => (float) $po->amount_tax,
            'amount_total' => (float) $po->amount_total,
            'state' => $po->state,
            'lines' => $lines->map(function ($line) {
                return [
                    'id' => $line->id,
                    'product_name' => $line->product_name ?? $line->description ?? 'Item',
                    'description' => $line->description,
                    'product_qty' => (float) $line->product_qty,
                    'price_unit' => (float) $line->price_unit,
                    'price_subtotal' => (float) ($line->price_subtotal ?? ($line->product_qty * $line->price_unit)),
                ];
            })->toArray(),
        ];
    }

    /**
     * Mengambil daftar Sales Orders langsung dari PostgreSQL.
     */
    public function getSalesOrders(?string $state = null, int $limit = 80, int $offset = 0): array
    {
        $query = $this->table('sale_order as so')
            ->leftJoin('res_partner as partner', 'so.partner_id', '=', 'partner.id')
            ->select([
                'so.id',
                'so.name',
                'so.date_order',
                'so.amount_untaxed',
                'so.amount_tax',
                'so.amount_total',
                'so.state',
                'so.partner_id',
                'partner.name as partner_name',
            ]);

        if ($state && $state !== 'all') {
            $query->where('so.state', $state);
        }

        $orders = $query->orderBy('so.id', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        return $orders->map(function ($so) {
            return [
                'id' => $so->id,
                'name' => $so->name,
                'partner_id' => [$so->partner_id, $so->partner_name ?? 'N/A'],
                'date_order' => $so->date_order,
                'amount_untaxed' => (float) $so->amount_untaxed,
                'amount_tax' => (float) $so->amount_tax,
                'amount_total' => (float) $so->amount_total,
                'state' => $so->state,
            ];
        })->toArray();
    }

    /**
     * Mengambil detail Sales Order berdasarkan ID beserta order lines dan data Customer.
     */
    public function getSalesOrderById(int $id): ?array
    {
        $so = $this->table('sale_order as so')
            ->leftJoin('res_partner as partner', 'so.partner_id', '=', 'partner.id')
            ->select([
                'so.id',
                'so.name',
                'so.date_order',
                'so.amount_untaxed',
                'so.amount_tax',
                'so.amount_total',
                'so.state',
                'so.partner_id',
                'partner.name as partner_name',
                'partner.email as partner_email',
                'partner.phone as partner_phone',
                'partner.street as partner_street',
                'partner.city as partner_city',
            ])
            ->where('so.id', $id)
            ->first();

        if (!$so) {
            return null;
        }

        $lines = $this->table('sale_order_line as sol')
            ->leftJoin('product_product as pp', 'sol.product_id', '=', 'pp.id')
            ->leftJoin('product_template as pt', 'pp.product_tmpl_id', '=', 'pt.id')
            ->select([
                'sol.id',
                'sol.name as description',
                'sol.product_uom_qty as product_qty',
                'sol.price_unit',
                'sol.price_subtotal',
                'pt.name as product_name',
            ])
            ->where('sol.order_id', $id)
            ->get();

        return [
            'id' => $so->id,
            'name' => $so->name,
            'partner_id' => [$so->partner_id, $so->partner_name ?? 'N/A'],
            'partner_email' => $so->partner_email ?? '-',
            'partner_phone' => $so->partner_phone ?? '-',
            'partner_street' => $so->partner_street ?? '-',
            'partner_city' => $so->partner_city ?? '-',
            'date_order' => $so->date_order,
            'amount_untaxed' => (float) $so->amount_untaxed,
            'amount_tax' => (float) $so->amount_tax,
            'amount_total' => (float) $so->amount_total,
            'state' => $so->state,
            'lines' => $lines->map(function ($line) {
                return [
                    'id' => $line->id,
                    'product_name' => $line->product_name ?? $line->description ?? 'Item',
                    'description' => $line->description,
                    'product_qty' => (float) $line->product_qty,
                    'price_unit' => (float) $line->price_unit,
                    'price_subtotal' => (float) ($line->price_subtotal ?? ($line->product_qty * $line->price_unit)),
                ];
            })->toArray(),
        ];
    }

    /**
     * Mengambil daftar Produk langsung dari PostgreSQL.
     */
    public function getProducts(int $limit = 80, int $offset = 0): array
    {
        return $this->table('product_template as pt')
            ->select([
                'pt.id',
                'pt.name',
                'pt.list_price',
                'pt.standard_price',
                'pt.type',
                'pt.active',
            ])
            ->where('pt.active', true)
            ->orderBy('pt.id', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get()
            ->toArray();
    }

    /**
     * Mengambil daftar Customers/Partners langsung dari PostgreSQL.
     */
    public function getCustomers(int $limit = 80, int $offset = 0): array
    {
        return $this->table('res_partner as p')
            ->select([
                'p.id',
                'p.name',
                'p.email',
                'p.phone',
                'p.street',
                'p.city',
                'p.is_company',
            ])
            ->where('p.active', true)
            ->orderBy('p.id', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get()
            ->toArray();
    }

    /**
     * Mengambil pergerakan persediaan/stok dari PostgreSQL.
     */
    public function getStocks(int $limit = 80, int $offset = 0): array
    {
        return $this->table('stock_picking as sp')
            ->leftJoin('res_partner as partner', 'sp.partner_id', '=', 'partner.id')
            ->select([
                'sp.id',
                'sp.name',
                'sp.scheduled_date',
                'sp.date_done',
                'sp.state',
                'partner.name as partner_name',
            ])
            ->orderBy('sp.id', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get()
            ->toArray();
    }
}
