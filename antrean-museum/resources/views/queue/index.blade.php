<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor Antrean Teater Museum</title>
    <!-- CSS Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .ticker-track {
            animation: ticker-right 28s linear infinite;
        }

        .ticker-group {
            display: flex;
            flex-shrink: 0;
            align-items: center;
            gap: 3rem;
            padding-right: 3rem;
        }

        @keyframes ticker-right {
            from {
                transform: translateX(-50%);
            }

            to {
                transform: translateX(0);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .ticker-track {
                animation-duration: 56s;
            }
        }
    </style>
</head>

<body class="bg-dark text-white min-vh-100 d-flex flex-column justify-content-between p-3 overflow-x-hidden">

    <!-- Header -->
    <header class="d-flex justify-content-between align-items-center py-1 border-bottom border-secondary pb-2 mb-3">
        <div style="width: 200px;"></div>
        <div class="text-center">
            <h1 class="h4 fw-bold text-warning text-uppercase tracking-wider m-0">Teater Film Dokumenter Museum</h1>
            <p class="text-white-50 small m-0">Silakan perhatikan nomor antrean Anda di layar</p>
        </div>
        <div class="text-end" style="width: 200px;">
            <time id="live-clock" class="d-inline-block fw-bold font-monospace text-warning fs-4 m-0">--:--:-- WIB</time>
        </div>
    </header>

    <!-- Main Content Area -->
    <div class="row g-3 align-items-center my-auto">
        <!-- Kotak Panggilan Utama -->
        <div class="col-md-6">
            <div class="card bg-secondary bg-opacity-25 border-3 border-warning rounded-4 py-5 px-4 text-center shadow-lg text-white">
                <h2 class="h5 fw-semibold text-white-50 mb-3">NOMOR DIPANGGIL</h2>
                <div id="current-queue" class="display-1 fw-black text-warning my-3" style="font-size: 6rem;">
                    {{ $currentQueue->queue_number ?? '-' }}
                </div>
                <p id="movie-title" class="h5 text-light fw-medium mb-3">
                    {{ $currentQueue->movie_title ?? 'Menunggu Panggilan...' }}
                </p>
                <div>
                    <span id="session-time" class="badge bg-warning bg-opacity-25 text-warning px-4 py-2 rounded-pill fs-6">
                        Sesi: {{ $currentQueue->session_time ?? '-' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Daftar Antrean Berikutnya -->
        <div class="col-md-6">
            <div class="card bg-secondary bg-opacity-25 border border-secondary rounded-4 p-4 text-white">
                <h3 class="h6 fw-bold text-light mb-3 pb-2 border-bottom border-secondary">Antrean Selanjutnya</h3>
                <div class="vstack gap-2 overflow-y-auto" id="waiting-list" style="max-height: 280px;">
                    @forelse($waitingQueues as $queue)
                    <div class="d-flex justify-content-between align-items-center bg-dark bg-opacity-50 px-3 py-3 rounded-3 border border-secondary border-opacity-50">
                        <span class="h4 fw-bold text-warning m-0">{{ $queue->queue_number }}</span>
                        <span class="small text-white-50">{{ $queue->movie_title }}</span>
                        <span class="badge bg-secondary text-light">{{ $queue->session_time }}</span>
                    </div>
                    @empty
                    <p class="text-white-50 text-center py-4 m-0">Belum ada antrean menunggu.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Bagian Bawah: Ticker & Footer Terkunci Rapi -->
    <div class="mt-3">
        <!-- Ticker / Running Text -->
        <section class="overflow-hidden border-top border-bottom border-warning border-opacity-25 bg-secondary bg-opacity-15 py-1 mb-2" aria-label="Informasi museum">
            <div class="ticker-track d-flex w-max text-sm fw-medium text-warning-emphasis">
                <div class="ticker-group">
                    <span class="text-light">Mohon siapkan nomor antrean saat dipanggil.</span>
                    <span aria-hidden="true" class="text-warning">&#9670;</span>
                    <span class="text-light">Pastikan sesi pemutaran sesuai dengan tiket Anda.</span>
                    <span aria-hidden="true" class="text-warning">&#9670;</span>
                    <span class="text-light">Terima kasih telah berkunjung ke Museum Pusat Informasi &amp; Sejarah.</span>
                </div>
                <div class="ticker-group" aria-hidden="true">
                    <span class="text-light">Mohon siapkan nomor antrean saat dipanggil.</span>
                    <span class="text-warning">&#9670;</span>
                    <span class="text-light">Pastikan sesi pemutaran sesuai dengan tiket Anda.</span>
                    <span class="text-warning">&#9670;</span>
                    <span class="text-light">Terima kasih telah berkunjung ke Museum Pusat Informasi &amp; Sejarah.</span>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="text-center text-white-50" style="font-size: 0.75rem;">
            Museum Pusat Informasi & Sejarah &copy; 2026
        </footer>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script Jam & Sinkronisasi AJAX -->
    <script>
        const monitorClock = document.getElementById('live-clock');
        const monitorClockFormatter = new Intl.DateTimeFormat('id-ID', {
            timeZone: 'Asia/Jakarta',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        });

        const updateMonitorClock = () => {
            monitorClock.textContent = `${monitorClockFormatter.format(new Date())} WIB`;
        };

        const updateMonitorData = async () => {
            try {
                const response = await fetch("{{ route('queue.data') }}", {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) return;

                const data = await response.json();
                const currentQueue = data.currentQueue;

                document.getElementById('current-queue').textContent = currentQueue?.queue_number ?? '-';
                document.getElementById('movie-title').textContent = currentQueue?.movie_title ?? 'Menunggu Panggilan...';
                document.getElementById('session-time').textContent = `Sesi: ${currentQueue?.session_time ?? '-'}`;

                const waitingList = document.getElementById('waiting-list');
                waitingList.replaceChildren();

                if (!data.waitingQueues || data.waitingQueues.length === 0) {
                    const emptyMessage = document.createElement('p');
                    emptyMessage.className = 'text-white-50 text-center py-4 m-0';
                    emptyMessage.textContent = 'Belum ada antrean menunggu.';
                    waitingList.append(emptyMessage);
                    return;
                }

                data.waitingQueues.forEach((queue) => {
                    const row = document.createElement('div');
                    row.className = 'd-flex justify-content-between align-items-center bg-dark bg-opacity-50 px-3 py-3 rounded-3 border border-secondary border-opacity-50';

                    const number = document.createElement('span');
                    number.className = 'h4 fw-bold text-warning m-0';
                    number.textContent = queue.queue_number;

                    const title = document.createElement('span');
                    title.className = 'small text-white-50';
                    title.textContent = queue.movie_title;

                    const session = document.createElement('span');
                    session.className = 'badge bg-secondary text-light';
                    session.textContent = queue.session_time;

                    row.append(number, title, session);
                    waitingList.append(row);
                });
            } catch {}
        };

        updateMonitorClock();
        updateMonitorData();
        setInterval(updateMonitorClock, 1000);
        setInterval(updateMonitorData, 5000);
    </script>
</body>

</html>