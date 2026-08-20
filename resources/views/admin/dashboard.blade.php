<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin — Inventori</title>
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
                <strong>Inventori</strong>
                <span>Admin Panel</span>
            </div>
        </a>

        <nav class="sidebar-nav">
            <div class="nav-section-label">Menu Utama</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-link active">
                <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>
            <a href="{{ route('purchase-orders.index') }}" class="nav-link">
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
                <div class="user-card-name">{{ $user->name }}</div>
                <div class="user-card-role">{{ $user->email }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">
                    <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <main class="main">
        <header class="topbar">
            <h1>Dashboard</h1>
            <span class="topbar-date">{{ \Carbon\Carbon::now('Asia/Jakarta')->isoFormat('dddd, D MMMM Y') }}</span>
        </header>

        <div class="content">

            @if (session('success'))
                <div class="alert" style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
                     {{ session('success') }}
                </div>
            @endif

            {{-- WELCOME HERO --}}
            <div style="background: var(--white); border: 1px solid var(--border); border-radius: 10px; padding: 24px; margin-bottom: 24px;">
                <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--text-dark); margin-bottom: 4px;">
                    Selamat Datang kembali, {{ $user->name }}!
                </h2>
                <p style="font-size: 0.875rem; color: var(--text-muted);">
                    Berikut adalah statistik ringkasan data inventori dan pesanan transaksi Anda hari ini.
                </p>
            </div>

            {{-- STATS GRID --}}
            <div class="stats-grid">
                {{-- Card Purchase Orders --}}
                <div class="stat-card">
                    <div class="stat-icon stat-icon-blue">
                        <svg viewBox="0 0 24 24" stroke="currentColor"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    </div>
                    <div class="stat-info">
                        <div class="stat-label">Purchase Orders</div>
                        <div class="stat-value">{{ $stats['po_count'] ?? 0 }}</div>
                        <div class="stat-subtext">Total: Rp {{ number_format($stats['po_total_sum'] ?? 0, 0, ',', '.') }}</div>
                    </div>
                </div>

                {{-- Card Sales Orders --}}
                <div class="stat-card">
                    <div class="stat-icon stat-icon-green">
                        <svg viewBox="0 0 24 24" stroke="currentColor"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                    </div>
                    <div class="stat-info">
                        <div class="stat-label">Sales Orders</div>
                        <div class="stat-value">{{ $stats['so_count'] ?? 0 }}</div>
                        <div class="stat-subtext">Total: Rp {{ number_format($stats['so_total_sum'] ?? 0, 0, ',', '.') }}</div>
                    </div>
                </div>

                {{-- Card Products --}}
                <div class="stat-card">
                    <div class="stat-icon stat-icon-purple">
                        <svg viewBox="0 0 24 24" stroke="currentColor"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                    </div>
                    <div class="stat-info">
                        <div class="stat-label">Produk Aktif</div>
                        <div class="stat-value">{{ $stats['product_count'] ?? 0 }}</div>
                        <div class="stat-subtext">Item Terdaftar</div>
                    </div>
                </div>

                {{-- Card Customers --}}
                <div class="stat-card">
                    <div class="stat-icon stat-icon-amber">
                        <svg viewBox="0 0 24 24" stroke="currentColor"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div class="stat-info">
                        <div class="stat-label">Pelanggan / Partner</div>
                        <div class="stat-value">{{ $stats['customer_count'] ?? 0 }}</div>
                        <div class="stat-subtext">Mitra Bisnis</div>
                    </div>
                </div>
            </div>

            {{-- RECENT TRANSACTIONS GRID --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 28px;">

                {{-- PO TERBARU --}}
                <div class="card">
                    <div class="card-head" style="display: flex; justify-content: space-between; align-items: center;">
                        <span> Purchase Orders Terbaru</span>
                        <a href="{{ route('purchase-orders.index') }}" style="font-size: 0.75rem; color: var(--primary); text-decoration: none; font-weight: 500;">
                            Lihat Semua →
                        </a>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>No. PO</th>
                                <th>Vendor</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($stats['recent_pos'] ?? [] as $po)
                                <tr>
                                    <td>
                                        <a href="{{ route('purchase-orders.show', $po['id']) }}" style="font-weight: 600; color: var(--primary); text-decoration: none;">
                                            {{ $po['name'] }}
                                        </a>
                                    </td>
                                    <td>{{ $po['partner_id'][1] ?? '-' }}</td>
                                    <td>Rp {{ number_format($po['amount_total'], 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $po['state'] }}">
                                            {{ ucfirst($po['state']) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 20px;">
                                        Belum ada data Purchase Order.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- SO TERBARU --}}
                <div class="card">
                    <div class="card-head" style="display: flex; justify-content: space-between; align-items: center;">
                        <span> Sales Orders Terbaru</span>
                        <a href="{{ route('sales-orders.index') }}" style="font-size: 0.75rem; color: var(--primary); text-decoration: none; font-weight: 500;">
                            Lihat Semua →
                        </a>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>No. SO</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($stats['recent_sos'] ?? [] as $so)
                                <tr>
                                    <td>
                                        <a href="{{ route('sales-orders.show', $so['id']) }}" style="font-weight: 600; color: var(--primary); text-decoration: none;">
                                            {{ $so['name'] }}
                                        </a>
                                    </td>
                                    <td>{{ $so['partner_id'][1] ?? '-' }}</td>
                                    <td>Rp {{ number_format($so['amount_total'], 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $so['state'] }}">
                                            @if(($so['state'] ?? '') == 'draft')
                                                Quotation
                                            @elseif(($so['state'] ?? '') == 'sale')
                                                Sales Order
                                            @else
                                                {{ ucfirst($so['state']) }}
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 20px;">
                                        Belum ada data Sales Order.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

            {{-- INFORMASI AKUN (MINIMALIS) --}}
            <div class="card">
                <div class="card-head"> Profil Akun Pengguna</div>
                <div style="padding: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
                    <div style="display: flex; align-items: center; gap: 14px;">
                        <div style="width: 44px; height: 44px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.125rem;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-weight: 700; font-size: 1rem; color: var(--text-dark);">{{ $user->name }}</div>
                            <div style="font-size: 0.8125rem; color: var(--text-muted);">{{ $user->email }}</div>
                        </div>
                    </div>
                    <div style="font-size: 0.8125rem; color: var(--text-muted);">
                        Terdaftar sejak: <strong>{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</strong>
                    </div>
                </div>
            </div>

        </div>
    </main>

</body>
</html>
