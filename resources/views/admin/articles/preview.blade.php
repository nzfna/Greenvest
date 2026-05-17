@extends('layouts.admin')
@section('title', 'Preview Artikel')

@section('content')
<div style="font-size:0.8125rem;color:#9CA3AF;margin-bottom:1.25rem;">
    <a href="{{ route('admin.articles.index') }}" style="color:#6B7280;text-decoration:none;">KELOLA ARTIKEL</a>
    <span style="margin:0 0.375rem;">›</span>
    <span style="color:#111827;font-weight:600;">PREVIEW ARTIKEL</span>
</div>

<h1 class="page-title" style="margin-bottom:1.75rem;">Preview Artikel</h1>

<div style="max-width:720px;background:#fff;border:1px solid #F3F4F6;border-radius:20px;overflow:hidden;padding:2rem;">
    {{-- Category pill --}}
    <div style="text-align:center;margin-bottom:1rem;">
        <span style="display:inline-flex;padding:0.25rem 0.875rem;background:#E8F5E9;color:#2D6A4F;font-size:0.8125rem;font-weight:600;border-radius:999px;border:1px solid #D8F3DC;">
            {{ $article->category->name }}
        </span>
    </div>

    {{-- Title --}}
    <h2 style="font-size:1.75rem;font-weight:800;color:#0D3B2E;text-align:center;line-height:1.25;margin-bottom:1.25rem;letter-spacing:-0.02em;">
        {{ $article->title }}
    </h2>

    {{-- Cover Image --}}
    @if($article->cover_image)
    <img src="{{ $article->cover_url }}" alt="{{ $article->title }}"
         style="width:100%;height:320px;object-fit:cover;border-radius:12px;margin-bottom:1.5rem;">
    @endif

    {{-- Content --}}
    <div style="background:#F9FAFB;border-radius:12px;padding:1.5rem;font-size:0.9375rem;color:#374151;line-height:1.75;">
        {!! $article->content !!}
    </div>

    {{-- Meta --}}
    <div style="margin-top:1.25rem;padding-top:1.25rem;border-top:1px solid #F3F4F6;display:flex;align-items:center;justify-content:space-between;font-size:0.8125rem;color:#9CA3AF;">
        <span>Oleh {{ $article->author->name ?? 'Anonim' }}</span>
        <span>{{ $article->published_at?->format('d M Y') ?? 'Draft' }}</span>
    </div>

    {{-- Actions --}}
    <div style="margin-top:1.5rem;display:flex;gap:0.75rem;flex-wrap:wrap;">
        <a href="{{ route('admin.articles.edit', $article) }}"
           style="display:inline-flex;align-items:center;gap:0.375rem;padding:0.6875rem 1.25rem;background:#8B6B1B;color:#fff;font-size:0.875rem;font-weight:600;border-radius:10px;text-decoration:none;">
            <i class="ph ph-pencil"></i> Sunting Artikel
        </a>
        <a href="{{ route('user.articles.show', $article->slug) }}" target="_blank"
           style="display:inline-flex;align-items:center;gap:0.375rem;padding:0.6875rem 1.25rem;background:#F3F4F6;color:#374151;font-size:0.875rem;font-weight:600;border-radius:10px;text-decoration:none;">
            <i class="ph ph-arrow-square-out"></i> Lihat di Site
        </a>
    </div>
</div>
@endsection
