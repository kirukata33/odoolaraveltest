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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
            <span class="brand-name">Inventori</span>
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
