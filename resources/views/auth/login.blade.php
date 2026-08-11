<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login ke sistem administrasi Laravel x Odoo 19">
    <title>Login — Laravel × Odoo 19</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #1d4ed8;
            --primary-hover: #1e40af;
            --primary-light: #dbeafe;
            --text-dark: #111827;
            --text-mid: #374151;
            --text-muted: #6b7280;
            --border: #d1d5db;
            --border-focus: #1d4ed8;
            --bg: #f3f4f6;
            --white: #ffffff;
            --error: #dc2626;
            --error-bg: #fef2f2;
            --error-border: #fecaca;
            --success-bg: #f0fdf4;
            --success-border: #bbf7d0;
            --success-text: #16a34a;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── TOP HEADER BAR ── */
        .top-bar {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 0 40px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .top-bar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .brand-icon {
            width: 34px;
            height: 34px;
            background: var(--primary);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .brand-icon svg { width: 18px; height: 18px; stroke: white; fill: none; }

        .brand-name {
            font-size: 0.9375rem;
            font-weight: 700;
            color: var(--text-dark);
            letter-spacing: -0.3px;
        }

        .top-bar-info {
            font-size: 0.8125rem;
            color: var(--text-muted);
        }

        /* ── MAIN LAYOUT ── */
        .page-body {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
        }

        /* ── CARD ── */
        .card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08), 0 4px 16px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .card-header {
            padding: 28px 32px 24px;
            border-bottom: 1px solid #f3f4f6;
        }

        .card-header h1 {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 4px;
            letter-spacing: -0.3px;
        }

        .card-header p {
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .card-body { padding: 28px 32px; }

        /* ── ALERTS ── */
        .alert {
            padding: 12px 14px;
            border-radius: 6px;
            font-size: 0.875rem;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            border: 1px solid;
        }

        .alert svg { width: 16px; height: 16px; flex-shrink: 0; margin-top: 1px; }

        .alert-success {
            background: var(--success-bg);
            border-color: var(--success-border);
            color: var(--success-text);
        }

        .alert-error {
            background: var(--error-bg);
            border-color: var(--error-border);
            color: var(--error);
        }

        /* ── FORM ── */
        .form-group { margin-bottom: 18px; }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-mid);
            margin-bottom: 6px;
        }

        .form-label .required {
            color: var(--error);
            margin-left: 2px;
        }

        .input-wrap { position: relative; }

        .input-icon {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            display: flex;
            pointer-events: none;
        }

        .input-icon svg { width: 16px; height: 16px; stroke: currentColor; fill: none; }

        .form-input {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 9px 36px 9px 36px;
            font-family: 'Inter', sans-serif;
            font-size: 0.9375rem;
            color: var(--text-dark);
            background: var(--white);
            transition: border-color 0.15s, box-shadow 0.15s;
            outline: none;
        }

        .form-input::placeholder { color: #9ca3af; }

        .form-input:focus {
            border-color: var(--border-focus);
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.1);
        }

        .form-input.is-invalid {
            border-color: var(--error);
        }

        .form-input.is-invalid:focus {
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
        }

        .invalid-text {
            margin-top: 5px;
            font-size: 0.8125rem;
            color: var(--error);
        }

        /* Password toggle */
        .pass-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #9ca3af;
            display: flex;
            padding: 2px;
            transition: color 0.15s;
        }

        .pass-toggle:hover { color: var(--text-muted); }
        .pass-toggle svg { width: 16px; height: 16px; stroke: currentColor; fill: none; }

        /* ── OPTIONS ROW ── */
        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .checkbox-label input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--primary);
            cursor: pointer;
        }

        .checkbox-label span {
            font-size: 0.875rem;
            color: var(--text-mid);
            user-select: none;
        }

        /* ── BUTTON ── */
        .btn-primary {
            width: 100%;
            padding: 10px 16px;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 6px;
            font-family: 'Inter', sans-serif;
            font-size: 0.9375rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s, box-shadow 0.15s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            letter-spacing: 0.1px;
        }

        .btn-primary:hover { background: var(--primary-hover); box-shadow: 0 2px 8px rgba(29,78,216,0.25); }
        .btn-primary:active { background: #1e3a8a; }
        .btn-primary:disabled { opacity: 0.65; cursor: not-allowed; }

        .spinner {
            width: 16px; height: 16px;
            border: 2px solid rgba(255,255,255,0.35);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.65s linear infinite;
            display: none;
        }

        .btn-primary.loading .spinner { display: block; }
        .btn-primary.loading .btn-text { display: none; }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── CARD FOOTER ── */
        .card-footer {
            padding: 14px 32px;
            background: #f9fafb;
            border-top: 1px solid var(--border);
            font-size: 0.8125rem;
            color: var(--text-muted);
            text-align: center;
        }

        /* ── PAGE FOOTER ── */
        .page-footer {
            padding: 20px;
            text-align: center;
            font-size: 0.8125rem;
            color: #9ca3af;
            border-top: 1px solid var(--border);
            background: var(--white);
        }

        @media (max-width: 480px) {
            .top-bar { padding: 0 20px; }
            .card-header, .card-body { padding-left: 20px; padding-right: 20px; }
            .card-footer { padding-left: 20px; padding-right: 20px; }
        }
    </style>
</head>
<body>

    {{-- Top bar --}}
    <header class="top-bar">
        <a href="#" class="top-bar-brand">
            <div class="brand-icon">
                <svg stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
            </div>
            <span class="brand-name">Laravel × Odoo 19</span>
        </a>
        <span class="top-bar-info">Sistem Manajemen Terintegrasi</span>
    </header>

    {{-- Body --}}
    <div class="page-body">
        <div class="login-container">

            <div class="card">
                <div class="card-header">
                    <h1>Masuk ke Akun</h1>
                    <p>Gunakan kredensial yang telah diberikan oleh administrator</p>
                </div>

                <div class="card-body">

                    {{-- Success --}}
                    @if (session('success'))
                        <div class="alert alert-success">
                            <svg stroke-width="2" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Email error --}}
                    @if ($errors->has('email'))
                        <div class="alert alert-error">
                            <svg stroke-width="2" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            {{ $errors->first('email') }}
                        </div>
                    @endif

                    <form id="loginForm" method="POST" action="{{ route('login.post') }}" novalidate>
                        @csrf

                        {{-- Email --}}
                        <div class="form-group">
                            <label class="form-label" for="email">
                                Alamat Email <span class="required">*</span>
                            </label>
                            <div class="input-wrap">
                                <span class="input-icon">
                                    <svg stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                        <polyline points="22,6 12,13 2,6"/>
                                    </svg>
                                </span>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                    placeholder="nama@domain.com"
                                    value="{{ old('email') }}"
                                    required
                                    autocomplete="email"
                                    autofocus
                                >
                            </div>
                        </div>

                        {{-- Password --}}
                        <div class="form-group">
                            <label class="form-label" for="password">
                                Kata Sandi <span class="required">*</span>
                            </label>
                            <div class="input-wrap">
                                <span class="input-icon">
                                    <svg stroke-width="2" viewBox="0 0 24 24">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                    </svg>
                                </span>
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                    placeholder="••••••••"
                                    required
                                    autocomplete="current-password"
                                >
                                <button type="button" id="togglePass" class="pass-toggle" aria-label="Tampilkan kata sandi">
                                    <svg id="eyeIcon" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Remember me --}}
                        <div class="form-options">
                            <label class="checkbox-label">
                                <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                <span>Ingat saya</span>
                            </label>
                        </div>

                        {{-- Submit --}}
                        <button type="submit" class="btn-primary" id="loginBtn">
                            <div class="spinner"></div>
                            <span class="btn-text">Masuk</span>
                        </button>

                    </form>
                </div>

                <div class="card-footer">
                    Hubungi administrator sistem jika mengalami kendala akses.
                </div>
            </div>

        </div>
    </div>

    {{-- Page footer --}}
    <footer class="page-footer">
        &copy; {{ date('Y') }} Laravel × Odoo 19. Seluruh hak cipta dilindungi.
    </footer>

    <script>
        // Password toggle
        const toggleBtn = document.getElementById('togglePass');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        toggleBtn.addEventListener('click', () => {
            const isPass = passwordInput.type === 'password';
            passwordInput.type = isPass ? 'text' : 'password';
            eyeIcon.innerHTML = isPass
                ? `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>`
                : `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>`;
        });

        // Loading state
        document.getElementById('loginForm').addEventListener('submit', () => {
            const btn = document.getElementById('loginBtn');
            btn.classList.add('loading');
            btn.disabled = true;
        });
    </script>
</body>
</html>
