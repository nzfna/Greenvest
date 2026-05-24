@extends('layouts.admin')
@section('title', 'Kelola Artikel')

@section('content')
<div x-data="deleteModal()">

{{-- Loading Screen --}}
<div id="page-loading" style="
    position:fixed;top:0;left:0;width:100%;height:100%;
    background:rgba(255,255,255,0.85);
    display:flex;flex-direction:column;
    align-items:center;justify-content:center;
    z-index:100;transition:opacity 0.5s ease;">
    <img src="/images/favicon.svg" style="width:52px;height:52px;margin-bottom:1rem;">
    <p style="font-size:1rem;font-weight:700;color:#1B4332;margin-bottom:1.25rem;">GREENVEST.CO</p>
    <div style="width:40px;height:40px;border:4px solid #D1FAE5;border-top-color:#1B4332;border-radius:50%;animation:spin 0.8s linear infinite;"></div>
</div>
<style>@keyframes spin { to { transform:rotate(360deg); } }</style>
<script>
window.addEventListener('load', function() {
    setTimeout(function() {
        var el = document.getElementById('page-loading');
        if(el) { el.style.opacity='0'; setTimeout(function(){ el.style.display='none'; },500); }
    }, 800);
});
</script>

{{-- Header --}}
<div style="margin-bottom:0.25rem;" class="section-label">ARSIP EKOLOGI</div>
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1.5rem;gap:1rem;flex-wrap:wrap;">
    <div>
        <h1 class="page-title">Kelola Artikel</h1>
        <p style="font-size:0.875rem;color:#6B7280;margin-top:0.25rem;border-left:3px solid #E5E7EB;padding-left:0.75rem;max-width:540px;">
            Penyimpanan pusat untuk seluruh publikasi digital Verdavest. Gunakan antarmuka ledger ini untuk memvalidasi, menyunting, dan menerbitkan laporan keberlanjutan terbaru.
        </p>
    </div>
    <a href="{{ route('admin.articles.create') }}"
       style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.6875rem 1.25rem;background:#1B4332;color:#fff;font-size:0.875rem;font-weight:600;border-radius:10px;text-decoration:none;white-space:nowrap;flex-shrink:0;">
        <i class="ph ph-plus"></i> Tambah Artikel
    </a>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.articles.index') }}"
      style="display:flex;gap:0.75rem;margin-bottom:1.25rem;flex-wrap:wrap;">
    <div style="position:relative;flex:1;min-width:200px;">
        <i class="ph ph-magnifying-glass" style="position:absolute;left:0.75rem;top:50%;transform:translateY(-50%);color:#9CA3AF;"></i>
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari judul artikel..."
               style="width:100%;padding:0.5625rem 0.875rem 0.5625rem 2.25rem;background:#fff;border:1.5px solid #E5E7EB;border-radius:10px;font-size:0.875rem;outline:none;">
    </div>
    <select name="category"
            style="padding:0.5625rem 0.875rem;background:#fff;border:1.5px solid #E5E7EB;border-radius:10px;font-size:0.875rem;outline:none;cursor:pointer;min-width:160px;">
        <option value="">Semua Kategori</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                {{ $cat->name }}
            </option>
        @endforeach
    </select>
    <button type="submit"
            style="padding:0.5625rem 1rem;background:#1B4332;color:#fff;font-size:0.875rem;font-weight:600;border:none;border-radius:10px;cursor:pointer;">
        Cari
    </button>
    @if(request('search') || request('category'))
    <a href="{{ route('admin.articles.index') }}"
       style="padding:0.5625rem 1rem;background:#F3F4F6;color:#6B7280;font-size:0.875rem;font-weight:500;border-radius:10px;text-decoration:none;display:flex;align-items:center;gap:0.25rem;">
        <i class="ph ph-x"></i> Reset
    </a>
    @endif
</form>

