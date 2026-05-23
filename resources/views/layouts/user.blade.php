<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GREENVEST.CO - @yield('title', 'Beranda')</title>
    <meta name="description" content="@yield('meta_desc', 'Greenvest adalah platform edukasi investasi hijau terpercaya di Indonesia.')">
    <link rel="icon" type="image/svg+xml" href="/images/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/regular/style.css">
    @vite(['resources/css/user.css', 'resources/js/user/app.js'])
    @stack('head')
</head>
<body x-data>

{{-- ── Navbar ── --}}
<nav class="navbar">
    <div class="navbar-inner">
        <a href="{{ route('user.home') }}" class="navbar-brand">GREENVEST.CO</a>

        <div class="navbar-links">
            <a href="{{ route('user.home') }}#edukasi"
               class="navbar-link {{ request()->routeIs('user.home') ? '' : '' }}">
               Edukasi
            </a>
            <a href="{{ route('user.articles.index') }}"
               class="navbar-link {{ request()->routeIs('user.articles.*') ? 'active' : '' }}">
               Artikel
            </a>
            <a href="{{ route('user.simulation') }}"
               class="navbar-link {{ request()->routeIs('user.simulation*') ? 'active' : '' }}">
               Simulasi
            </a>
        </div>

        {{-- ── Dark Mode Toggle ── --}}
<button class="dark-toggle" onclick="toggleDark()" title="Toggle Dark Mode" id="dark-btn">
    <i class="ph ph-moon" id="dark-icon"></i>
</button>
        <div class="navbar-search" x-data="navSearch()">
            <button class="navbar-search-btn" @click="toggle()" aria-label="Cari">
                <i class="ph ph-magnifying-glass"></i>
            </button>
            <div x-show="open" x-cloak
                 style="position:fixed;top:64px;left:0;right:0;background:#fff;border-bottom:1px solid #F3F4F6;padding:1rem 1.5rem;z-index:40;box-shadow:0 4px 16px rgba(0,0,0,0.06);">
                <div style="max-width:600px;margin:0 auto;display:flex;gap:0.75rem;">
                    <input
                        x-ref="input"
                        type="text"
                        x-model="query"
                        placeholder="Cari artikel..."
                        @keyup.enter="submit()"
                        @keyup.escape="open=false"
                        style="flex:1;padding:0.6875rem 1rem;border:1.5px solid #E5E7EB;border-radius:10px;font-size:0.9375rem;outline:none;"
                    >
                    <button @click="submit()"
                            style="padding:0.6875rem 1.25rem;background:#1B4332;color:#fff;border:none;border-radius:10px;font-size:0.875rem;font-weight:600;cursor:pointer;">
                        Cari
                    </button>
                    <button @click="open=false"
                            style="padding:0.6875rem;background:#F3F4F6;border:none;border-radius:10px;cursor:pointer;font-size:1rem;color:#6B7280;">
                        <i class="ph ph-x"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</nav>

{{-- ── Page Content ── --}}
@yield('content')

{{-- ── Footer ── --}}
<footer class="footer">
    <p>&copy; {{ date('Y') }} Greenvest.co — Ekosistem Edukasi Investasi Hijau Indonesia</p>
</footer>

<script>
function toggleDark() {
    const body = document.body;
    const icon = document.getElementById('dark-icon');
    body.classList.toggle('dark');
    const isDark = body.classList.contains('dark');
    localStorage.setItem('gv_dark', isDark ? '1' : '0');
    icon.className = isDark ? 'ph ph-sun' : 'ph ph-moon';
}

// Load preferensi saat halaman dibuka
(function() {
    const isDark = localStorage.getItem('gv_dark') === '1';
    if (isDark) {
        document.body.classList.add('dark');
        document.addEventListener('DOMContentLoaded', function() {
            const icon = document.getElementById('dark-icon');
            if (icon) icon.className = 'ph ph-sun';
        });
    }
})();
</script>
@stack('scripts')
</body>
</html>
