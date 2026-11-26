<!DOCTYPE html>
<html lang="id" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f5f7fa">
    <title>Dashboard Operator | Teater Film MGM</title>
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
                <a href="{{ route('queue.admin') }}" aria-current="page">Admin</a>
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
                <a class="sidebar-link" href="#admin-overview">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1V10Z"></path></svg>
                    Ringkasan operasional
                </a>
            </div>
            <div class="sidebar-group">
                <p class="sidebar-label">Admin / Operator Loket</p>
                <a class="sidebar-link" href="#queue-controls" aria-current="page">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M4 5h16M4 12h16M4 19h16"></path><circle cx="8" cy="5" r="1"></circle><circle cx="15" cy="12" r="1"></circle><circle cx="10" cy="19" r="1"></circle></svg>
                    Kontrol antrean
                </a>
            </div>
            <div class="sidebar-group">
                <p class="sidebar-label">Pengelolaan</p>
                <a class="sidebar-link" href="#carousel-management">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="8.5" cy="9" r="1.5"></circle><path d="m21 15-5-5L5 20"></path></svg>
                    Editor slide promosi TV
                </a>
                <a class="sidebar-link" href="{{ route('queue.history') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"></path><path d="M3 3v5h5m4-1v5l3 2"></path></svg>
                    Riwayat 30 hari
                </a>
                @can('manage-accounts')
                <a class="sidebar-link" href="{{ route('queue.accounts.index') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="9" cy="8" r="3.5"></circle><path d="M2.5 20v-1.5a6.5 6.5 0 0 1 13 0V20M17 8h5m-2.5-2.5v5"></path></svg>
                    Manajemen akun
                </a>
                @endcan
            </div>
            <div class="sidebar-status">
                <strong><span class="admin-online-indicator" aria-hidden="true"></span>Sistem aktif</strong>
                Layar dan kiosk siap digunakan.
            </div>
        </aside>

        <main class="admin-content" id="admin-overview">
            <section class="admin-intro">
                <div>
                    <p class="page-kicker">Teater film dokumenter</p>
                    <h1 class="page-title">Kontrol antrean</h1>
                    <p class="page-description">Pantau antrean hari ini dan kelola informasi di layar teater.</p>
                </div>
                <span class="admin-online"><span class="admin-online-indicator" aria-hidden="true"></span>Loket aktif · Online</span>
            </section>

            <section class="admin-summary-grid" aria-label="Ringkasan operasional">
                <div class="admin-stats-column">
                    <section class="stats-grid" aria-label="Statistik pengunjung">
                        <article class="panel stat-card">
                            <h2>Antrean hari ini</h2>
                            <div class="stat-value"><span id="stats-today-total">{{ $stats['today']['total'] }}</span></div>
                            <div class="stat-detail">
                                Individu <span id="stats-today-individual">{{ $stats['today']['individual'] }}</span>
                                <span class="mx-1">·</span>
                                Rombongan <span id="stats-today-group">{{ $stats['today']['group'] }}</span>
                            </div>
                        </article>
                        <article class="panel stat-card">
                            <h2>Total bulan ini</h2>
                            <div class="stat-value"><span id="stats-month-total">{{ $stats['month']['total'] }}</span></div>
                            <div class="stat-detail">
                                Individu <span id="stats-month-individual">{{ $stats['month']['individual'] }}</span>
                                <span class="mx-1">·</span>
                                Rombongan <span id="stats-month-group">{{ $stats['month']['group'] }}</span>
                            </div>
                        </article>
                    </section>
                    <p class="stat-note">Rombongan dihitung dari satu perwakilan yang mendaftar, bukan jumlah anggota rombongan.</p>
                </div>

                <section id="queue-controls" class="panel admin-call-panel" aria-label="Kontrol pemanggilan antrean">
                    <div>
                        <span class="eyebrow accent-text">Sedang dipanggil</span>
                        <div id="active-number" class="admin-call-name">{{ $currentQueue->queue_number ?? '---' }}</div>
                        <div id="active-name" class="admin-call-number">{{ $currentQueue->institution_name ?? $currentQueue->visitor_name ?? 'Belum ada antrean dipanggil' }}</div>
                    </div>
                    @unless(request()->routeIs('queue.history'))
                    <button id="btn-next" class="btn btn-accent admin-next-button">
                        Panggil antrean selanjutnya <span aria-hidden="true">→</span>
                    </button>
                    @endunless
                </section>
            </section>

            @unless(request()->routeIs('queue.history'))
            <section id="carousel-management" class="panel admin-section">
                <div class="admin-section-head">
                    <div>
                        <h2 class="panel-title">Kelola carousel promosi</h2>
                        <p>Gunakan gambar lanskap rasio 16:9 (disarankan 1920 x 1080 px) agar tampil pas di carousel. Rasio lain tetap utuh, tetapi bisa menyisakan ruang kosong. Format JPG, PNG, atau WebP; ukuran maksimal 2 MB.</p>
                    </div>
                </div>

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

                <form action="{{ route('queue.ads.store') }}" method="POST" enctype="multipart/form-data" class="ad-form row g-3 mb-3">
                    @csrf
                    <div class="col-md-6">
                        <label for="ad-title" class="form-label">Judul iklan</label>
                        <input id="ad-title" name="title" type="text" maxlength="120" class="form-control" value="{{ old('title') }}" placeholder="Contoh: Jadwal pameran museum" required>
                    </div>
                    <div class="col-md-6">
                        <label for="ad-image" class="form-label">Gambar iklan</label>
                        <input id="ad-image" name="image" type="file" accept=".jpg,.jpeg,.jpe,.jfif,.png,.webp,image/jpeg,image/png,image/webp" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label for="ad-description" class="form-label">Keterangan (opsional)</label>
                        <textarea id="ad-description" name="description" rows="2" maxlength="500" class="form-control" placeholder="Tulis keterangan singkat untuk mendampingi gambar promosi...">{{ old('description') }}</textarea>
                    </div>
                    <div class="col-sm-4 col-md-3">
                        <label for="ad-duration" class="form-label">Durasi tampil (detik)</label>
                        <input id="ad-duration" name="duration_seconds" type="number" min="3" max="60" class="form-control" value="{{ old('duration_seconds', 8) }}" required>
                    </div>
                    <div class="col-sm-4 col-md-3">
                        <label for="ad-order" class="form-label">Urutan tampil</label>
                        <input id="ad-order" name="sort_order" type="number" min="0" max="10000" class="form-control" value="{{ old('sort_order') }}" placeholder="Otomatis jika kosong">
                    </div>
                    <div class="col-sm-4 col-md-3 d-flex align-items-end">
                        <div class="form-check mb-2">
                            <input id="ad-active" name="is_active" type="checkbox" value="1" class="form-check-input" @checked(old('is_active', '1'))>
                            <label for="ad-active" class="form-check-label">Langsung aktifkan</label>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-accent fw-bold admin-ad-submit">+ Tambah iklan</button>
                    </div>
                </form>

                <div class="panel-heading mb-2">
                    <h3 class="panel-title">Daftar iklan terpasang</h3>
                    <span class="eyebrow">{{ $ads->count() }} iklan</span>
                </div>
                @forelse($ads as $ad)
                <article class="admin-ad-item">
                    <img src="{{ asset('storage/'.$ad->image_path) }}" alt="{{ $ad->title }}">
                    <div class="admin-ad-copy">
                        <strong>{{ $ad->title }}</strong>
                        <span>{{ $ad->description ?: 'Tanpa keterangan' }}</span>
                        <span>{{ $ad->duration_seconds }} detik · Urutan {{ $ad->sort_order }}</span>
                    </div>
                    <div class="admin-ad-actions">
                        <span class="status-pill {{ $ad->is_active ? 'is-called' : '' }}">{{ $ad->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        <form action="{{ route('queue.ads.toggle', $ad) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline-secondary btn-sm">{{ $ad->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                        </form>
                        <form action="{{ route('queue.ads.destroy', $ad) }}" method="POST" onsubmit="return confirm('Hapus iklan ini beserta gambarnya?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">Hapus</button>
                        </form>
                    </div>
                </article>
                @empty
                <p class="text-body-secondary small mb-0">Belum ada iklan aktif. Tambahkan gambar melalui formulir di atas.</p>
                @endforelse
            </section>
            @endunless

            <section id="queue-list" class="panel admin-section">
                <div class="admin-section-head">
                    <div>
                        <h2 class="panel-title">
                            @if(request()->routeIs('queue.history'))
                            Riwayat antrean · 30 hari terakhir
                            @else
                            Daftar antrean hari ini
                            @endif
                        </h2>
                        <p>{{ $queues->count() }} antrean tercatat</p>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        @if(request()->routeIs('queue.history'))
                        <a href="{{ route('queue.admin') }}" class="btn btn-outline-secondary btn-sm">Kembali ke hari ini</a>
                        <a href="{{ route('queue.history.export') }}" class="btn btn-outline-accent btn-sm">Unduh Excel</a>
                        @else
                        <a href="{{ route('queue.history') }}" class="btn btn-outline-secondary btn-sm">Riwayat 30 hari</a>
                        <button id="btn-delete-all" class="btn btn-outline-danger btn-sm">Hapus antrean hari ini</button>
                        @endif
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table admin-table">
                        <thead>
                            <tr>
                                <th>No. antrean</th>
                                <th>Nama perwakilan</th>
                                <th>Instansi</th>
                                <th>Kategori</th>
                                <th>Judul film</th>
                                <th>Sesi</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="admin-queue-table">
                            @forelse($queues as $queue)
                            <tr id="row-{{ $queue->id }}">
                                <td class="queue-number-cell">{{ $queue->queue_number }}</td>
                                <td>{{ $queue->visitor_name }}</td>
                                <td>{{ $queue->institution_name ?? '—' }}</td>
                                <td>{{ $queue->visitor_type === 'group' ? 'Rombongan' : 'Individu' }}</td>
                                <td>{{ $queue->movie_title }}</td>
                                <td>{{ $queue->session_time }}</td>
                                <td>{{ $queue->created_at->copy()->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</td>
                                <td><span class="status-pill {{ $queue->status === 'called' ? 'is-called' : ($queue->status === 'waiting' ? 'is-waiting' : '') }}">{{ ucfirst($queue->status) }}</span></td>
                                <td><button class="btn btn-outline-danger btn-sm btn-delete" data-id="{{ $queue->id }}">Hapus</button></td>
                            </tr>
                            @empty
                            <tr><td colspan="9" class="text-center text-body-secondary py-4">Belum ada data antrean.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <script>
        const isHistoryPage = {{ request()->routeIs('queue.history') ? 'true' : 'false' }};
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

        const updateStats = (stats) => {
            document.getElementById('stats-today-total').textContent = stats.today.total;
            document.getElementById('stats-today-individual').textContent = stats.today.individual;
            document.getElementById('stats-today-group').textContent = stats.today.group;
            document.getElementById('stats-month-total').textContent = stats.month.total;
            document.getElementById('stats-month-individual').textContent = stats.month.individual;
            document.getElementById('stats-month-group').textContent = stats.month.group;
        };

        const appendCell = (row, value, className = 'p-3') => {
            const cell = document.createElement('td');
            cell.className = className;
            cell.textContent = value ?? '—';
            row.append(cell);
            return cell;
        };

        const renderQueues = (queues) => {
            const table = document.getElementById('admin-queue-table');
            table.replaceChildren();

            if (queues.length === 0) {
                const row = document.createElement('tr');
                const cell = appendCell(row, 'Belum ada data antrean.', 'text-center text-body-secondary py-4');
                cell.colSpan = 9;
                table.append(row);
                return;
            }

            const dateFormatter = new Intl.DateTimeFormat('id-ID', {
                timeZone: 'Asia/Jakarta',
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            queues.forEach((queue) => {
                const row = document.createElement('tr');
                row.id = `row-${queue.id}`;
                appendCell(row, queue.queue_number, 'queue-number-cell');
                appendCell(row, queue.visitor_name);
                appendCell(row, queue.institution_name || '—');
                appendCell(row, queue.visitor_type === 'group' ? 'Rombongan' : 'Individu');
                appendCell(row, queue.movie_title);
                appendCell(row, queue.session_time);
                appendCell(row, dateFormatter.format(new Date(queue.created_at)));

                const statusCell = document.createElement('td');
                statusCell.className = '';
                const badge = document.createElement('span');
                badge.className = `status-pill ${queue.status === 'waiting' ? 'is-waiting' : (queue.status === 'called' ? 'is-called' : '')}`;
                badge.textContent = queue.status.charAt(0).toUpperCase() + queue.status.slice(1);
                statusCell.append(badge);
                row.append(statusCell);

                const actionCell = document.createElement('td');
                actionCell.className = '';
                const deleteButton = document.createElement('button');
                deleteButton.className = 'btn btn-outline-danger btn-sm btn-delete';
                deleteButton.dataset.id = queue.id;
                deleteButton.textContent = 'Hapus';
                actionCell.append(deleteButton);
                row.append(actionCell);
                table.append(row);
            });
        };

        const refreshAdminData = async () => {
            const response = await fetch("{{ route('queue.admin.data') }}", {
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) throw new Error('Gagal memperbarui data admin.');

            const data = await response.json();
            document.getElementById('active-name').textContent =
                data.currentQueue?.institution_name || data.currentQueue?.visitor_name || '---';
            document.getElementById('active-number').textContent = data.currentQueue?.queue_number ?? '---';
            updateStats(data.stats);
            if (!isHistoryPage) renderQueues(data.queues);
        };

        document.addEventListener('click', async (event) => {
            const nextButton = event.target.closest('#btn-next');
            const deleteAllButton = event.target.closest('#btn-delete-all');
            const deleteButton = event.target.closest('.btn-delete');

            try {
                if (nextButton) {
                    const response = await fetch("{{ route('queue.call') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            'Accept': 'application/json'
                        }
                    });
                    const result = await response.json();
                    if (!response.ok) throw new Error(result.message || 'Gagal memanggil antrean.');
                    if (!result.success) {
                        alert(result.message);
                        return;
                    }
                    await refreshAdminData();
                }

                if (deleteAllButton && confirm('Hapus semua antrean hari ini? Riwayat hari sebelumnya tetap tersimpan.')) {
                    const response = await fetch("{{ route('queue.destroyAll') }}", {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            'Accept': 'application/json'
                        }
                    });
                    if (!response.ok) throw new Error('Gagal menghapus antrean hari ini.');
                    await refreshAdminData();
                }

                if (deleteButton && confirm('Hapus data antrean ini?')) {
                    const response = await fetch(`{{ url('/antrean/delete') }}/${deleteButton.dataset.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            'Accept': 'application/json'
                        }
                    });
                    if (!response.ok) throw new Error('Gagal menghapus antrean.');
                    if (isHistoryPage) deleteButton.closest('tr').remove();
                    else await refreshAdminData();
                }
            } catch (error) {
                console.error(error);
                alert(error.message);
            }
        });

        updateClock();
        setInterval(updateClock, 1000);
        if (!isHistoryPage) {
            refreshAdminData().catch((error) => console.error(error));
            setInterval(() => refreshAdminData().catch((error) => console.error(error)), 2000);
        }
    </script>
</body>

</html>
