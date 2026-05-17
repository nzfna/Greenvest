@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<div style="margin-bottom:0.25rem;" class="section-label">RINGKASAN EKOSISTEM</div>
<h1 class="page-title" style="margin-bottom:2rem;">Dashboard Admin</h1>

{{-- ── Aktivitas Terkini (3 cards) ── --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
    <h2 style="font-size:1rem;font-weight:700;color:#374151;">Aktivitas Terkini</h2>
    <a href="{{ route('admin.logs') }}" class="panel-link">Lihat Semua</a>
</div>

<div class="activity-grid" style="margin-bottom:2rem;">
    @forelse($recentActivities as $log)
    <div class="activity-card">
        <div class="activity-meta">
            <span class="activity-type-badge">
                {{ match($log->entity_type) {
                    'article' => 'BARU SAJA',
                    'comment' => 'KOMENTAR',
                    default   => strtoupper($log->action),
                } }}
            </span>
            <span class="activity-time">{{ $log->created_at->diffForHumans() }}</span>
        </div>
        <div class="activity-card-title">
            {{ match($log->action) {
                'create_article'  => 'Artikel Baru',
                'update_article'  => 'Pembaruan Berhasil',
                'delete_article'  => 'Artikel Dihapus',
                'approve_comment' => 'Komentar Disetujui',
                'reject_comment'  => 'Komentar Ditolak',
                'shadowban'       => 'Moderasi Komentar',
                'login'           => 'Admin Login',
                'update_profile'  => 'Profil Diperbarui',
                'change_password' => 'Password Diubah',
                default           => ucwords(str_replace('_', ' ', $log->action)),
            } }}
            @if($log->action === 'approve_comment' || $log->action === 'reject_comment' || $log->action === 'shadowban')
                <span style="display:inline-block;width:7px;height:7px;background:#EF4444;border-radius:50%;margin-left:4px;vertical-align:middle;"></span>
            @endif
        </div>
        <div class="activity-card-desc">"{{ Str::limit($log->description, 45) }}"</div>
        <div class="activity-card-footer">
            <span class="activity-card-actor">{{ $log->user?->name ?? 'SYSTEM' }}</span>
            <i class="ph {{ $log->icon }}" style="color:#9CA3AF;font-size:0.875rem;"></i>
        </div>
    </div>
    @empty
    <div class="activity-card" style="grid-column:1/-1;text-align:center;color:#9CA3AF;font-size:0.875rem;">
        <i class="ph ph-activity" style="font-size:2rem;margin-bottom:0.5rem;display:block;opacity:0.4;"></i>
        Belum ada aktivitas.
    </div>
    @endforelse
</div>

{{-- ── Artikel & Komentar Terbaru ── --}}
<div class="dashboard-cols">

    {{-- Artikel Terbaru --}}
    <div class="panel">
        <div class="panel-header">
            <h3 class="panel-title">Artikel Terbaru</h3>
            <a href="{{ route('admin.articles.index') }}" class="panel-link">Lihat Semua</a>
        </div>
        @forelse($latestArticles as $article)
        <div class="article-row">
            <div class="article-row-icon {{ $article->category->slug }}">
                <i class="ph {{ $article->category->icon }}"></i>
            </div>
            <div class="article-row-info">
                <div class="article-row-title">{{ $article->title }}</div>
                <div class="article-row-meta">
                    {{ $article->published_at?->format('d M Y') }} · {{ $article->author->name ?? 'Anonim' }}
                </div>
            </div>
            <a href="{{ route('admin.articles.preview', $article->slug) }}"
               style="color:#9CA3AF;font-size:0.75rem;text-decoration:none;flex-shrink:0;">
               <i class="ph ph-arrow-right"></i>
            </a>
        </div>
        @empty
        <div style="padding:2rem;text-align:center;color:#9CA3AF;font-size:0.875rem;">Belum ada artikel.</div>
        @endforelse
    </div>

    {{-- Komentar Terbaru --}}
    <div class="panel">
        <div class="panel-header">
            <h3 class="panel-title">Komentar Terbaru</h3>
            <a href="{{ route('admin.comments.index') }}" class="panel-link">Lihat Semua</a>
        </div>
        @forelse($recentComments as $comment)
        <div class="comment-row">
            <div class="comment-row-header">
                <span class="comment-user">{{ strtoupper($comment->user_name) }}</span>
                <span class="comment-time">{{ $comment->created_at->diffForHumans() }}</span>
            </div>
            <div class="comment-text">"{{ Str::limit($comment->content, 50) }}"</div>
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <span class="comment-article-tag">
                    <i class="ph {{ $comment->article?->category->icon ?? 'ph-file-text' }}"></i>
                    {{ strtoupper(Str::limit($comment->article?->title ?? '', 20)) }}
                </span>
                <button class="btn-reply-sm"
                        onclick="window.location.href='{{ route('admin.comments.index') }}'">
                    BALAS
                </button>
            </div>
        </div>
        @empty
        <div style="padding:2rem;text-align:center;color:#9CA3AF;font-size:0.875rem;">Belum ada komentar.</div>
        @endforelse
    </div>
</div>
@endsection
