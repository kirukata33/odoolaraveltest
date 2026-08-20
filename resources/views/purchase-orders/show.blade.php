<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail PO {{ $order['name'] ?? '' }} — Laravel × Odoo 19</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
            <a href="{{ route('purchase-orders.index') }}" class="nav-link active">
                <svg viewBox="0 0 24 24"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                Purchase Orders
            </a>
            <a href="{{ route('sales-orders.index') }}" class="nav-link">
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
            <h1>Detail Purchase Order</h1>
            <a href="{{ route('purchase-orders.index') }}" class="btn-back">
                ← Kembali ke Daftar PO
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
                        <div class="po-title">
                            <span>{{ $order['name'] }}</span>
                            <span class="badge badge-{{ $order['state'] }}">
                                {{ ucfirst($order['state']) }}
                            </span>
                        </div>
                        <div class="po-meta">
                            Tanggal Order: <strong>{{ $order['date_order'] ? \Carbon\Carbon::parse($order['date_order'])->isoFormat('D MMMM Y, HH:mm') : '-' }}</strong>
                        </div>
                    </div>
                    <div>
                        <button onclick="window.print()" class="btn-back" style="cursor: pointer;">
                            🖨️ Cetak PO
                        </button>
                    </div>
                </div>

                <!-- GRID INFO VENDOR & TRANSAKSI -->
                <div class="grid-info">
                    <div class="info-card">
                        <div class="info-card-title">🏢 Informasi Vendor / Supplier</div>
                        <div class="info-row">
                            <span class="info-label">Nama Vendor</span>
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
                            <span class="info-label">No. Referensi PO</span>
                            <span class="info-value">{{ $order['name'] }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Status PO</span>
                            <span class="info-value">{{ ucfirst($order['state']) }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Sumber Data</span>
                            <span class="info-value" style="color: #16a34a;">Direct PostgreSQL (`purchase_order`)</span>
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
                                    <td colspan="5" style="text-align: center; color: #6b7280; padding: 24px;">Tidak ada item produk pada PO ini.</td>
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
                        <span>Total PO</span>
                        <span>Rp {{ number_format($order['amount_total'], 0, ',', '.') }}</span>
                    </div>
                </div>
            @endif

        </div>
    </main>

</body>
</html>
