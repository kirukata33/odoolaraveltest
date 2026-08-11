<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Purchase Order - Odoo</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background: #f6f7f9; color: #222; }
        h1 { margin-bottom: 4px; }
        .subtitle { color: #666; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        th, td { padding: 10px 14px; border-bottom: 1px solid #e5e5e5; text-align: left; font-size: 14px; }
        th { background: #2c3e50; color: #fff; }
        tr:hover { background: #f1f5f9; }
        .badge { padding: 3px 10px; border-radius: 12px; font-size: 12px; color: #fff; }
        .badge-draft { background: #95a5a6; }
        .badge-sent { background: #3498db; }
        .badge-purchase { background: #27ae60; }
        .badge-done { background: #16a085; }
        .badge-cancel { background: #c0392b; }
        .error-box { background: #fdecea; border: 1px solid #f5c6cb; color: #c0392b; padding: 14px; border-radius: 6px; margin-bottom: 16px; }
        .filters { margin-bottom: 16px; }
        .filters a { margin-right: 10px; padding: 6px 12px; background: #fff; border: 1px solid #ccc; border-radius: 6px; text-decoration: none; color: #333; font-size: 13px; }
        .filters a.active { background: #2c3e50; color: #fff; border-color: #2c3e50; }
    </style>
</head>
<body>

    <h1>Laporan Purchase Order</h1>
    <p class="subtitle">Data diambil langsung dari Odoo lewat API (JSON-RPC)</p>

    <div class="filters">
        <a href="{{ route('purchase-orders.index') }}" class="{{ !$status ? 'active' : '' }}">Semua</a>
        <a href="{{ route('purchase-orders.index', ['status' => 'draft']) }}" class="{{ $status == 'draft' ? 'active' : '' }}">Draft (RFQ)</a>
        <a href="{{ route('purchase-orders.index', ['status' => 'purchase']) }}" class="{{ $status == 'purchase' ? 'active' : '' }}">Confirmed</a>
        <a href="{{ route('purchase-orders.index', ['status' => 'done']) }}" class="{{ $status == 'done' ? 'active' : '' }}">Done</a>
        <a href="{{ route('purchase-orders.index', ['status' => 'cancel']) }}" class="{{ $status == 'cancel' ? 'active' : '' }}">Cancelled</a>
    </div>

    @if ($error)
        <div class="error-box">
            <strong>Gagal mengambil data dari Odoo:</strong><br>
            {{ $error }}
        </div>
    @endif

    @if (!$error)
        <table>
            <thead>
                <tr>
                    <th>No. PO</th>
                    <th>Vendor</th>
                    <th>Tanggal Order</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Dibuat oleh</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td>{{ $order['name'] ?? '-' }}</td>
                        <td>{{ $order['partner_id'][1] ?? '-' }}</td>
                        <td>{{ $order['date_order'] ?? '-' }}</td>
                        <td>Rp {{ number_format($order['amount_total'] ?? 0, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge badge-{{ $order['state'] ?? 'draft' }}">
                                {{ ucfirst($order['state'] ?? '-') }}
                            </span>
                        </td>
                        <td>{{ $order['user_id'][1] ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding: 20px;">Tidak ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

</body>
</html>
