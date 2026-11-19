<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Operator Antrean Museum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/queue-theme.css') }}" rel="stylesheet">
</head>

<body class="bg-dark text-white min-vh-100 p-3 p-md-4">
    <main class="container">
        <header class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-3 border-bottom border-secondary">
            <div>
                <h1 class="h4 fw-bold m-0 text-warning">Dashboard Petugas Teater</h1>
                <time id="live-date" class="small text-white-50"></time>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <span id="live-clock" class="fw-bold font-monospace text-warning me-2">--:--:-- WIB</span>
                <a href="{{ route('queue.index') }}" target="_blank" class="btn btn-outline-info btn-sm">Layar TV</a>
                <a href="{{ route('queue.kiosk') }}" target="_blank" class="btn btn-outline-info btn-sm">Kiosk</a>
                @unless(request()->routeIs('queue.history'))
                <a href="#carousel-management" class="btn btn-outline-warning btn-sm">Kelola Carousel</a>
                @endunless
                @include('queue.partials.theme-toggle')
            </div>
        </header>

        <section class="row g-3 mb-4" aria-label="Statistik pengunjung">
            <div class="col-md-6">
                <div class="card bg-secondary bg-opacity-25 border border-secondary rounded-4 p-3 h-100">
                    <h2 class="h6 text-warning fw-bold">Hari Ini</h2>
                    <div class="fs-4 fw-bold"><span id="stats-today-total">{{ $stats['today']['total'] }}</span> pengunjung tercatat</div>
                    <div class="small text-white-50">
                        Individu: <span id="stats-today-individual">{{ $stats['today']['individual'] }}</span>
                        <span class="mx-1">|</span>
                        Rombongan: <span id="stats-today-group">{{ $stats['today']['group'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-secondary bg-opacity-25 border border-secondary rounded-4 p-3 h-100">
                    <h2 class="h6 text-warning fw-bold">Bulan Ini</h2>
                    <div class="fs-4 fw-bold"><span id="stats-month-total">{{ $stats['month']['total'] }}</span> pengunjung tercatat</div>
                    <div class="small text-white-50">
                        Individu: <span id="stats-month-individual">{{ $stats['month']['individual'] }}</span>
                        <span class="mx-1">|</span>
                        Rombongan: <span id="stats-month-group">{{ $stats['month']['group'] }}</span>
                    </div>
                </div>
            </div>
            <p class="col-12 small text-white-50 mt-0 mb-0">Rombongan dihitung dari satu perwakilan yang mendaftar, bukan jumlah anggota rombongan.</p>
        </section>

        @unless(request()->routeIs('queue.history'))
        <section id="carousel-management" class="card bg-secondary bg-opacity-25 border border-secondary rounded-4 p-3 p-md-4 mb-4 shadow">
            <div class="mb-3">
                <h2 class="h5 fw-bold text-warning mb-1">Kelola Carousel Promosi</h2>
                <p class="small text-white-50 mb-0">Unggah gambar promosi untuk ditampilkan di sisi kanan layar antrean. Format JPEG/JPG, PNG, atau WebP; maksimal 2 MB.</p>
            </div>

            @if(session('success'))
            <div class="alert alert-success" role="status">{{ session('success') }}</div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('queue.ads.store') }}" method="POST" enctype="multipart/form-data" class="row g-3 mb-4">
                @csrf
                <div class="col-md-6">
                    <label for="ad-title" class="form-label">Judul iklan</label>
                    <input id="ad-title" name="title" type="text" maxlength="120" class="form-control" value="{{ old('title') }}" required>
                </div>
                <div class="col-md-6">
                    <label for="ad-image" class="form-label">Gambar iklan</label>
                    <input id="ad-image" name="image" type="file" accept=".jpg,.jpeg,.jpe,.jfif,.png,.webp,image/jpeg,image/png,image/webp" class="form-control" required>
                </div>
                <div class="col-12">
                    <label for="ad-description" class="form-label">Keterangan (opsional)</label>
                    <textarea id="ad-description" name="description" rows="2" maxlength="500" class="form-control">{{ old('description') }}</textarea>
                </div>
                <div class="col-md-3">
                    <label for="ad-duration" class="form-label">Durasi (detik)</label>
                    <input id="ad-duration" name="duration_seconds" type="number" min="3" max="60" class="form-control" value="{{ old('duration_seconds', 8) }}" required>
                </div>
                <div class="col-md-3">
                    <label for="ad-order" class="form-label">Urutan tampil</label>
                    <input id="ad-order" name="sort_order" type="number" min="0" max="10000" class="form-control" value="{{ old('sort_order') }}" placeholder="Otomatis jika kosong">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check mb-2">
                        <input id="ad-active" name="is_active" type="checkbox" value="1" class="form-check-input" @checked(old('is_active', '1'))>
                        <label for="ad-active" class="form-check-label">Langsung aktifkan</label>
                    </div>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-warning fw-bold w-100">Tambah Iklan</button>
                </div>
            </form>

            <h3 class="h6 fw-bold text-light mb-3">Daftar iklan ({{ $ads->count() }})</h3>
            @forelse($ads as $ad)
            <article class="d-flex flex-column flex-md-row align-items-md-center gap-3 border border-secondary rounded-3 p-3 mb-2">
                <img src="{{ asset('storage/'.$ad->image_path) }}" alt="{{ $ad->title }}" class="rounded object-fit-cover" style="width: 160px; height: 90px;">
                <div class="flex-grow-1">
                    <div class="fw-bold">{{ $ad->title }}</div>
                    <div class="small text-white-50">{{ $ad->description ?: 'Tanpa keterangan' }}</div>
                    <div class="small text-white-50">Urutan {{ $ad->sort_order }} · {{ $ad->duration_seconds }} detik</div>
                </div>
                <span class="badge {{ $ad->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $ad->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                <form action="{{ route('queue.ads.toggle', $ad) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-outline-warning btn-sm">{{ $ad->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                </form>
                <form action="{{ route('queue.ads.destroy', $ad) }}" method="POST" onsubmit="return confirm('Hapus iklan ini beserta gambarnya?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm">Hapus</button>
                </form>
            </article>
            @empty
            <p class="text-white-50 mb-0">Belum ada iklan. Tambahkan gambar melalui formulir di atas.</p>
            @endforelse
        </section>
        @endunless

        <section class="card bg-secondary bg-opacity-25 border border-secondary rounded-4 p-3 p-md-4 shadow">
            <div class="row g-3 align-items-center mb-4">
                <div class="col-md-4">
                    <div class="card bg-dark border border-warning p-3 rounded-4 text-center h-100">
                        <h2 class="small text-warning fw-semibold mb-2">Sedang Dipanggil</h2>
                        <div id="active-name" class="h4 fw-bold text-warning">{{ $currentQueue->institution_name ?? $currentQueue->visitor_name ?? '---' }}</div>
                        <div class="h5 fw-bold">Nomor: <span id="active-number" class="text-warning">{{ $currentQueue->queue_number ?? '---' }}</span></div>
                    </div>
                </div>
                <div class="col-md-8">
                    @unless(request()->routeIs('queue.history'))
                    <button id="btn-next" class="btn btn-success fw-bold py-4 rounded-4 fs-5 shadow-sm w-100">
                        Panggil Antrean Selanjutnya 📢
                    </button>
                    @endunless
                </div>
            </div>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                <h2 class="h6 fw-bold text-light m-0">
                    @if(request()->routeIs('queue.history'))
                    📚 Riwayat Antrean (30 Hari Terakhir)
                    @else
                    📋 Daftar Antrean Hari Ini
                    @endif
                </h2>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    @if(request()->routeIs('queue.history'))
                    <a href="{{ route('queue.admin') }}" class="btn btn-outline-warning btn-sm">Kembali ke Hari Ini</a>
                    <a href="{{ route('queue.history.export') }}" class="btn btn-success btn-sm">Unduh Excel (.xlsx)</a>
                    @else
                    <a href="{{ route('queue.history') }}" class="btn btn-outline-info btn-sm">Riwayat 30 Hari</a>
                    <button id="btn-delete-all" class="btn btn-outline-danger btn-sm">Hapus Antrean Hari Ini</button>
                    @endif
                </div>
            </div>

            <div class="table-responsive rounded-3 border border-secondary">
                <table class="table table-striped table-hover m-0 align-middle">
                    <thead class="table-secondary">
                        <tr>
                            <th class="p-3">No. Antrean</th>
                            <th class="p-3">Nama Perwakilan</th>
                            <th class="p-3">Instansi</th>
                            <th class="p-3">Kategori</th>
                            <th class="p-3">Judul Film</th>
                            <th class="p-3">Sesi</th>
                            <th class="p-3">Tanggal</th>
                            <th class="p-3">Status</th>
                            <th class="p-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="small" id="admin-queue-table">
                        @forelse($queues as $queue)
                        <tr id="row-{{ $queue->id }}">
                            <td class="p-3 fw-bold text-warning">{{ $queue->queue_number }}</td>
                            <td class="p-3">{{ $queue->visitor_name }}</td>
                            <td class="p-3">{{ $queue->institution_name ?? '—' }}</td>
                            <td class="p-3">{{ $queue->visitor_type === 'group' ? 'Rombongan' : 'Individu' }}</td>
                            <td class="p-3">{{ $queue->movie_title }}</td>
                            <td class="p-3">{{ $queue->session_time }}</td>
                            <td class="p-3">{{ $queue->created_at->copy()->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</td>
                            <td class="p-3"><span class="badge {{ $queue->status === 'waiting' ? 'bg-warning text-dark' : ($queue->status === 'called' ? 'bg-success' : 'bg-secondary') }}">{{ ucfirst($queue->status) }}</span></td>
                            <td class="p-3 text-center">
                                <button class="btn btn-outline-danger btn-sm btn-delete" data-id="{{ $queue->id }}">Hapus</button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="text-center text-body-secondary py-4">Belum ada data antrean.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>

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
                appendCell(row, queue.queue_number, 'p-3 fw-bold text-warning');
                appendCell(row, queue.visitor_name);
                appendCell(row, queue.institution_name || '—');
                appendCell(row, queue.visitor_type === 'group' ? 'Rombongan' : 'Individu');
                appendCell(row, queue.movie_title);
                appendCell(row, queue.session_time);
                appendCell(row, dateFormatter.format(new Date(queue.created_at)));

                const statusCell = document.createElement('td');
                statusCell.className = 'p-3';
                const badge = document.createElement('span');
                badge.className = `badge ${queue.status === 'waiting' ? 'bg-warning text-dark' : (queue.status === 'called' ? 'bg-success' : 'bg-secondary')}`;
                badge.textContent = queue.status.charAt(0).toUpperCase() + queue.status.slice(1);
                statusCell.append(badge);
                row.append(statusCell);

                const actionCell = document.createElement('td');
                actionCell.className = 'p-3 text-center';
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
            const response = await fetch("{{ route('queue.data') }}", {
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
