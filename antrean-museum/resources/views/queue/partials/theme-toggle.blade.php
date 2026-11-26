<button id="queue-theme-toggle" type="button" class="theme-toggle" aria-label="Aktifkan mode gelap" aria-pressed="false" title="Ganti tema">
    <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M20.2 15.1A8.6 8.6 0 0 1 8.9 3.8 8.8 8.8 0 1 0 20.2 15.1Z"></path>
    </svg>
    <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <circle cx="12" cy="12" r="4"></circle>
        <path d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"></path>
    </svg>
</button>

<script>
    (() => {
        const root = document.documentElement;
        const button = document.getElementById('queue-theme-toggle');
        const storageKey = 'queue-theme';
        const savedTheme = localStorage.getItem(storageKey);

        const applyTheme = (theme) => {
            const isDark = theme === 'dark';
            root.dataset.bsTheme = isDark ? 'dark' : 'light';
            button.setAttribute('aria-pressed', String(isDark));
            button.setAttribute('aria-label', isDark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap');
            button.title = isDark ? 'Ganti ke mode terang' : 'Ganti ke mode gelap';
        };

        applyTheme(savedTheme === 'dark' ? 'dark' : 'light');

        button.addEventListener('click', () => {
            const theme = root.dataset.bsTheme === 'dark' ? 'light' : 'dark';
            localStorage.setItem(storageKey, theme);
            applyTheme(theme);
        });

        window.addEventListener('storage', (event) => {
            if (event.key === storageKey) applyTheme(event.newValue === 'dark' ? 'dark' : 'light');
        });
    })();
</script>
