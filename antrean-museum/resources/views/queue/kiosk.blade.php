<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiosk Tiket Teater Museum</title>
    <!-- CSS Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-white d-flex align-items-center justify-content-center vh-100">
    <div class="card bg-secondary bg-opacity-25 border border-secondary p-4 rounded-4 shadow-lg text-white" style="max-width: 450px; width: 100%;">
        <div class="card-body text-center">
            <h1 class="h3 fw-bold mb-2 text-warning">Ambil Tiket Teater Museum</h1>
            <p class="text-white-50 small mb-4">Silakan pilih film dan ambil nomor antrean Anda</p>

            <!-- Notifikasi Berhasil -->
            @if(session('success'))
            <div class="alert alert-success bg-success bg-opacity-25 border-success text-success-emphasis p-3 rounded-3 mb-4">
                <p class="small mb-1 text-white">Nomor Antrean Anda Berhasil Dicetak:</p>
                <p class="display-5 fw-bold m-0 text-warning">{{ session('success') }}</p>
            </div>
            @endif

            <!-- Notifikasi Gagal / Sesi Sudah Ke-booking -->
            @if(session('error'))
            <div class="alert alert-danger bg-danger bg-opacity-25 border-danger text-danger-emphasis p-3 rounded-3 mb-4">
                <p class="small m-0 text-white fw-bold">⚠️ {{ session('error') }}</p>
            </div>
            @endif

            <form action="{{ route('queue.store') }}" method="POST" class="text-start">
                @csrf
                <div class="mb-3">
                    <label class="form-label small text-white-50">Nama Pengunjung</label>
                    <input type="text" name="visitor_name" required placeholder="Masukkan Nama Pengunjung" class="form-control bg-dark border-secondary text-white py-2 rounded-3 shadow-none" value="{{ old('visitor_name') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label small text-white-50">Pilih Judul Film Dokumenter</label>
                    <select name="movie_title" class="form-select bg-dark border-secondary text-white py-2 rounded-3 shadow-none">
                        <option value="Jejak Sejarah Nusantara" {{ old('movie_title') == 'Jejak Sejarah Nusantara' ? 'selected' : '' }}>Jejak Sejarah Nusantara</option>
                        <option value="Eksplorasi Gunung Merapi" {{ old('movie_title') == 'Eksplorasi Gunung Merapi' ? 'selected' : '' }}>Eksplorasi Gunung Merapi</option>
                        <option value="Peradaban Kuno & Budaya" {{ old('movie_title') == 'Peradaban Kuno & Budaya' ? 'selected' : '' }}>Peradaban Kuno & Budaya</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="form-label small text-white-50">Sesi Pemutaran</label>
                    <select name="session_time" class="form-select bg-dark border-secondary text-white py-2 rounded-3 shadow-none">
                        <option value="10:00 WIB" {{ old('session_time') == '10:00 WIB' ? 'selected' : '' }}>Sesi 1 - 10:00 WIB</option>
                        <option value="13:00 WIB" {{ old('session_time') == '13:00 WIB' ? 'selected' : '' }}>Sesi 2 - 13:00 WIB</option>
                        <option value="15:30 WIB" {{ old('session_time') == '15:30 WIB' ? 'selected' : '' }}>Sesi 3 - 15:30 WIB</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-warning w-100 fw-bold py-3 rounded-3 shadow text-dark">
                    Cetak Tiket Antrean 🎟️
                </button>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>