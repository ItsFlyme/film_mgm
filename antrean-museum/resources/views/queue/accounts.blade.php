<!DOCTYPE html>
<html lang="id" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f5f7fa">
    <title>Manajemen Akun | Teater Film MGM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/queue-theme.css') }}" rel="stylesheet">
</head>

<body class="admin-page">
    <header class="site-header">
        <a href="{{ route('queue.index') }}" class="brand" aria-label="Teater Film Dokumenter Museum Gunungapi Merapi">
            <span class="brand-mark" aria-hidden="true">
                <img class="brand-logo brand-logo-light" src="{{ asset('images/mgm-logo-light.png') }}" alt="">
                <img class="brand-logo brand-logo-dark" src="{{ asset('images/mgm-logo-dark.png') }}" alt="">
            </span>
            <span class="brand-copy">
                <strong>MUSEUM GUNUNGAPI MERAPI</strong>
                <span>Sistem antrean teater film</span>
            </span>
        </a>

        <div class="header-center">
            <nav class="site-nav" aria-label="Navigasi halaman">
                <a href="{{ route('queue.index') }}">Layar TV</a>
                <a href="{{ route('queue.kiosk') }}">Form</a>
                <a href="{{ route('queue.admin') }}">Admin</a>
            </nav>
            @include('queue.partials.theme-toggle')
            @include('queue.partials.account-menu')
        </div>

        <div class="header-meta">
            <time id="live-date" class="header-date"></time>
            <time id="live-clock" class="header-clock">--:--:-- WIB</time>
        </div>
    </header>

    <div class="admin-layout">
        <aside class="admin-sidebar" aria-label="Navigasi dashboard">
            <div class="sidebar-group">
                <p class="sidebar-label">Navigasi</p>
                <a class="sidebar-link" href="{{ route('queue.admin') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1V10Z"></path></svg>
                    Ringkasan operasional
                </a>
            </div>
            <div class="sidebar-group">
                <p class="sidebar-label">Pengelolaan</p>
                <a class="sidebar-link" href="{{ route('queue.history') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"></path><path d="M3 3v5h5m4-1v5l3 2"></path></svg>
                    Riwayat 30 hari
                </a>
                <a class="sidebar-link" href="{{ route('queue.accounts.index') }}" aria-current="page">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="9" cy="8" r="3.5"></circle><path d="M2.5 20v-1.5a6.5 6.5 0 0 1 13 0V20M17 8h5m-2.5-2.5v5"></path></svg>
                    Manajemen akun
                </a>
            </div>
            <div class="sidebar-status">
                <strong><span class="admin-online-indicator" aria-hidden="true"></span>Akses super admin</strong>
                Kelola akun petugas teater.
            </div>
        </aside>

        <main class="admin-content">
            <section class="admin-intro">
                <div>
                    <p class="page-kicker">Pengaturan sistem</p>
                    <h1 class="page-title">Manajemen akun</h1>
                    <p class="page-description">Tambah, perbarui, atau hapus akun admin dan super admin.</p>
                </div>
            </section>

            @if(session('success'))
                <div class="alert alert-success py-2" role="status">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger py-2" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="panel admin-section">
                <div class="admin-section-head">
                    <div>
                        <h2 class="panel-title">Tambah akun petugas</h2>
                        <p>Buat kredensial baru untuk mengakses kiosk dan dashboard antrean.</p>
                    </div>
                </div>
                <form action="{{ route('queue.accounts.store') }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-md-4">
                        <label for="new-username" class="form-label">Username</label>
                        <input id="new-username" name="username" type="text" value="{{ old('username') }}" minlength="3" maxlength="50" autocomplete="off" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label for="new-role" class="form-label">Role</label>
                        <select id="new-role" name="role" class="form-select" required>
                            <option value="admin" @selected(old('role', 'admin') === 'admin')>Admin</option>
                            <option value="super_admin" @selected(old('role') === 'super_admin')>Super Admin</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="new-password" class="form-label">Password</label>
                        <input id="new-password" name="password" type="password" minlength="8" autocomplete="new-password" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label for="new-password-confirmation" class="form-label">Konfirmasi password</label>
                        <input id="new-password-confirmation" name="password_confirmation" type="password" minlength="8" autocomplete="new-password" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-accent">Tambah akun</button>
                    </div>
                </form>
            </section>

            <section class="panel admin-section">
                <div class="admin-section-head">
                    <div>
                        <h2 class="panel-title">Akun terdaftar</h2>
                        <p>{{ $accounts->count() }} akun petugas. Kosongkan password jika tidak ingin mengubahnya.</p>
                    </div>
                </div>

                <div class="account-list">
                    @forelse($accounts as $account)
                        <article class="account-card">
                            <form action="{{ route('queue.accounts.update', $account) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="account-edit-grid">
                                    <div>
                                        <label for="username-{{ $account->id }}" class="form-label">Username</label>
                                        <input id="username-{{ $account->id }}" name="username" type="text" value="{{ $account->username }}" minlength="3" maxlength="50" class="form-control" required>
                                    </div>
                                    <div>
                                        <label for="role-{{ $account->id }}" class="form-label">Role</label>
                                        <select id="role-{{ $account->id }}" name="role" class="form-select" required>
                                            <option value="admin" @selected($account->role === 'admin')>Admin</option>
                                            <option value="super_admin" @selected($account->role === 'super_admin')>Super Admin</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="password-{{ $account->id }}" class="form-label">Password baru</label>
                                        <input id="password-{{ $account->id }}" name="password" type="password" minlength="8" autocomplete="new-password" class="form-control">
                                    </div>
                                    <div>
                                        <label for="password-confirmation-{{ $account->id }}" class="form-label">Konfirmasi password</label>
                                        <input id="password-confirmation-{{ $account->id }}" name="password_confirmation" type="password" minlength="8" autocomplete="new-password" class="form-control">
                                    </div>
                                </div>
                                <div class="account-actions">
                                    <button type="submit" class="btn btn-outline-secondary btn-sm">Simpan perubahan</button>
                                    @if(!$account->is(auth()->user()))
                                        <button type="submit" class="btn btn-outline-danger btn-sm" form="delete-account-{{ $account->id }}">Hapus akun</button>
                                    @else
                                        <span class="text-body-secondary small">Akun aktif</span>
                                    @endif
                                </div>
                            </form>
                            @if(!$account->is(auth()->user()))
                                <form id="delete-account-{{ $account->id }}" action="{{ route('queue.accounts.destroy', $account) }}" method="POST" onsubmit="return confirm('Hapus akun {{ $account->username }}?')">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            @endif
                        </article>
                    @empty
                        <p class="text-body-secondary mb-0">Belum ada akun petugas.</p>
                    @endforelse
                </div>
            </section>
        </main>
    </div>

    <script>
        const liveDate = document.getElementById('live-date');
        const liveClock = document.getElementById('live-clock');
        const dateFormatter = new Intl.DateTimeFormat('id-ID', {
            timeZone: 'Asia/Jakarta',
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        const clockFormatter = new Intl.DateTimeFormat('id-ID', {
            timeZone: 'Asia/Jakarta',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        });
        const updateClock = () => {
            const now = new Date();
            liveDate.textContent = dateFormatter.format(now);
            liveClock.textContent = `${clockFormatter.format(now)} WIB`;
        };
        updateClock();
        setInterval(updateClock, 1000);
    </script>
</body>

</html>
