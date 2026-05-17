<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GREENVEST.CO - @yield('title', 'Dashboard')</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/regular/style.css">
    @vite(['resources/css/admin.css', 'resources/js/admin/app.js'])
    @stack('head')
</head>
<body x-data>

<div class="admin-layout">

    {{-- ── Sidebar ── --}}
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="sidebar-logo-mark">
                <i class="ph ph-leaf" style="color:#D8F3DC;font-size:1rem;"></i>
            </div>
            <div>
                <div class="sidebar-logo-text">GREENVEST.CO</div>
                <div class="sidebar-logo-sub">Ecological Ledger</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}"
               class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="ph ph-squares-four"></i>
                Dashboard
            </a>
            <a href="{{ route('admin.articles.index') }}"
               class="{{ request()->routeIs('admin.articles.*') ? 'active' : '' }}">
                <i class="ph ph-file-text"></i>
                Articles
            </a>
            <a href="{{ route('admin.comments.index') }}"
               class="{{ request()->routeIs('admin.comments.*') ? 'active' : '' }}">
                <i class="ph ph-chat-circle"></i>
                Comments
                @if(isset($pendingCommentCount) && $pendingCommentCount > 0)
                    <span style="margin-left:auto;background:#EF4444;color:#fff;font-size:0.6rem;font-weight:700;padding:0.1rem 0.375rem;border-radius:999px;">
                        {{ $pendingCommentCount }}
                    </span>
                @endif
            </a>
            <a href="{{ route('admin.profile') }}"
               class="{{ request()->routeIs('admin.profile*') ? 'active' : '' }}">
                <i class="ph ph-user"></i>
                Profile
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="{{ route('admin.logs') }}"
               class="{{ request()->routeIs('admin.logs*') ? 'active' : '' }}"
               style="display:flex;align-items:center;gap:0.75rem;padding:0.6875rem 0.875rem;border-radius:10px;font-size:0.875rem;font-weight:500;color:rgba(255,255,255,0.6);text-decoration:none;transition:all 0.15s;"
               onmouseover="this.style.color='rgba(255,255,255,0.9)'"
               onmouseout="this.style.color='rgba(255,255,255,0.6)'">
                <i class="ph ph-clock-counter-clockwise"></i>
                Logs
            </a>
        </div>
    </aside>

    {{-- ── Main ── --}}
    <div class="admin-main">

        {{-- Topbar --}}
        <header class="topbar" x-data="topbarSearch()">
            <div class="topbar-search">
                <i class="ph ph-magnifying-glass topbar-search-icon"></i>
                <input
                    type="text"
                    placeholder="Cari arsip..."
                    x-model="query"
                    @input.debounce.400ms="search()"
                    @keyup.enter="query && (window.location.href='/admin/artikel?search='+encodeURIComponent(query))"
                >
                {{-- Inline results --}}
                <div x-show="open" x-cloak
                     style="position:absolute;top:calc(100% + 6px);left:0;right:0;background:#fff;border:1px solid #E5E7EB;border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,0.1);z-index:50;overflow:hidden;">
                    <template x-for="item in results" :key="item.id">
                        <a :href="'/admin/artikel/'+item.slug+'/preview'"
                           style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem 1rem;text-decoration:none;border-bottom:1px solid #F9FAFB;font-size:0.875rem;color:#374151;"
                           @mouseenter="$el.style.background='#F9FAFB'"
                           @mouseleave="$el.style.background='#fff'">
                            <i class="ph ph-file-text" style="color:#9CA3AF;"></i>
                            <span x-text="item.title"></span>
                        </a>
                    </template>
                </div>
            </div>

            <div class="topbar-admin">
                <span class="topbar-admin-name">{{ auth()->user()->name }}</span>
                <img src="{{ auth()->user()->photo_url }}"
                     alt="Avatar"
                     class="topbar-avatar">
            </div>
        </header>

        {{-- Page Content --}}
        <main class="page-content">
            {{-- Flash messages --}}
            @if(session('success'))
                <div id="flash-success" style="margin-bottom:1rem;padding:0.875rem 1.25rem;background:#fff;border-left:4px solid #059669;border-radius:12px;font-size:0.875rem;font-weight:500;display:flex;align-items:center;gap:0.75rem;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
                    <i class="ph ph-check-circle" style="color:#059669;font-size:1.1rem;"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div style="margin-bottom:1rem;padding:0.875rem 1.25rem;background:#fff;border-left:4px solid #EF4444;border-radius:12px;font-size:0.875rem;font-weight:500;display:flex;align-items:center;gap:0.75rem;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
                    <i class="ph ph-warning-circle" style="color:#EF4444;font-size:1.1rem;"></i>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

{{-- Toast Container --}}
<div id="toast-container" class="toast-container"></div>

{{-- FAB (new article shortcut) --}}
@if(!request()->routeIs('admin.articles.create', 'admin.articles.edit'))
<a href="{{ route('admin.articles.create') }}" class="fab" title="Tambah Artikel">
    <i class="ph ph-plus"></i>
</a>
@endif

<script>
// Auto-dismiss flash
setTimeout(() => {
    document.getElementById('flash-success')?.remove();
}, 4000);
</script>

@stack('scripts')
</body>
</html>
