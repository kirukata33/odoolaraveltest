<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Orders — Laravel × Odoo 19</title>
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

        .nav-section-label {
            font-size: 0.6875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--text-light);
            padding: 0 8px;
            margin: 14px 0 6px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-mid);
            text-decoration: none;
            transition: background 0.15s, color 0.15s;
            margin-bottom: 2px;
        }

        .nav-link:hover { background: var(--bg); color: var(--text-dark); }
        .nav-link.active { background: #eff6ff; color: var(--primary); font-weight: 600; }
        .nav-link svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2; flex-shrink: 0; }

        .sidebar-footer {
            padding: 14px 12px;
            border-top: 1px solid var(--border);
        }

        .user-card {
            padding: 10px;
            border-radius: 6px;
            background: var(--bg);
            margin-bottom: 8px;
        }

        .user-card-name {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-dark);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-card-role {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 1px;
        }

        .btn-logout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            width: 100%;
            padding: 8px;
            border: 1px solid var(--border);
            border-radius: 6px;
            background: var(--white);
            font-family: 'Inter', sans-serif;
            font-size: 0.8125rem;
            font-weight: 500;
            color: var(--text-mid);
            cursor: pointer;
            text-decoration: none;
            transition: background 0.15s, border-color 0.15s;
        }

        .btn-logout:hover { background: var(--bg); border-color: #d1d5db; }
        .btn-logout svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2; }

        /* ── MAIN ── */
        .main {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ── TOPBAR ── */
        .topbar {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky; top: 0; z-index: 10;
        }

        .topbar h1 { font-size: 1rem; font-weight: 700; color: var(--text-dark); }
        .topbar-date { font-size: 0.8125rem; color: var(--text-muted); }

        /* ── CONTENT ── */
        .content { padding: 28px; flex: 1; }

        .content-subtitle {
            font-size: 0.8125rem;
            color: var(--text-muted);
            margin-bottom: 20px;
        }

        .alert {
            padding: 10px 14px;
            border-radius: 6px;
            font-size: 0.875rem;
            margin-bottom: 20px;
            border: 1px solid #bbf7d0;
            background: #f0fdf4;
            color: #16a34a;
        }

        .alert-error {
            border: 1px solid #fecaca;
            background: #fef2f2;
            color: #dc2626;
        }

        /* ── FILTERS ── */
        .filters {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filters a {
            padding: 7px 14px;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 6px;
            text-decoration: none;
            color: var(--text-mid);
            font-size: 0.8125rem;
            font-weight: 500;
            transition: background 0.15s, border-color 0.15s;
        }

        .filters a:hover { background: var(--bg); }
        .filters a.active { background: var(--primary); color: var(--white); border-color: var(--primary); }

        /* ── CARD / TABLE ── */
        .card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
        }

        .card-head {
            padding: 14px 20px;
            border-bottom: 1px solid var(--border);
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-dark);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 10px 20px;
            font-size: 0.875rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        table tr:last-child td { border-bottom: none; }

        table th {
            background: #f9fafb;
            font-weight: 600;
            color: var(--text-muted);
            font-size: 0.8125rem;
        }

        table td { color: var(--text-dark); }
        table tbody tr:hover { background: #f9fafb; }

        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            color: #fff;
        }

        .badge-draft   { background: #9ca3af; }
        .badge-sent    { background: #3b82f6; }
        .badge-sale    { background: #16a34a; }
        .badge-done    { background: #0d9488; }
        .badge-cancel  { background: #dc2626; }

        .empty-state {
            text-align: center;
            padding: 32px 20px;
            color: var(--text-muted);
            font-size: 0.875rem;
        }

        @media (max-width: 760px) {
            .sidebar { display: none; }
            .main { margin-left: 0; }
            .content { padding: 20px 16px; }
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

    {{-- MAIN --}}
    <main class="main">
        <header class="topbar">
            <h1>Sales Orders</h1>
            <span class="topbar-date">{{ \Carbon\Carbon::now('Asia/Jakarta')->isoFormat('dddd, D MMMM Y') }}</span>
        </header>

        <div class="content">

            <p class="content-subtitle">Data diambil langsung dari Database PostgreSQL Odoo 19 (Tabel: sale_order)</p>

            @if ($error)
                <div class="alert alert-error">
                    <strong>Gagal mengambil data dari Database Odoo:</strong> {{ $error }}
                </div>
            @endif

            <div class="filters">
                <a href="{{ route('sales-orders.index') }}" class="{{ !$status ? 'active' : '' }}">Semua</a>
                <a href="{{ route('sales-orders.index', ['status' => 'draft']) }}" class="{{ $status == 'draft' ? 'active' : '' }}">Quotation</a>
                <a href="{{ route('sales-orders.index', ['status' => 'sent']) }}" class="{{ $status == 'sent' ? 'active' : '' }}">Quotation Sent</a>
                <a href="{{ route('sales-orders.index', ['status' => 'sale']) }}" class="{{ $status == 'sale' ? 'active' : '' }}">Sales Order</a>
                <a href="{{ route('sales-orders.index', ['status' => 'done']) }}" class="{{ $status == 'done' ? 'active' : '' }}">Locked (Done)</a>
                <a href="{{ route('sales-orders.index', ['status' => 'cancel']) }}" class="{{ $status == 'cancel' ? 'active' : '' }}">Cancelled</a>
            </div>

            @if (!$error)
                <div class="card">
                    <div class="card-head">Daftar Sales Order</div>
                    <table>
                        <thead>
                            <tr>
                                <th>No. SO</th>
                                <th>Pelanggan (Customer)</th>
                                <th>Tanggal Order</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Salesperson</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $order)
                                <tr>
                                    <td><strong>{{ $order['name'] ?? '-' }}</strong></td>
                                    <td>{{ $order['partner_id'][1] ?? '-' }}</td>
                                    <td>{{ $order['date_order'] ?? '-' }}</td>
                                    <td>Rp {{ number_format($order['amount_total'] ?? 0, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $order['state'] ?? 'draft' }}">
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
                                    </td>
                                    <td>{{ $order['user_id'][1] ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="empty-state">Tidak ada data Sales Order.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

        </div>
    </main>

</body>
</html>
