<button id="queue-theme-toggle" type="button" class="btn btn-outline-light btn-sm" aria-pressed="false">
    Mode Terang
</button>

<script>
    (() => {
        const root = document.documentElement;
        const button = document.getElementById('queue-theme-toggle');
        const storageKey = 'queue-theme';

        const applyTheme = (theme) => {
            root.dataset.bsTheme = theme;
            button.textContent = theme === 'dark' ? '☀️ Mode Terang' : '🌙 Mode Gelap';
            button.setAttribute('aria-pressed', String(theme === 'light'));
        };

        applyTheme(localStorage.getItem(storageKey) || 'dark');
        button.addEventListener('click', () => {
            const theme = root.dataset.bsTheme === 'dark' ? 'light' : 'dark';
            localStorage.setItem(storageKey, theme);
            applyTheme(theme);
        });
    })();
</script>
