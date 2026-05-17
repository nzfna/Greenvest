@extends('layouts.user')
@section('title', 'Artikel — Greenvest')

@section('content')

{{-- Hero --}}
<div class="artikel-hero">
    <div class="page-wrap">
        <h1 class="artikel-hero-title">ARTIKEL</h1>
        <p class="artikel-hero-sub">Jelajahi wawasan terbaru mengenai investasi berkelanjutan dan masa depan keuangan hijau.</p>

        {{-- Category tabs --}}
        <div class="category-tabs">
            <a href="{{ route('user.articles.index') }}"
               class="category-tab {{ !request('category') ? 'active' : '' }}">
               General
            </a>
            @foreach($categories as $cat)
            <a href="{{ route('user.articles.index', ['category' => $cat->slug]) }}"
               class="category-tab {{ request('category') === $cat->slug ? 'active' : '' }}">
               {{ $cat->name }}
            </a>
            @endforeach
        </div>

        {{-- Progress Literasi Minggu Ini (localStorage) --}}
        <div x-data="literacyWidget()" style="max-width:340px;margin:1.25rem auto 0;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.375rem;">
                <span style="font-size:0.8125rem;color:#6B7280;" x-text="statusLabel"></span>
                <span style="font-size:0.9375rem;font-weight:800;color:#0D3B2E;min-width:56px;text-align:right;"
                      x-text="displayText"></span>
            </div>
            <div style="height:7px;background:#E5E7EB;border-radius:999px;overflow:hidden;">
                <div style="height:100%;border-radius:999px;transition:width 0.6s;"
                     :style="{ width: animatedPct + '%', background: barColor }"></div>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:0.6875rem;color:#9CA3AF;margin-top:0.375rem;">
                <span>Baca +1%</span>
                <span>Komentar +3%</span>
                <span>Simulasi +2%</span>
            </div>
        </div>
    </div>
</div>

<div class="page-wrap" style="padding-bottom:2rem;">

    {{-- Search info --}}
    @if(request('search'))
    <p style="font-size:0.875rem;color:#6B7280;margin-bottom:1.25rem;">
        Hasil pencarian untuk "<strong>{{ request('search') }}</strong>" — {{ $articles->total() }} artikel ditemukan.
        <a href="{{ route('user.articles.index') }}" style="color:#40916C;text-decoration:none;margin-left:0.375rem;">Reset</a>
    </p>
    @endif

    {{-- Articles grid --}}
    <div class="articles-grid">
        @forelse($articles as $article)
        <div style="background:#fff;border:1px solid #F3F4F6;border-radius:16px;overflow:hidden;display:flex;flex-direction:column;">
            <img src="{{ $article->cover_url }}" alt="{{ $article->title }}"
                 class="article-card-img">
            <div class="article-card-body" style="flex:1;display:flex;flex-direction:column;">
                <span class="category-pill pill-{{ $article->category->slug }}" style="margin-bottom:0.625rem;display:inline-block;">
                    {{ $article->category->name }}
                </span>
                <div class="article-card-title" style="margin-bottom:0.5rem;">{{ $article->title }}</div>
                <div class="article-card-desc" style="margin-bottom:0.75rem;flex:1;">{{ $article->excerpt }}</div>
                <a href="{{ route('user.articles.show', $article->slug) }}" class="read-more-link">
                    Baca Selengkapnya <i class="ph ph-arrow-right"></i>
                </a>
            </div>
        </div>
        @empty
        <div style="grid-column:1/-1;text-align:center;padding:4rem 0;color:#9CA3AF;">
            <i class="ph ph-newspaper" style="font-size:3rem;display:block;margin-bottom:1rem;opacity:0.4;"></i>
            <p style="font-size:1rem;font-weight:600;">Belum ada artikel.</p>
            <p style="font-size:0.875rem;margin-top:0.375rem;">Coba kategori atau kata kunci lain.</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($articles->hasPages())
    <div class="pagination">
        @if($articles->onFirstPage())
            <span class="page-btn disabled"><i class="ph ph-caret-left"></i></span>
        @else
            <a href="{{ $articles->previousPageUrl() }}" class="page-btn"><i class="ph ph-caret-left"></i></a>
        @endif

        @foreach($articles->getUrlRange(1, $articles->lastPage()) as $page => $url)
            <a href="{{ $url }}" class="page-btn {{ $page == $articles->currentPage() ? 'active' : '' }}">{{ $page }}</a>
        @endforeach

        @if($articles->hasMorePages())
            <a href="{{ $articles->nextPageUrl() }}" class="page-btn"><i class="ph ph-caret-right"></i></a>
        @else
            <span class="page-btn disabled"><i class="ph ph-caret-right"></i></span>
        @endif
    </div>
    @endif

</div>
@endsection
