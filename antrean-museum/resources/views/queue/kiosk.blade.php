<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiosk Tiket Teater Museum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/queue-theme.css') }}" rel="stylesheet">
</head>

<body class="bg-dark text-white min-vh-100 p-3">
    <header class="container d-flex justify-content-between align-items-center gap-3 py-3">
        <a href="{{ route('queue.index') }}" class="btn btn-outline-info btn-sm">Layar Antrean</a>
        @include('queue.partials.theme-toggle')
    </header>

    <main class="d-flex align-items-center justify-content-center">
        <div class="card bg-secondary bg-opacity-25 border border-secondary p-4 rounded-4 shadow-lg text-white" style="max-width: 520px; width: 100%;">
            <div class="card-body">
                <h1 class="h3 fw-bold mb-2 text-warning text-center">Pendaftaran Antrean Teater</h1>
                <p class="text-white-50 small mb-4 text-center">Masukkan satu nama perwakilan untuk setiap pendaftaran rombongan.</p>

                @if(session('success'))
                <div class="alert alert-success p-3 rounded-3 mb-4" role="status">
                    Nomor antrean berhasil dibuat: <strong class="fs-4 text-warning">{{ session('success') }}</strong>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger p-3 rounded-3 mb-4" role="alert">{{ session('error') }}</div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger p-3 rounded-3 mb-4" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('queue.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="visitor_type" class="form-label">Kategori pendaftaran</label>
                        <select id="visitor_type" name="visitor_type" class="form-select" required>
                            <option value="individual" {{ old('visitor_type', 'individual') === 'individual' ? 'selected' : '' }}>Individu</option>
                            <option value="group" {{ old('visitor_type') === 'group' ? 'selected' : '' }}>Rombongan / Instansi</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="visitor_name" class="form-label">Nama pengunjung / perwakilan</label>
                        <input id="visitor_name" type="text" name="visitor_name" required maxlength="255" placeholder="Masukkan nama perwakilan" class="form-control" value="{{ old('visitor_name') }}">
                    </div>
                    <div id="institution-field" class="mb-3" @if(old('visitor_type') !== 'group') hidden @endif>
                        <label for="institution_name" class="form-label">Nama instansi / rombongan</label>
                        <input id="institution_name" type="text" name="institution_name" maxlength="255" placeholder="Contoh: SMP Negeri 1" class="form-control" value="{{ old('institution_name') }}">
                        <div class="form-text">Satu instansi hanya dapat mendaftar satu sesi per hari.</div>
                    </div>
                    <div class="mb-3">
                        <label for="movie_title" class="form-label">Judul film dokumenter</label>
                        <select id="movie_title" name="movie_title" class="form-select" required>
                            <option value="Jejak Sejarah Nusantara" {{ old('movie_title') === 'Jejak Sejarah Nusantara' ? 'selected' : '' }}>Jejak Sejarah Nusantara</option>
                            <option value="Eksplorasi Gunung Merapi" {{ old('movie_title') === 'Eksplorasi Gunung Merapi' ? 'selected' : '' }}>Eksplorasi Gunung Merapi</option>
                            <option value="Peradaban Kuno &amp; Budaya" {{ old('movie_title') === 'Peradaban Kuno & Budaya' ? 'selected' : '' }}>Peradaban Kuno &amp; Budaya</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="session_time" class="form-label">Sesi pemutaran</label>
                        <select id="session_time" name="session_time" class="form-select" required>
                            <option value="10:00 WIB" {{ old('session_time') === '10:00 WIB' ? 'selected' : '' }}>Sesi 1 - 10:00 WIB</option>
                            <option value="13:00 WIB" {{ old('session_time') === '13:00 WIB' ? 'selected' : '' }}>Sesi 2 - 13:00 WIB</option>
                            <option value="15:30 WIB" {{ old('session_time') === '15:30 WIB' ? 'selected' : '' }}>Sesi 3 - 15:30 WIB</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-warning w-100 fw-bold py-3 rounded-3 shadow text-dark">
                        Buat Nomor Antrean 🎟️
                    </button>
                </form>
            </div>
        </div>
    </main>

    <script>
        const categorySelect = document.getElementById('visitor_type');
        const institutionField = document.getElementById('institution-field');
        const institutionInput = document.getElementById('institution_name');

        const updateCategoryFields = () => {
            const isGroup = categorySelect.value === 'group';
            institutionField.hidden = !isGroup;
            institutionInput.required = isGroup;
        };

        categorySelect.addEventListener('change', updateCategoryFields);
        updateCategoryFields();
    </script>
</body>

</html>
