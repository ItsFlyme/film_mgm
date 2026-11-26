<details class="account-menu">
    <summary aria-label="Profil akun {{ auth()->user()->username }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <circle cx="12" cy="8" r="3.5"></circle>
            <path d="M5 20v-1.5a7 7 0 0 1 14 0V20"></path>
        </svg>
    </summary>
    <div class="account-menu-panel">
        <strong>{{ auth()->user()->username }}</strong>
        <span>{{ auth()->user()->role === 'super_admin' ? 'Super Admin' : 'Admin' }}</span>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </div>
</details>