{{-- Table --}}
<div class="table-wrap articles-table">
    <div class="table-head">
        <div>JUDUL ARTIKEL</div>
        <div>PENULIS</div>
        <div>TANGGAL RILIS</div>
        <div>REVISI</div>
        <div style="text-align:right;">TINDAKAN</div>
    </div>

    @forelse($articles as $article)
    <div class="table-row">
        {{-- Title + category + tags --}}
        <div style="min-width:0;">
            <div style="display:flex;align-items:center;gap:0.625rem;margin-bottom:0.25rem;">
                <div class="article-row-icon {{ $article->category->slug }}" style="width:28px;height:28px;flex-shrink:0;">
                    <i class="ph {{ $article->category->icon }}" style="font-size:0.875rem;"></i>
                </div>
                <a href="{{ route('admin.articles.preview', $article->slug) }}"
                   style="font-size:0.875rem;font-weight:700;color:#111827;text-decoration:none;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:280px;display:block;"
                   title="{{ $article->title }}">
                    {{ $article->title }}
                </a>
            </div>
            <div style="margin-left:calc(28px + 0.625rem);display:flex;gap:0.375rem;flex-wrap:wrap;">
                <span class="badge badge-gray">{{ $article->category->name }}</span>
            </div>
        </div>

        {{-- Author --}}
        <div style="font-size:0.8125rem;color:#6B7280;display:flex;align-items:center;gap:0.375rem;">
            <i class="ph ph-user-circle" style="color:#9CA3AF;font-size:0.9375rem;"></i>
            {{ $article->author->name ?? 'Anonim' }}
        </div>

        {{-- Date --}}
        <div style="font-size:0.8125rem;color:#6B7280;">
            {{ $article->published_at?->format('d M Y') ?? $article->created_at->format('d M Y') }}
        </div>

        {{-- Revision badge --}}
        <div>
            <span style="display:inline-flex;align-items:center;padding:0.1875rem 0.625rem;background:#EFF6FF;color:#3B82F6;font-size:0.6875rem;font-weight:700;border-radius:999px;">
                {{ $article->revision_count }}x
            </span>
        </div>

        {{-- Actions --}}
        <div style="display:flex;align-items:center;gap:0.375rem;justify-content:flex-end;">
            <a href="{{ route('admin.articles.preview', $article->slug) }}"
               class="action-btn action-btn-view" title="Preview">
                <i class="ph ph-eye"></i>
            </a>
            <a href="{{ route('admin.articles.edit', $article) }}"
               class="action-btn action-btn-edit" title="Sunting">
                <i class="ph ph-pencil"></i>
            </a>
            <button class="action-btn action-btn-delete" title="Hapus"
                    @click="show({{ $article->id }}, '{{ addslashes($article->title) }}')">
                <i class="ph ph-trash"></i>
            </button>
        </div>
    </div>
    @empty
    <div style="padding:3rem;text-align:center;color:#9CA3AF;">
        <i class="ph ph-file-text" style="font-size:2.5rem;display:block;margin-bottom:0.75rem;opacity:0.4;"></i>
        <p style="font-size:0.875rem;">Belum ada artikel ditemukan.</p>
        <a href="{{ route('admin.articles.create') }}"
           style="display:inline-flex;align-items:center;gap:0.25rem;margin-top:0.75rem;font-size:0.875rem;font-weight:600;color:#1B4332;text-decoration:none;">
            <i class="ph ph-plus"></i> Buat artikel pertama
        </a>
    </div>
    @endforelse
</div>

{{-- Pagination --}}
<div style="margin-top:1.25rem;display:flex;align-items:center;justify-content:space-between;font-size:0.8125rem;color:#6B7280;">
    <span>Menampilkan {{ $articles->firstItem() }}–{{ $articles->lastItem() }} dari {{ $articles->total() }} artikel</span>
    <div style="display:flex;gap:0.375rem;">
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
</div>

{{-- Delete Modal --}}
<div x-show="open" x-cloak class="modal-backdrop" @click.self="open=false">
    <div class="modal-card" @click.stop>
        <div class="modal-icon-wrap">
            <i class="ph ph-warning" style="font-size:1.75rem;color:#EF4444;"></i>
        </div>
        <h2 class="modal-title">Hapus Artikel?</h2>
        <p class="modal-desc">Apakah kamu yakin ingin menghapus artikel ini? Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait dari arsip publik.</p>
        <div class="modal-target">
            <div class="modal-target-label">ARTIKEL TARGET</div>
            <div class="modal-target-value" x-text="articleTitle"></div>
        </div>
        <div class="modal-actions">
            <button class="btn-cancel" @click="open=false">BATAL</button>
            <button class="btn-danger" @click="confirm()">YA, HAPUS</button>
        </div>
    </div>
</div>

</div>
@endsection
