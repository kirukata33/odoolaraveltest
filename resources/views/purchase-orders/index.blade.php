<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Orders — Laravel × Odoo 19</title>
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
            <h1>Purchase Orders</h1>
            <span class="topbar-date">{{ \Carbon\Carbon::now('Asia/Jakarta')->isoFormat('dddd, D MMMM Y') }}</span>
        </header>

        <div class="content">

            <p class="content-subtitle">Data diambil langsung dari Database PostgreSQL Odoo 19</p>

            @if ($error)
                <div class="alert alert-error">
                    <strong>Gagal mengambil data dari Database Odoo:</strong> {{ $error }}
                </div>
            @endif

            <div class="filters">
                <a href="{{ route('purchase-orders.index') }}" class="{{ !$status ? 'active' : '' }}">Semua</a>
                <a href="{{ route('purchase-orders.index', ['status' => 'draft']) }}" class="{{ $status == 'draft' ? 'active' : '' }}">Draft (RFQ)</a>
                <a href="{{ route('purchase-orders.index', ['status' => 'purchase']) }}" class="{{ $status == 'purchase' ? 'active' : '' }}">Confirmed</a>
                <a href="{{ route('purchase-orders.index', ['status' => 'done']) }}" class="{{ $status == 'done' ? 'active' : '' }}">Done</a>
                <a href="{{ route('purchase-orders.index', ['status' => 'cancel']) }}" class="{{ $status == 'cancel' ? 'active' : '' }}">Cancelled</a>
            </div>

            @if (!$error)
                <div class="card">
                    <div class="card-head">Daftar Purchase Order</div>
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
                                    <td>
                                        <a href="{{ route('purchase-orders.show', $order['id']) }}" style="font-weight: 700; color: #1d4ed8; text-decoration: none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                            {{ $order['name'] ?? '-' }}
                                        </a>
                                    </td>
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
                                    <td colspan="6" class="empty-state">Tidak ada data.</td>
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
