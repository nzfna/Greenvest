@extends('layouts.user')
@section('title', 'Greenvest — Edukasi Investasi Hijau')
@section('meta_desc', 'Platform edukasi investasi hijau terpercaya di Indonesia. Pelajari Green Bonds, Energi Surya, dan ESG.')

@section('content')
<div class="page-wrap">

    {{-- ── Hero ── --}}
    <section class="hero" id="edukasi">
        <div class="hero-inner">
            <div>
                <div class="hero-tag">
                    <i class="ph ph-leaf"></i> Platform Investasi Hijau #1
                </div>
                <h1 class="hero-title">Greenvest Edukasi Investasi Hijau</h1>
                <p class="hero-desc">
                    Greenvest adalah platform yang memberikan edukasi tentang Investasi hijau dengan artikel dan simulasi yang mudah dimengerti.
                </p>
                <div class="hero-actions">
                    <a href="{{ route('user.articles.show', ['slug' => 'apa-itu-greenvest']) }}" class="btn-cta-primary">
                        Mulai Belajar
                    </a>
                    <a href="{{ route('user.simulation') }}" class="btn-cta-secondary">
                        Coba Simulasi
                    </a>
                </div>
            </div>

            {{-- Featured article card --}}
            @if($featured)
            <a href="{{ route('user.articles.show', $featured->slug) }}" class="featured-card">
                <img src="{{ $featured->cover_url }}"
                     alt="{{ $featured->title }}"
                     class="featured-img">
                <div class="featured-card-body">
                    <span class="category-pill pill-{{ $featured->category->slug }}">
                        {{ $featured->category->name }}
                    </span>
                    <div class="featured-title">{{ $featured->title }}</div>
                    <div class="featured-desc">{{ $featured->excerpt }}</div>
                </div>
                <div class="featured-arrow"><i class="ph ph-arrow-right"></i></div>
            </a>
            @endif
        </div>
    </section>

    {{-- ── GREEN BONDS Section ── --}}
    @foreach($articlesByCategory as $slug => $catArticles)
    @php $category = $categories->firstWhere('slug', $slug); @endphp
    @if($catArticles->isNotEmpty() && $category)

    <section style="margin-bottom:3rem;">
        <div class="section-header">
            @if($loop->odd)
            <h2 class="section-title"><span class="section-title-underline">{{ strtoupper($category->name) }}</span></h2>
            <div style="display:flex;align-items:center;gap:0.5rem;">
                <div class="section-nav-btns" x-data="carousel('#carousel-{{ $slug }}')">
                    <button class="section-nav-btn" @click="prev()"><i class="ph ph-caret-left"></i></button>
                    <button class="section-nav-btn" @click="next()"><i class="ph ph-caret-right"></i></button>
                </div>
            </div>
            @else
            <div style="display:flex;align-items:center;gap:0.5rem;" x-data="carousel('#carousel-{{ $slug }}')">
                <button class="section-nav-btn" @click="prev()"><i class="ph ph-caret-left"></i></button>
                <button class="section-nav-btn" @click="next()"><i class="ph ph-caret-right"></i></button>
            </div>
            <h2 class="section-title"><span class="section-title-underline">{{ strtoupper($category->name) }}</span></h2>
            @endif
        </div>

        <div id="carousel-{{ $slug }}" class="articles-grid" style="overflow-x:auto;scroll-snap-type:x mandatory;">
            @foreach($catArticles as $article)
            <a href="{{ route('user.articles.show', $article->slug) }}" class="article-card" style="scroll-snap-align:start;">
                <img src="{{ $article->cover_url }}" alt="{{ $article->title }}" class="article-card-img">
                <div class="article-card-body">
                    <span class="category-pill pill-{{ $article->category->slug }}" style="margin-bottom:0.5rem;display:inline-block;">
                        {{ $article->category->name }}
                    </span>
                    <div class="article-card-title">{{ $article->title }}</div>
                    <div class="article-card-desc">{{ $article->excerpt }}</div>
                </div>
            </a>
            @endforeach
        </div>
    </section>

    @endif
    @endforeach

</div>
@endsection
