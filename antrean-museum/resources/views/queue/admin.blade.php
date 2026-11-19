<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Operator Antrean Museum</title>
    <!-- CSS Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-dark text-white min-vh-100 p-4 d-flex align-items-center justify-content-center">
    <div class="card bg-secondary bg-opacity-25 border border-secondary p-4 rounded-4 shadow-lg text-white w-100" style="max-width: 950px;">
        <div class="card-body">
            <!-- Header -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-3 border-bottom border-secondary">
                <h1 class="h4 fw-bold m-0 text-warning">Dashboard Petugas Teater</h1>
                <div class="d-flex align-items-center justify-content-between justify-content-md-end gap-3">
                    <span id="live-clock" class="h5 m-0 fw-bold text-light font-monospace">--:--:-- WIB</span>
                    <a href="{{ route('queue.index') }}" target="_blank" class="btn btn-outline-info btn-sm px-3">Buka Layar TV</a>
                </div>
            </div>

            <!-- Panel Panggilan -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card bg-dark border border-warning p-3 rounded-4 text-center h-100 justify-content-center">
                        <h3 class="small text-warning fw-semibold mb-2">Sedang Dipanggil</h3>
                        <div id="active-number" class="display-4 fw-black text-warning m-0">
                            {{ $currentQueue->queue_number ?? '---' }}
                        </div>
                    </div>
                </div>
                <div class="col-md-8 d-flex flex-column justify-content-center">
                    <button id="btn-next" class="btn btn-success fw-bold py-4 rounded-4 fs-5 shadow-sm w-100">
                        Panggil Antrean Selanjutnya 📢
                    </button>
                </div>
            </div>

            <!-- Bagian Judul Tabel & Tombol Navigasi Riwayat -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                <div>
                    <h3 class="h6 fw-bold text-light m-0">
                        @if(request()->routeIs('queue.history'))
                        📚 Seluruh Riwayat Antrean (Arsip)
                        @else
                        📋 Daftar Antrean Hari Ini
                        @endif
                    </h3>
                </div>

                <div class="d-flex align-items-center gap-2">
                    @if(request()->routeIs('queue.history'))
                    <a href="{{ route('queue.admin') }}" class="btn btn-outline-warning btn-sm px-3">
                        ⬅️ Kembali ke Hari Ini
                    </a>
                    @else
                    <a href="{{ route('queue.history') }}" class="btn btn-outline-info btn-sm px-3">
                        📜 Lihat Semua Riwayat
                    </a>
                    <button id="btn-delete-all" class="btn btn-outline-danger btn-sm px-3 py-1">
                        ⚠️ Hapus Semua Hari Ini
                    </button>
                    @endif
                </div>
            </div>

            <div class="table-responsive rounded-3 overflow-y-auto border border-secondary" style="max-height: 350px;">
                <table class="table table-dark table-striped table-hover m-0 align-middle">
                    <thead class="table-secondary text-dark position-sticky top-0" style="z-index: 1;">
                        <tr>
                            <th class="p-3">No. Antrean</th>
                            <th class="p-3">Nama Pengunjung</th>
                            <th class="p-3">Judul Film</th>
                            <th class="p-3">Sesi</th>
                            <th class="p-3">Status</th>
                            <th class="p-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="small" id="admin-queue-table">
                        @foreach($queues as $q)
                        <tr id="row-{{ $q->id }}">
                            <td class="p-3 fw-bold text-warning">{{ $q->queue_number }}</td>
                            <td class="p-3">{{ $q->visitor_name }}</td>
                            <td class="p-3">{{ $q->movie_title }}</td>
                            <td class="p-3">{{ $q->session_time }}</td>
                            <td class="p-3">
                                <span class="badge 
                                    @if($q->status == 'waiting') bg-warning text-dark
                                    @elseif($q->status == 'called') bg-success
                                    @else bg-secondary @endif">
                                    {{ ucfirst($q->status) }}
                                </span>
                            </td>
                            <td class="p-3 text-center">
                                <button class="btn btn-outline-danger btn-sm px-2 py-1 btn-delete" data-id="{{ $q->id }}">
                                    🗑️ Hapus
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Bootstrap JS Bundle -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

            <!-- Script AJAX & Auto-Sync -->
            <script>
                const operatorClock = document.getElementById('live-clock');
                const updateOperatorClock = () => {
                    const time = new Intl.DateTimeFormat('id-ID', {
                        timeZone: 'Asia/Jakarta',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                        hour12: false
                    }).format(new Date());

                    operatorClock.textContent = `${time} WIB`;
                };

                updateOperatorClock();
                setInterval(updateOperatorClock, 1000);

                // Fungsi untuk memperbarui data admin secara otomatis
                const refreshAdminData = async () => {
                    try {
                        const response = await fetch("{{ route('queue.data') }}", {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        if (!response.ok) return;

                        const data = await response.json();

                        // 1. Update Nomor Aktif
                        const activeNumberEl = document.getElementById('active-number');
                        activeNumberEl.textContent = data.currentQueue?.queue_number ?? '---';

                        // 2. Update Tabel Antrean
                        const tableBody = document.getElementById('admin-queue-table');
                        tableBody.replaceChildren();

                        if (!data.queues || data.queues.length === 0) {
                            const emptyRow = document.createElement('tr');
                            emptyRow.innerHTML = `<td colspan="6" class="text-center text-white-50 py-3">Belum ada antrean hari ini.</td>`;
                            tableBody.append(emptyRow);
                            return;
                        }

                        data.queues.forEach((q) => {
                            const row = document.createElement('tr');
                            row.id = `row-${q.id}`;

                            let badgeClass = 'bg-secondary';
                            let statusText = q.status.charAt(0).toUpperCase() + q.status.slice(1);
                            if (q.status === 'waiting') {
                                badgeClass = 'bg-warning text-dark';
                            } else if (q.status === 'called') {
                                badgeClass = 'bg-success';
                            }

                            row.innerHTML = `
                        <td class="p-3 fw-bold text-warning">${q.queue_number}</td>
                        <td class="p-3">${q.visitor_name}</td>
                        <td class="p-3">${q.movie_title}</td>
                        <td class="p-3">${q.session_time}</td>
                        <td class="p-3">
                            <span class="badge ${badgeClass}">${statusText}</span>
                        </td>
                        <td class="p-3 text-center">
                            <button class="btn btn-outline-danger btn-sm px-2 py-1 btn-delete" data-id="${q.id}">
                                🗑️ Hapus
                            </button>
                        </td>
                    `;
                            tableBody.append(row);
                        });
                    } catch (err) {
                        console.error("Gagal memperbarui data admin:", err);
                    }
                };

                // Tombol Panggil Antrean Selanjutnya
                $('#btn-next').click(function() {
                    $.ajax({
                        url: "{{ route('queue.call') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                refreshAdminData();
                            } else {
                                alert(response.message);
                            }
                        }
                    });
                });

                // Tombol Hapus Satuan
                $(document).on('click', '.btn-delete', function() {
                    const queueId = $(this).data('id');

                    if (confirm('Apakah Anda yakin ingin menghapus nomor antrean ini?')) {
                        $.ajax({
                            url: "{{ url('/antrean/delete') }}/" + queueId,
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                if (response.success) {
                                    $(`#row-${queueId}`).fadeOut(300, function() {
                                        $(this).remove();
                                    });
                                    refreshAdminData();
                                } else {
                                    alert('Gagal menghapus antrean.');
                                }
                            }
                        });
                    }
                });

                // Tombol Hapus Semua Antrean
                $('#btn-delete-all').click(function() {
                    if (confirm('PERHATIAN: Apakah Anda yakin ingin menghapus SEMUA daftar antrean? Tindakan ini tidak dapat dibatalkan!')) {
                        $.ajax({
                            url: "{{ route('queue.destroyAll') }}",
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                if (response.success) {
                                    refreshAdminData();
                                } else {
                                    alert('Gagal mengosongkan antrean.');
                                }
                            }
                        });
                    }
                });

                // Cek apakah saat ini sedang berada di halaman riwayat atau halaman admin biasa
                const isHistoryPage = window.location.pathname.includes('/history');

                // Tombol Hapus Satuan
                $(document).on('click', '.btn-delete', function() {
                    const queueId = $(this).data('id');

                    if (confirm('Apakah Anda yakin ingin menghapus nomor antrean ini?')) {
                        $.ajax({
                            url: "{{ url('/antrean/delete') }}/" + queueId,
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                if (response.success) {
                                    $(`#row-${queueId}`).fadeOut(300, function() {
                                        $(this).remove();
                                    });
                                    if (!isHistoryPage) refreshAdminData();
                                } else {
                                    alert('Gagal menghapus antrean.');
                                }
                            }
                        });
                    }
                });

                // Tombol Hapus Semua Antrean
                $('#btn-delete-all').click(function() {
                    if (confirm('PERHATIAN: Apakah Anda yakin ingin menghapus SEMUA daftar antrean hari ini?')) {
                        $.ajax({
                            url: "{{ route('queue.destroyAll') }}",
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                if (response.success) {
                                    refreshAdminData();
                                } else {
                                    alert('Gagal mengosongkan antrean.');
                                }
                            }
                        });
                    }
                });

                // Auto-sync HANYA berjalan jika BUKAN di halaman riwayat
                if (!isHistoryPage) {
                    setInterval(refreshAdminData, 2000);
                }
            </script>
</body>

</html>