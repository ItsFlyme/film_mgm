<!DOCTYPE html>
<html lang="id" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f5f7fa">
    <title>Pendaftaran Mandiri | Teater Film MGM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/queue-theme.css') }}" rel="stylesheet">
</head>

<body class="kiosk-page">
    <header class="site-header">
        <div class="brand">
            <span class="brand-mark" aria-hidden="true">
                <img class="brand-logo brand-logo-light" src="{{ asset('images/mgm-logo-light.png') }}" alt="">
                <img class="brand-logo brand-logo-dark" src="{{ asset('images/mgm-logo-dark.png') }}" alt="">
            </span>
            <span class="brand-copy">
                <strong>MUSEUM GUNUNGAPI MERAPI</strong>
                <span>Teater Film Dokumenter</span>
            </span>
        </div>

        <div class="header-center">
            <nav class="site-nav" aria-label="Navigasi halaman">
                <a href="{{ route('queue.index') }}">Layar TV</a>
                <a href="{{ route('queue.kiosk') }}" aria-current="page">Form</a>
                <a href="{{ route('queue.admin') }}">Admin</a>
            </nav>
            @include('queue.partials.theme-toggle')
        </div>

        <div class="header-meta">
            <span class="header-date">Pendaftaran mandiri</span>
            <time class="header-date">{{ now()->timezone('Asia/Jakarta')->locale('id')->translatedFormat('l, d F Y') }}</time>
        </div>
    </header>

    <main class="kiosk-main">
        <section class="panel kiosk-card">
            <p class="page-kicker">Pendaftaran mandiri</p>
            <h1 class="page-title">Registrasi Antrean Teater Film</h1>
            <p class="page-description">Pilih film dan sesi, lalu isi data pendaftaran antrean.</p>

            @if(session('success'))
            <div class="alert alert-success p-3 mt-4 mb-0" role="status">
                Nomor antrean berhasil dibuat:
                <strong class="d-block fs-3 mt-1">{{ session('success') }}</strong>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger p-3 mt-4 mb-0" role="alert">{{ session('error') }}</div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger p-3 mt-4 mb-0" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('queue.store') }}" method="POST" class="mt-4">
                @csrf

                <section aria-labelledby="visitor-section-title">
                    <h2 id="visitor-section-title" class="kiosk-step-title">
                        <span class="kiosk-step-number" aria-hidden="true">1</span>
                        <span>Data pengunjung</span>
                    </h2>
                    <div class="category-options mb-3">
                        <label class="choice-card">
                            <input id="visitor_type" type="radio" name="visitor_type" value="individual" required @checked(old('visitor_type', 'individual') === 'individual')>
                            <span class="choice-title">Individu / keluarga</span>
                            <span class="choice-meta">Pendaftaran perorangan</span>
                        </label>
                        <label class="choice-card">
                            <input type="radio" name="visitor_type" value="group" required @checked(old('visitor_type') === 'group')>
                            <span class="choice-title">Rombongan / instansi</span>
                            <span class="choice-meta">Pendaftaran oleh satu perwakilan</span>
                        </label>
                    </div>

                    <div class="mb-3">
                        <label id="visitor-name-label" for="visitor_name" class="form-label">Nama pengunjung</label>
                        <input id="visitor_name" type="text" name="visitor_name" required maxlength="255" placeholder="Masukkan nama pengunjung" class="form-control" value="{{ old('visitor_name') }}">
                    </div>
                    <div id="institution-field" class="mb-3" @if(old('visitor_type') !== 'group') hidden @endif>
                        <label for="institution_name" class="form-label">Nama instansi / rombongan</label>
                        <input id="institution_name" type="text" name="institution_name" maxlength="255" placeholder="Contoh: SMP Negeri 1" class="form-control" value="{{ old('institution_name') }}">
                        <div class="form-text">Satu instansi hanya dapat mendaftar satu sesi per hari.</div>
                    </div>
                </section>

                <hr class="kiosk-divider">

                <section aria-labelledby="movie-section-title">
                    <h2 id="movie-section-title" class="kiosk-step-title">
                        <span class="kiosk-step-number" aria-hidden="true">2</span>
                        <span>Pilih film dokumenter</span>
                    </h2>
                    <div class="selection-options">
                        <label class="choice-card">
                            <input type="radio" name="movie_title" value="Eksplorasi Gunung Merapi" required @checked(old('movie_title', 'Eksplorasi Gunung Merapi') === 'Eksplorasi Gunung Merapi')>
                            <span class="choice-title">Eksplorasi Gunung Merapi</span>
                            <span class="choice-meta">Film dokumenter</span>
                        </label>
                        <label class="choice-card">
                            <input type="radio" name="movie_title" value="Jejak Sejarah Nusantara" required @checked(old('movie_title') === 'Jejak Sejarah Nusantara')>
                            <span class="choice-title">Jejak Sejarah Nusantara</span>
                            <span class="choice-meta">Film dokumenter</span>
                        </label>
                        <label class="choice-card">
                            <input type="radio" name="movie_title" value="Peradaban Kuno &amp; Budaya" required @checked(old('movie_title') === 'Peradaban Kuno & Budaya')>
                            <span class="choice-title">Peradaban Kuno &amp; Budaya</span>
                            <span class="choice-meta">Film dokumenter</span>
                        </label>
                    </div>
                </section>

                <hr class="kiosk-divider">

                <section aria-labelledby="session-section-title">
                    <h2 id="session-section-title" class="kiosk-step-title">
                        <span class="kiosk-step-number" aria-hidden="true">3</span>
                        <span>Pilih sesi pemutaran</span>
                    </h2>
                    <div class="selection-options session-options">
                        <label class="choice-card session-card">
                            <input type="radio" name="session_time" value="10:00 WIB" required @checked(old('session_time', '10:00 WIB') === '10:00 WIB')>
                            <span class="choice-title">10:00 WIB</span>
                            <span class="choice-meta">Setiap hari</span>
                        </label>
                        <label class="choice-card session-card">
                            <input type="radio" name="session_time" value="13:00 WIB" required @checked(old('session_time') === '13:00 WIB')>
                            <span class="choice-title">13:00 WIB</span>
                            <span class="choice-meta">Setiap hari</span>
                        </label>
                        <label class="choice-card session-card">
                            <input type="radio" name="session_time" value="15:30 WIB" required @checked(old('session_time') === '15:30 WIB')>
                            <span class="choice-title">15:30 WIB</span>
                            <span class="choice-meta">Setiap hari</span>
                        </label>
                    </div>
                </section>

                <hr class="kiosk-divider">

                <button type="submit" class="btn btn-accent kiosk-submit w-100">
                    <span>Ambil nomor antrean</span>
                    <span class="kiosk-submit-arrow" aria-hidden="true">→</span>
                </button>
                <p class="form-text small mb-0 mt-2">Nomor antrean akan ditampilkan setelah pendaftaran berhasil.</p>
            </form>
        </section>
    </main>

    <script>
        const categoryOptions = document.querySelectorAll('input[name="visitor_type"]');
        const institutionField = document.getElementById('institution-field');
        const institutionInput = document.getElementById('institution_name');
        const visitorNameLabel = document.getElementById('visitor-name-label');
        const visitorNameInput = document.getElementById('visitor_name');

        const updateCategoryFields = () => {
            const selectedCategory = document.querySelector('input[name="visitor_type"]:checked');
            const isGroup = selectedCategory?.value === 'group';
            institutionField.hidden = !isGroup;
            institutionInput.required = isGroup;
            visitorNameLabel.textContent = isGroup ? 'Nama perwakilan' : 'Nama pengunjung';
            visitorNameInput.placeholder = isGroup ? 'Masukkan nama perwakilan' : 'Masukkan nama pengunjung';
        };

        categoryOptions.forEach((option) => option.addEventListener('change', updateCategoryFields));
        updateCategoryFields();
    </script>
</body>

</html>
