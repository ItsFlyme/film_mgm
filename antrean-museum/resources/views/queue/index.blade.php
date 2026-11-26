<!DOCTYPE html>
<html lang="id" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f5f7fa">
    <title>Layar Antrean | Teater Film MGM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/queue-theme.css') }}" rel="stylesheet">
</head>

<body class="monitor-page">
    <header class="site-header">
        <a href="{{ route('queue.index') }}" class="brand" aria-label="Teater Film Dokumenter Museum Gunungapi Merapi">
            <span class="brand-mark" aria-hidden="true">
                <img class="brand-logo brand-logo-light" src="{{ asset('images/mgm-logo-light.png') }}" alt="">
                <img class="brand-logo brand-logo-dark" src="{{ asset('images/mgm-logo-dark.png') }}" alt="">
            </span>
            <span class="brand-copy">
                <strong>TEATER FILM DOKUMENTER</strong>
                <span>Museum Gunungapi Merapi</span>
            </span>
        </a>

        <div class="header-center">
            @include('queue.partials.theme-toggle')
        </div>

        <div class="header-meta">
            <time id="live-date" class="header-date"></time>
            <time id="live-clock" class="header-clock">--:--:-- WIB</time>
        </div>
    </header>

    <div class="monitor-intro">
        <h1 class="page-title monitor-overview-title">Informasi Antrean &amp; Agenda Teater</h1>
    </div>

    <main class="monitor-main">
        <section class="monitor-left" aria-label="Informasi antrean">
            <article class="panel called-panel">
                <p class="eyebrow accent-text mb-0">Sedang dipanggil</p>
                <div id="current-queue" class="called-queue-number">{{ $currentQueue->queue_number ?? '---' }}</div>
                <h2 id="current-name" class="called-name">
                    {{ $currentQueue->institution_name ?? $currentQueue->visitor_name ?? 'Menunggu Panggilan...' }}
                </h2>
                <p class="called-hint">Silakan menuju pintu masuk teater.</p>
                <div class="called-film-row">
                    <div>
                        <span class="eyebrow">Film dokumenter</span>
                        <p id="movie-title" class="called-film-title">{{ $currentQueue->movie_title ?? 'Belum ada sesi aktif' }}</p>
                    </div>
                    <span id="session-time" class="called-session">{{ $currentQueue->session_time ?? '—' }}</span>
                </div>
            </article>

            <section class="panel waiting-panel">
                <div class="panel-heading mb-2">
                    <h2 class="panel-title">Antrean menunggu</h2>
                    <span class="waiting-count"><span id="waiting-count">{{ $waitingQueues->count() }}</span> dalam antrean</span>
                </div>
                <div id="waiting-list" class="monitor-waiting-list" aria-live="polite">
                    @forelse($waitingQueues as $queue)
                    <div class="monitor-queue-row">
                        <span class="monitor-queue-number">{{ $queue->queue_number }}</span>
                        <span class="monitor-queue-info">
                            <span class="monitor-queue-name d-block">{{ $queue->institution_name ?? $queue->visitor_name }}</span>
                            <span class="monitor-queue-film d-block">{{ $queue->movie_title }}</span>
                        </span>
                        <span class="monitor-queue-session">{{ $queue->session_time }}</span>
                    </div>
                    @empty
                    <p class="text-body-secondary text-center py-3 mb-0">Belum ada antrean menunggu.</p>
                    @endforelse
                </div>
            </section>
        </section>

        <section class="panel ads-panel" aria-label="Informasi dan promosi teater">
            <div class="panel-heading">
                <h2 class="panel-title">Informasi &amp; Agenda Teater MGM</h2>
                <span class="eyebrow">Museum MGM</span>
            </div>

            <div id="museum-ads" class="carousel slide d-none">
                <div id="ads-indicators" class="carousel-indicators"></div>
                <div id="ads-inner" class="carousel-inner"></div>
                <button class="carousel-control-prev d-none" type="button" data-bs-target="#museum-ads" data-bs-slide="prev" aria-label="Iklan sebelumnya">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                </button>
                <button class="carousel-control-next d-none" type="button" data-bs-target="#museum-ads" data-bs-slide="next" aria-label="Iklan berikutnya">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                </button>
            </div>
            <div id="ads-empty" class="ads-empty">
                <span>Belum ada iklan aktif. Tambahkan gambar melalui dashboard petugas.</span>
            </div>

            <div class="daily-sessions" aria-label="Jadwal sesi pemutaran">
                <div class="daily-session">
                    <time>10:00</time>
                    <span>WIB · Setiap hari</span>
                </div>
                <div class="daily-session is-featured">
                    <time>13:00</time>
                    <span>WIB · Setiap hari</span>
                </div>
                <div class="daily-session">
                    <time>15:30</time>
                    <span>WIB · Setiap hari</span>
                </div>
            </div>
        </section>
    </main>

    <footer class="monitor-footer">
        <section class="overflow-hidden" aria-label="Tata tertib teater">
            <div class="ticker-track">
                <div class="ticker-group">
                    <span class="eyebrow">Tata tertib teater</span>
                    <span>Simpan makanan &amp; minuman di luar teater</span>
                    <span class="ticker-dot" aria-hidden="true">●</span>
                    <span>Aktifkan mode senyap</span>
                    <span class="ticker-dot" aria-hidden="true">●</span>
                    <span>Ikuti arahan petugas MGM</span>
                </div>
                <div class="ticker-group" aria-hidden="true">
                    <span class="eyebrow">Tata tertib teater</span>
                    <span>Simpan makanan &amp; minuman di luar teater</span>
                    <span class="ticker-dot">●</span>
                    <span>Aktifkan mode senyap</span>
                    <span class="ticker-dot">●</span>
                    <span>Ikuti arahan petugas MGM</span>
                </div>
            </div>
        </section>
        <div class="monitor-copyright">Museum Gunungapi Merapi · Teater Film Dokumenter</div>
    </footer>

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
                appendText(waitingList, 'p', 'text-body-secondary text-center py-3 mb-0', 'Belum ada antrean menunggu.');
                return;
            }

            queues.forEach((queue) => {
                const row = document.createElement('div');
                row.className = 'monitor-queue-row';
                appendText(row, 'span', 'monitor-queue-number', queue.queue_number);

                const detail = document.createElement('div');
                detail.className = 'monitor-queue-info';
                appendText(detail, 'span', 'monitor-queue-name', queue.institution_name || queue.visitor_name);
                appendText(detail, 'span', 'monitor-queue-film', queue.movie_title);
                row.append(detail);

                appendText(row, 'span', 'monitor-queue-session', queue.session_time);
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
            document.getElementById('current-queue').textContent = currentQueue?.queue_number ?? '---';
            document.getElementById('movie-title').textContent = currentQueue?.movie_title ?? 'Belum ada sesi aktif';
            document.getElementById('session-time').textContent = currentQueue?.session_time ?? '—';
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
                appendText(caption, 'h3', '', ad.title);
                if (ad.description) appendText(caption, 'p', '', ad.description);
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
