<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail SO {{ $order['name'] ?? '' }} — Laravel × Odoo 19</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #1d4ed8;
            --primary-hover: #1e40af;
            --bg: #f3f4f6;
            --white: #ffffff;
            --border: #e5e7eb;
            --text-dark: #111827;
            --text-mid: #374151;
            --text-muted: #6b7280;
            --text-light: #9ca3af;
            --sidebar-w: 220px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            font-size: 0.9375rem;
        }

        /* ── SIDEBAR ── */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--white);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
        }

        .sidebar-brand {
            padding: 20px 20px 18px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--text-dark);
        }

        .brand-box {
            width: 32px; height: 32px;
            background: var(--primary);
            border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .brand-box svg { width: 16px; height: 16px; stroke: white; fill: none; stroke-width: 2; }
        .brand-label strong { display: block; font-size: 0.875rem; font-weight: 700; line-height: 1.2; }
        .brand-label span { font-size: 0.75rem; color: var(--text-muted); }

        .sidebar-nav { flex: 1; padding: 14px 12px; }
        .nav-section-label { font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px; color: var(--text-light); padding: 0 8px; margin: 14px 0 6px; }

        .nav-link {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 10px; border-radius: 6px; font-size: 0.875rem; font-weight: 500;
            color: var(--text-mid); text-decoration: none; transition: background 0.15s, color 0.15s; margin-bottom: 2px;
        }
        .nav-link:hover { background: var(--bg); color: var(--text-dark); }
        .nav-link.active { background: #eff6ff; color: var(--primary); font-weight: 600; }
        .nav-link svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2; flex-shrink: 0; }

        .sidebar-footer { padding: 14px 12px; border-top: 1px solid var(--border); }
        .user-card { padding: 10px; border-radius: 6px; background: var(--bg); margin-bottom: 8px; }
        .user-card-name { font-size: 0.875rem; font-weight: 600; color: var(--text-dark); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-card-role { font-size: 0.75rem; color: var(--text-muted); margin-top: 1px; }

        .btn-logout {
            display: flex; align-items: center; justify-content: center; gap: 7px;
            width: 100%; padding: 8px; border: 1px solid var(--border); border-radius: 6px;
            background: var(--white); font-family: 'Inter', sans-serif; font-size: 0.8125rem;
            font-weight: 500; color: var(--text-mid); cursor: pointer; text-decoration: none; transition: background 0.15s;
        }
        .btn-logout:hover { background: var(--bg); }

        /* ── MAIN ── */
        .main { margin-left: var(--sidebar-w); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
        .topbar { background: var(--white); border-bottom: 1px solid var(--border); padding: 14px 28px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 10; }
        .topbar h1 { font-size: 1rem; font-weight: 700; color: var(--text-dark); }

        .btn-back {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 7px 14px; background: var(--white); border: 1px solid var(--border);
            border-radius: 6px; color: var(--text-mid); text-decoration: none; font-size: 0.8125rem; font-weight: 500; transition: background 0.15s;
        }
        .btn-back:hover { background: var(--bg); }

        .content { padding: 28px; flex: 1; }

        /* ── HEADER CARD ── */
        .header-card {
            background: var(--white); border: 1px solid var(--border); border-radius: 8px;
            padding: 24px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px;
        }

        .so-title { font-size: 1.5rem; font-weight: 700; color: var(--text-dark); display: flex; align-items: center; gap: 12px; }
        .so-meta { margin-top: 6px; font-size: 0.875rem; color: var(--text-muted); }

        .badge { display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 0.75rem; font-weight: 600; color: #fff; }
        .badge-draft { background: #9ca3af; }
        .badge-sent { background: #3b82f6; }
        .badge-sale { background: #16a34a; }
        .badge-done { background: #0d9488; }
        .badge-cancel { background: #dc2626; }

        /* ── GRID INFO ── */
        .grid-info { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px; }
        .info-card { background: var(--white); border: 1px solid var(--border); border-radius: 8px; padding: 20px; }
        .info-card-title { font-size: 0.8125rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); margin-bottom: 14px; border-bottom: 1px solid var(--border); padding-bottom: 8px; }

        .info-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 0.875rem; }
        .info-row:last-child { margin-bottom: 0; }
        .info-label { color: var(--text-muted); }
        .info-value { font-weight: 600; color: var(--text-dark); text-align: right; }

        /* ── TABLE ITEMS ── */
        .card { background: var(--white); border: 1px solid var(--border); border-radius: 8px; overflow: hidden; margin-bottom: 24px; }
        .card-head { padding: 16px 20px; border-bottom: 1px solid var(--border); font-size: 0.9375rem; font-weight: 700; color: var(--text-dark); background: #fafafa; }

        table { width: 100%; border-collapse: collapse; }
        table th, table td { padding: 12px 20px; font-size: 0.875rem; text-align: left; border-bottom: 1px solid var(--border); }
        table tr:last-child td { border-bottom: none; }
        table th { background: #f9fafb; font-weight: 600; color: var(--text-muted); font-size: 0.8125rem; }
        table td { color: var(--text-dark); }
        table tbody tr:hover { background: #f9fafb; }
        .text-right { text-align: right; }

        /* ── TOTALS ── */
        .totals-box { width: 340px; margin-left: auto; background: var(--white); border: 1px solid var(--border); border-radius: 8px; padding: 20px; }
        .total-row { display: flex; justify-content: space-between; font-size: 0.875rem; margin-bottom: 10px; }
        .total-row.grand-total { border-top: 2px solid var(--border); padding-top: 10px; font-size: 1.125rem; font-weight: 700; color: var(--primary); margin-bottom: 0; }

        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main { margin-left: 0; }
            .grid-info { grid-template-columns: 1fr; }
            .totals-box { width: 100%; }
        }
    </style>
</head>
<body>

    {{-- SIDEBAR --}}
    <aside class="sidebar">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
            <div class="brand-box">
                <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            </div>
            <div class="brand-label">
                <strong>Laravel × Odoo 19</strong>
                <span>Admin Panel</span>
            </div>
        </a>

        <nav class="sidebar-nav">
            <div class="nav-section-label">Menu</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-link">
                <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>
            <a href="{{ route('purchase-orders.index') }}" class="nav-link">
                <svg viewBox="0 0 24 24"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                Purchase Orders
            </a>
            <a href="{{ route('sales-orders.index') }}" class="nav-link active">
                <svg viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                Sales Orders
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="user-card">
                <div class="user-card-name">{{ $user->name ?? 'User' }}</div>
                <div class="user-card-role">{{ $user->email ?? '' }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" fill="none" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- MAIN --}}
    <main class="main">
        <header class="topbar">
            <h1>Detail Sales Order</h1>
            <a href="{{ route('sales-orders.index') }}" class="btn-back">
                ← Kembali ke Daftar SO
            </a>
        </header>

        <div class="content">

            @if ($error)
                <div class="alert alert-error" style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
                    <strong>Error:</strong> {{ $error }}
                </div>
            @endif

            @if ($order)
                <!-- HEADER CARD -->
                <div class="header-card">
                    <div>
                        <div class="so-title">
                            <span>{{ $order['name'] }}</span>
                            <span class="badge badge-{{ $order['state'] }}">
                                @if(($order['state'] ?? '') == 'draft')
                                    Quotation
                                @elseif(($order['state'] ?? '') == 'sent')
                                    Quotation Sent
                                @elseif(($order['state'] ?? '') == 'sale')
                                    Sales Order
                                @else
                                    {{ ucfirst($order['state'] ?? '-') }}
                                @endif
                            </span>
                        </div>
                        <div class="so-meta">
                            Tanggal Order: <strong>{{ $order['date_order'] ? \Carbon\Carbon::parse($order['date_order'])->isoFormat('D MMMM Y, HH:mm') : '-' }}</strong>
                        </div>
                    </div>
                    <div>
                        <button onclick="window.print()" class="btn-back" style="cursor: pointer;">
                            🖨️ Cetak SO
                        </button>
                    </div>
                </div>

                <!-- GRID INFO CUSTOMER & TRANSAKSI -->
                <div class="grid-info">
                    <div class="info-card">
                        <div class="info-card-title">👤 Informasi Pelanggan (Customer)</div>
                        <div class="info-row">
                            <span class="info-label">Nama Pelanggan</span>
                            <span class="info-value">{{ $order['partner_id'][1] ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Email</span>
                            <span class="info-value">{{ $order['partner_email'] ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Telepon</span>
                            <span class="info-value">{{ $order['partner_phone'] ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Kota / Alamat</span>
                            <span class="info-value">{{ $order['partner_city'] ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="info-card">
                        <div class="info-card-title">📑 Ringkasan Transaksi</div>
                        <div class="info-row">
                            <span class="info-label">No. Referensi SO</span>
                            <span class="info-value">{{ $order['name'] }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Status SO</span>
                            <span class="info-value">{{ ucfirst($order['state']) }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Sumber Data</span>
                            <span class="info-value" style="color: #16a34a;">Direct PostgreSQL (`sale_order`)</span>
                        </div>
                    </div>
                </div>

                <!-- ITEM LINES TABLE -->
                <div class="card">
                    <div class="card-head">📦 Rincian Produk (Order Lines)</div>
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Deskripsi / Nama Produk</th>
                                <th class="text-right">Kuantitas (Qty)</th>
                                <th class="text-right">Harga Satuan</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($order['lines'] as $index => $line)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $line['product_name'] }}</strong>
                                        @if ($line['description'] && $line['description'] !== $line['product_name'])
                                            <div style="font-size: 0.75rem; color: #6b7280;">{{ $line['description'] }}</div>
                                        @endif
                                    </td>
                                    <td class="text-right">{{ number_format($line['product_qty'], 2, ',', '.') }}</td>
                                    <td class="text-right">Rp {{ number_format($line['price_unit'], 0, ',', '.') }}</td>
                                    <td class="text-right"><strong>Rp {{ number_format($line['price_subtotal'], 0, ',', '.') }}</strong></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; color: #6b7280; padding: 24px;">Tidak ada item produk pada Sales Order ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- TOTALS BOX -->
                <div class="totals-box">
                    <div class="total-row">
                        <span>Subtotal (Untaxed)</span>
                        <span>Rp {{ number_format($order['amount_untaxed'], 0, ',', '.') }}</span>
                    </div>
                    <div class="total-row">
                        <span>Pajak (Tax)</span>
                        <span>Rp {{ number_format($order['amount_tax'], 0, ',', '.') }}</span>
                    </div>
                    <div class="total-row grand-total">
                        <span>Total Sales Order</span>
                        <span>Rp {{ number_format($order['amount_total'], 0, ',', '.') }}</span>
                    </div>
                </div>
            @endif

        </div>
    </main>

</body>
</html>
