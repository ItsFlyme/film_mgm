<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor Antrean Teater Museum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/queue-theme.css') }}" rel="stylesheet">
    <style>
        .ticker-track {
            animation: ticker-left 28s linear infinite;
            width: max-content;
        }

        .ticker-group {
            display: flex;
            flex-shrink: 0;
            align-items: center;
            gap: 3rem;
            padding-right: 3rem;
        }

        @keyframes ticker-left {
            from {
                transform: translateX(0);
            }

            to {
                transform: translateX(-50%);
            }
        }

        .ad-frame {
            aspect-ratio: 16 / 9;
            min-height: 320px;
            position: relative;
            overflow: hidden;
            border-radius: 1rem;
            background: #101820;
        }

        .ad-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .ad-caption {
            position: absolute;
            inset: auto 0 0;
            padding: 3rem 1.5rem 1.5rem;
            color: #fff;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.88));
        }

        @media (prefers-reduced-motion: reduce) {
            .ticker-track {
                animation-duration: 56s;
            }
        }
    </style>
</head>

<body class="bg-dark text-white min-vh-100 d-flex flex-column justify-content-between p-3 overflow-x-hidden">
    <header class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-2 border-bottom border-secondary mb-3">
        <nav class="d-flex flex-wrap align-items-center gap-2" aria-label="Navigasi halaman">
            <a href="{{ route('queue.index') }}" class="btn btn-outline-info btn-sm" aria-current="page">Layar TV</a>
            <a href="{{ route('queue.kiosk') }}" class="btn btn-outline-info btn-sm">Kiosk</a>
            <a href="{{ route('queue.admin') }}" class="btn btn-outline-info btn-sm">Admin</a>
        </nav>
        <div class="text-center">
            <h1 class="h4 fw-bold text-warning text-uppercase m-0">Teater Film Dokumenter Museum</h1>
            <p class="text-white-50 small m-0">Silakan perhatikan nomor antrean Anda di layar</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="text-end">
                <time id="live-date" class="d-block text-white-50 small"></time>
                <time id="live-clock" class="d-block fw-bold font-monospace text-warning fs-5">--:--:-- WIB</time>
            </div>
            @include('queue.partials.theme-toggle')
        </div>
    </header>

    <main class="row g-3 align-items-stretch my-auto">
        <section class="col-md-5 d-flex flex-column gap-3">
            <div class="card bg-secondary bg-opacity-25 border-3 border-warning rounded-4 p-4 text-center shadow-lg text-white">
                <h2 class="h6 fw-semibold text-white-50 mb-2">SEDANG DIPANGGIL</h2>
                <div id="current-name" class="display-4 fw-bold text-warning my-2">
                    {{ $currentQueue->institution_name ?? $currentQueue->visitor_name ?? 'Menunggu Panggilan...' }}
                </div>
                <div class="h4 fw-bold mb-3">
                    Nomor antrean: <span id="current-queue" class="text-warning">{{ $currentQueue->queue_number ?? '-' }}</span>
                </div>
                <p id="movie-title" class="h6 text-light fw-medium mb-2">{{ $currentQueue->movie_title ?? '-' }}</p>
                <span id="session-time" class="badge bg-warning bg-opacity-25 text-warning px-4 py-2 rounded-pill fs-6">
                    Sesi: {{ $currentQueue->session_time ?? '-' }}
                </span>
            </div>

            <section class="card bg-secondary bg-opacity-25 border border-secondary rounded-4 p-3 text-white">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-secondary">
                    <h2 class="h5 fw-bold text-light m-0">Antrean Menunggu</h2>
                    <span id="waiting-count" class="badge bg-warning text-dark">{{ $waitingQueues->count() }}</span>
                </div>
                <div id="waiting-list" class="vstack gap-2 overflow-y-auto" style="max-height: 260px;" aria-live="polite">
                    @forelse($waitingQueues as $queue)
                    <div class="d-flex justify-content-between align-items-center gap-3 bg-dark bg-opacity-50 px-3 py-3 rounded-3 border border-secondary">
                        <span class="h4 fw-bold text-warning text-nowrap flex-shrink-0 m-0">{{ $queue->queue_number }}</span>
                        <span class="small text-white text-end flex-grow-1">{{ $queue->institution_name ?? $queue->visitor_name }}</span>
                        <span class="badge bg-secondary text-body text-nowrap flex-shrink-0">{{ $queue->session_time }}</span>
                    </div>
                    @empty
                    <p class="text-white-50 text-center py-3 m-0">Belum ada antrean menunggu.</p>
                    @endforelse
                </div>
            </section>
        </section>

        <section class="col-md-7">
            <div class="card bg-secondary bg-opacity-25 border border-secondary rounded-4 p-3 p-md-4 text-white h-100">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-secondary">
                    <h2 class="h5 fw-bold text-light m-0">Informasi &amp; Promosi Museum</h2>
                    <span class="small text-white-50">Geser otomatis</span>
                </div>
                <div id="museum-ads" class="carousel slide my-auto d-none">
                    <div id="ads-indicators" class="carousel-indicators"></div>
                    <div id="ads-inner" class="carousel-inner rounded-4"></div>
                    <button class="carousel-control-prev d-none" type="button" data-bs-target="#museum-ads" data-bs-slide="prev" aria-label="Iklan sebelumnya">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </button>
                    <button class="carousel-control-next d-none" type="button" data-bs-target="#museum-ads" data-bs-slide="next" aria-label="Iklan berikutnya">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </button>
                </div>
                <div id="ads-empty" class="border border-secondary rounded-4 text-center text-white-50 p-5 my-auto">
                    Belum ada iklan aktif. Tambahkan gambar melalui dashboard petugas.
                </div>
            </div>
        </section>
    </main>

    <div class="mt-3">
        <section class="overflow-hidden border-top border-bottom border-warning border-opacity-25 bg-secondary bg-opacity-15 py-2 mb-2" aria-label="Informasi antrean">
            <div class="ticker-track d-flex text-sm fw-medium text-warning">
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
        <footer class="text-center text-white-50 small">
            Museum Pusat Informasi &amp; Sejarah &copy; 2026
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const clock = document.getElementById('live-clock');
        const date = document.getElementById('live-date');
        const waitingList = document.getElementById('waiting-list');
        const adsCarousel = document.getElementById('museum-ads');
        const adsInner = document.getElementById('ads-inner');
        const adsIndicators = document.getElementById('ads-indicators');
        const adsEmpty = document.getElementById('ads-empty');
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
            date.textContent = dateFormatter.format(now);
            clock.textContent = `${clockFormatter.format(now)} WIB`;
        };

        const appendText = (parent, tag, className, value) => {
            const element = document.createElement(tag);
            element.className = className;
            element.textContent = value;
            parent.append(element);
            return element;
        };

        let lastWaitingSignature = null;
        let lastAdsSignature = null;
        const renderWaitingList = (queues) => {
            const signature = JSON.stringify(queues.map((queue) => [
                queue.id,
                queue.queue_number,
                queue.visitor_name,
                queue.institution_name,
                queue.movie_title,
                queue.session_time
            ]));

            if (signature === lastWaitingSignature) return;
            lastWaitingSignature = signature;
            waitingList.replaceChildren();
            document.getElementById('waiting-count').textContent = queues.length;

            if (queues.length === 0) {
                appendText(waitingList, 'p', 'text-white-50 text-center py-3 m-0', 'Belum ada antrean menunggu.');
                return;
            }

            queues.forEach((queue) => {
                const row = document.createElement('div');
                row.className = 'd-flex justify-content-between align-items-center gap-3 bg-dark bg-opacity-50 px-3 py-3 rounded-3 border border-secondary';
                appendText(row, 'span', 'h4 fw-bold text-warning text-nowrap flex-shrink-0 m-0', queue.queue_number);

                const detail = document.createElement('div');
                detail.className = 'small text-end flex-grow-1';
                appendText(detail, 'span', 'd-block text-white', queue.institution_name || queue.visitor_name);
                appendText(detail, 'span', 'd-block text-white-50', queue.movie_title);
                row.append(detail);

                appendText(row, 'span', 'badge bg-secondary text-body text-nowrap flex-shrink-0', queue.session_time);
                waitingList.append(row);
            });
        };

        const updateMonitorData = async () => {
            const response = await fetch("{{ route('queue.data') }}", {
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) throw new Error('Gagal mengambil data antrean.');

            const data = await response.json();
            const currentQueue = data.currentQueue;
            document.getElementById('current-name').textContent =
                currentQueue?.institution_name || currentQueue?.visitor_name || 'Menunggu Panggilan...';
            document.getElementById('current-queue').textContent = currentQueue?.queue_number ?? '-';
            document.getElementById('movie-title').textContent = currentQueue?.movie_title ?? '-';
            document.getElementById('session-time').textContent = `Sesi: ${currentQueue?.session_time ?? '-'}`;
            renderWaitingList(data.waitingQueues);
        };

        const renderAds = (ads) => {
            const signature = JSON.stringify(ads);
            if (signature === lastAdsSignature) return;
            lastAdsSignature = signature;

            bootstrap.Carousel.getInstance(adsCarousel)?.dispose();
            adsInner.replaceChildren();
            adsIndicators.replaceChildren();

            const previousButton = adsCarousel.querySelector('.carousel-control-prev');
            const nextButton = adsCarousel.querySelector('.carousel-control-next');
            const hasMultipleAds = ads.length > 1;
            previousButton.classList.toggle('d-none', !hasMultipleAds);
            nextButton.classList.toggle('d-none', !hasMultipleAds);
            adsCarousel.classList.toggle('d-none', ads.length === 0);
            adsEmpty.classList.toggle('d-none', ads.length > 0);

            ads.forEach((ad, index) => {
                const indicator = document.createElement('button');
                indicator.type = 'button';
                indicator.dataset.bsTarget = '#museum-ads';
                indicator.dataset.bsSlideTo = String(index);
                indicator.setAttribute('aria-label', `Iklan ${index + 1}`);
                if (index === 0) {
                    indicator.classList.add('active');
                    indicator.setAttribute('aria-current', 'true');
                }
                adsIndicators.append(indicator);

                const slide = document.createElement('div');
                slide.className = `carousel-item${index === 0 ? ' active' : ''}`;
                slide.dataset.bsInterval = String(ad.duration_seconds * 1000);

                const frame = document.createElement('div');
                frame.className = 'ad-frame';
                const image = document.createElement('img');
                image.className = 'ad-image';
                image.src = ad.image_url;
                image.alt = ad.title;
                image.loading = index === 0 ? 'eager' : 'lazy';
                frame.append(image);

                const caption = document.createElement('div');
                caption.className = 'ad-caption';
                appendText(caption, 'h3', 'h4 fw-bold mb-1', ad.title);
                if (ad.description) appendText(caption, 'p', 'mb-0', ad.description);
                frame.append(caption);

                slide.append(frame);
                adsInner.append(slide);
            });

            if (ads.length > 0) {
                new bootstrap.Carousel(adsCarousel, {
                    interval: 8000,
                    ride: 'carousel',
                    pause: false,
                    wrap: true
                }).cycle();
            }
        };

        const updateAds = async () => {
            const response = await fetch("{{ route('queue.ads.data') }}", {
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) throw new Error('Gagal mengambil iklan carousel.');
            const data = await response.json();
            renderAds(data.ads);
        };

        updateClock();
        updateMonitorData().catch((error) => console.error(error));
        updateAds().catch((error) => console.error(error));
        setInterval(updateClock, 1000);
        setInterval(() => updateMonitorData().catch((error) => console.error(error)), 5000);
        setInterval(() => updateAds().catch((error) => console.error(error)), 15000);
    </script>
</body>

</html>
