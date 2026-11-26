<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f7f9fc">
    <title>Login Petugas | Teater Film MGM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
</head>

<body class="min-h-screen bg-[#f7f9fc] font-sans text-slate-900">
    <div class="flex min-h-screen flex-col">
        <header class="relative z-10 flex h-14 items-center justify-between border-b border-slate-200/80 bg-white/70 px-5 sm:px-8 lg:px-14">
            <a href="{{ route('queue.index') }}" class="flex items-center gap-3 text-slate-800 no-underline" aria-label="Museum Gunungapi Merapi">
                <span class="flex h-8 w-7 items-start justify-center overflow-hidden">
                    <img src="{{ asset('images/mgm-logo-light.png') }}" alt="" class="h-8 w-8 max-w-none object-cover object-top">
                </span>
                <span class="text-[11px] font-bold tracking-[.12em]">MUSEUM GUNUNGAPI MERAPI</span>
            </a>
            <a href="{{ route('queue.index') }}" class="inline-flex items-center gap-2 rounded-md border border-slate-200 px-3.5 py-2 text-[11px] font-semibold text-slate-700 no-underline transition hover:border-slate-300 hover:bg-white">
                <span aria-hidden="true">←</span>
                Layar TV Antrean
            </a>
        </header>

        <main class="relative flex flex-1 items-center justify-center overflow-hidden px-4 py-10">
            <div class="pointer-events-none absolute inset-0 opacity-70" aria-hidden="true" style="background-image: linear-gradient(to right, rgba(226,232,240,.42) 1px, transparent 1px), linear-gradient(to bottom, rgba(226,232,240,.42) 1px, transparent 1px); background-size: 184px 238px;"></div>

            <section class="relative w-full max-w-[376px] overflow-hidden rounded-md border border-slate-200 bg-white px-6 pb-7 pt-6 shadow-[0_1px_3px_rgba(15,23,42,.03)] sm:px-[26px]">
                <div class="absolute inset-x-0 top-0 h-[3px] bg-red-600"></div>
                <div class="mb-4 rounded bg-red-50 px-2.5 py-1.5 font-mono text-[9px] font-semibold tracking-[.08em] text-red-600">
                    INTERNAL ACCESS · FORM &amp; ADMIN
                </div>
                <h1 class="text-[24px] font-bold leading-tight tracking-[-.045em] text-slate-900">Masuk Sistem Teater</h1>
                <p class="mt-2 text-[11px] leading-[1.7] text-slate-500">Gunakan kredensial petugas untuk membuka akses Kiosk &amp; Kontrol Antrean.</p>

                @if($errors->any())
                    <div class="mt-4 rounded border border-red-200 bg-red-50 px-3 py-2 text-[11px] text-red-700" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('login.store') }}" method="POST" class="mt-5 space-y-4">
                    @csrf
                    <div>
                        <label for="username" class="mb-1.5 block text-[11px] font-semibold text-slate-800">Username</label>
                        <div class="flex h-[42px] items-center gap-3 rounded-md border border-slate-300 px-3 focus-within:border-slate-500 focus-within:ring-2 focus-within:ring-slate-100">
                            <svg class="h-4 w-4 shrink-0 text-slate-500" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm0 2c-4.1 0-7 2.1-7 4.5V21h14v-2.5c0-2.4-2.9-4.5-7-4.5Z"/>
                            </svg>
                            <input id="username" name="username" type="text" value="{{ old('username') }}" autocomplete="username" required class="h-full min-w-0 flex-1 border-0 bg-transparent p-0 text-[12px] text-slate-800 outline-none placeholder:text-slate-400 focus:ring-0" placeholder="Masukkan username">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="mb-1.5 block text-[11px] font-semibold text-slate-800">Password</label>
                        <div class="flex h-[42px] items-center gap-3 rounded-md border border-slate-300 px-3 focus-within:border-slate-500 focus-within:ring-2 focus-within:ring-slate-100">
                            <svg class="h-4 w-4 shrink-0 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <rect x="5" y="10" width="14" height="11" rx="2"/>
                                <path d="M8 10V7a4 4 0 1 1 8 0v3"/>
                            </svg>
                            <input id="password" name="password" type="password" autocomplete="current-password" required class="h-full min-w-0 flex-1 border-0 bg-transparent p-0 text-[12px] text-slate-800 outline-none placeholder:text-slate-400 focus:ring-0" placeholder="Masukkan password">
                            <button id="toggle-password" type="button" class="grid h-7 w-7 shrink-0 place-items-center rounded text-slate-500 hover:bg-slate-100" aria-label="Tampilkan password" aria-pressed="false">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z"/>
                                    <circle cx="12" cy="12" r="2.5"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <label class="flex cursor-pointer items-center gap-2 text-[10px] text-slate-600">
                        <input type="checkbox" name="remember" value="1" @checked(old('remember', true)) class="h-3.5 w-3.5 rounded border-slate-300 accent-[#e3262d] focus:ring-red-500">
                        Ingat sesi di perangkat ini
                    </label>

                    <button type="submit" class="flex h-[41px] w-full items-center justify-center gap-1.5 rounded-md bg-[#e3262d] px-4 text-[12px] font-bold text-white transition hover:bg-[#cc1f26] focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        Masuk &amp; Buka Kiosk
                        <span aria-hidden="true">→</span>
                    </button>
                </form>

                <div class="mt-5 flex gap-2.5 border-t border-slate-200 pt-4 text-[10px] leading-[1.6] text-slate-500">
                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M12 3 19 6v5c0 4.8-3 8-7 10-4-2-7-5.2-7-10V6l7-3Z"/>
                        <path d="M12 8v4m0 3h.01"/>
                    </svg>
                    <p>Tidak ada pendaftaran akun mandiri. Akun petugas dikelola oleh super admin.</p>
                </div>
            </section>
        </main>

        <footer class="pb-6 text-center font-mono text-[9px] font-medium tracking-[.08em] text-slate-400">
            {{ now()->year }} Museum Gunungapi Merapi · Sleman, Yogyakarta
        </footer>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('toggle-password');

        togglePassword.addEventListener('click', () => {
            const isVisible = passwordInput.type === 'text';
            passwordInput.type = isVisible ? 'password' : 'text';
            togglePassword.setAttribute('aria-pressed', String(!isVisible));
            togglePassword.setAttribute('aria-label', isVisible ? 'Tampilkan password' : 'Sembunyikan password');
        });
    </script>
</body>

</html>
