@extends('layouts.admin')
@section('title', 'Kelola Komentar')

@section('content')
<div x-data="commentModal()">

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

<div style="margin-bottom:0.25rem;" class="section-label">EDITORIAL CONTROLS</div>
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1.25rem;flex-wrap:wrap;gap:1rem;">
    <div>
        <h1 class="page-title">Kelola Komentar</h1>
        <p style="font-size:0.875rem;color:#6B7280;margin-top:0.25rem;max-width:480px;">
            Moderasi interaksi komunitas pada artikel edukasi. Pantau kualitas diskusi dan cegah aktivitas yang melanggar panduan komunitas.
        </p>
    </div>
    <a href="{{ route('admin.comments.export') }}"
       style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.6875rem 1.125rem;background:#F9FAFB;color:#374151;font-size:0.875rem;font-weight:600;border:1.5px solid #E5E7EB;border-radius:10px;text-decoration:none;white-space:nowrap;">
        <i class="ph ph-export"></i> Export Log
    </a>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.comments.index') }}"
      style="display:flex;gap:0.75rem;margin-bottom:1.25rem;flex-wrap:wrap;align-items:flex-end;">
    <div style="flex:1;min-width:200px;">
        <label style="display:block;font-size:0.75rem;font-weight:600;color:#6B7280;margin-bottom:0.375rem;">SEARCH KOMENTAR</label>
        <div style="position:relative;">
            <i class="ph ph-magnifying-glass" style="position:absolute;left:0.75rem;top:50%;transform:translateY(-50%);color:#9CA3AF;"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nama, email, isi..."
                   style="width:100%;padding:0.5625rem 0.875rem 0.5625rem 2.25rem;background:#fff;border:1.5px solid #E5E7EB;border-radius:10px;font-size:0.875rem;outline:none;">
        </div>
    </div>
    <div style="min-width:140px;">
        <label style="display:block;font-size:0.75rem;font-weight:600;color:#6B7280;margin-bottom:0.375rem;">STATUS</label>
        <select name="status" style="width:100%;padding:0.5625rem 0.875rem;background:#fff;border:1.5px solid #E5E7EB;border-radius:10px;font-size:0.875rem;outline:none;cursor:pointer;">
            <option value="">Semua Status</option>
            <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
    </div>
    <div style="min-width:160px;">
        <label style="display:block;font-size:0.75rem;font-weight:600;color:#6B7280;margin-bottom:0.375rem;">KATEGORI</label>
        <select name="category" style="width:100%;padding:0.5625rem 0.875rem;background:#fff;border:1.5px solid #E5E7EB;border-radius:10px;font-size:0.875rem;outline:none;cursor:pointer;">
            <option value="">Semua Kategori</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" style="padding:0.5625rem 1rem;background:#1B4332;color:#fff;font-size:0.875rem;font-weight:600;border:none;border-radius:10px;cursor:pointer;">
        Filter
    </button>
    @if(request('search') || request('status') || request('category'))
    <a href="{{ route('admin.comments.index') }}"
       style="padding:0.5625rem 1rem;background:#F3F4F6;color:#6B7280;font-size:0.875rem;border-radius:10px;text-decoration:none;display:flex;align-items:center;gap:0.25rem;">
        <i class="ph ph-x"></i> Clear Filters
    </a>
    @endif
</form>

{{-- Table --}}
<div class="table-wrap">
    <div class="table-head" style="grid-template-columns:2fr 3fr 1.5fr 1fr 80px 80px 60px;">
        <div>NAMA PENGGUNA</div>
        <div>KOMENTAR</div>
        <div>ARTIKEL</div>
        <div>WAKTU</div>
        <div>EMAIL</div>
        <div>STATUS</div>
        <div>AKSI</div>
    </div>

    @forelse($comments as $comment)
    <div class="table-row" style="grid-template-columns:2fr 3fr 1.5fr 1fr 80px 80px 60px;">
        {{-- User --}}
        <div style="display:flex;align-items:center;gap:0.625rem;">
            <div style="width:32px;height:32px;background:#1B4332;color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;flex-shrink:0;">
                {{ $comment->initials }}
            </div>
            <span style="font-size:0.875rem;font-weight:600;color:#111827;">{{ $comment->user_name }}</span>
        </div>

        {{-- Comment excerpt --}}
        <div style="font-size:0.8125rem;color:#6B7280;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
            "{{ Str::limit($comment->content, 60) }}"
        </div>

        {{-- Article --}}
        <div style="font-size:0.8125rem;color:#374151;">
            @if($comment->article)
            <a href="{{ route('admin.articles.preview', $comment->article->slug) }}"
               style="color:#40916C;text-decoration:none;display:flex;align-items:center;gap:0.25rem;">
                <i class="ph {{ $comment->article->category->icon }}" style="font-size:0.875rem;"></i>
                {{ Str::limit($comment->article->title, 25) }}
            </a>
            @endif
        </div>

        {{-- Time --}}
        <div style="font-size:0.75rem;color:#9CA3AF;">{{ $comment->created_at->diffForHumans() }}</div>

        {{-- Email --}}
        <div style="font-size:0.75rem;color:#9CA3AF;overflow:hidden;text-overflow:ellipsis;">
            {{ $comment->user_email ? Str::limit($comment->user_email, 18) : '—' }}
        </div>

        {{-- Status badge --}}
        <div>
            <span class="badge" style="{{ match($comment->status) {
                'approved'     => 'background:#D1E7DD;color:#0F5132',
                'rejected'     => 'background:#F8D7DA;color:#842029',
                'shadowbanned' => 'background:#F3F4F6;color:#6B7280',
                default        => 'background:#FFF3CD;color:#856404',
            } }}">
                {{ $comment->status_badge['label'] }}
            </span>
        </div>

        {{-- Open modal --}}
        <div>
            <button
                style="padding:0.375rem 0.625rem;background:#F9FAFB;border:1px solid #E5E7EB;border-radius:8px;font-size:0.75rem;font-weight:600;cursor:pointer;color:#374151;"
                @click="show({
                    id: {{ $comment->id }},
                    user_name: '{{ addslashes($comment->user_name) }}',
                    user_email: '{{ $comment->user_email }}',
                    content: `{{ addslashes($comment->content) }}`,
                    admin_reply: `{{ addslashes($comment->admin_reply ?? '') }}`,
                    ip_address: '{{ $comment->ip_address }}',
                    user_agent: '{{ addslashes(Str::limit($comment->user_agent ?? '', 30)) }}',
                    status: '{{ $comment->status }}',
                    article_title: '{{ addslashes($comment->article?->title ?? '') }}',
                    article_slug: '{{ $comment->article?->slug ?? '' }}',
                    article_cover: '{{ $comment->article?->cover_url ?? '' }}',
                    created_at: '{{ $comment->created_at->diffForHumans() }}'
                })">
                Detail
            </button>
        </div>
    </div>
    @empty
    <div style="padding:3rem;text-align:center;color:#9CA3AF;">
        <i class="ph ph-chat-circle" style="font-size:2.5rem;display:block;margin-bottom:0.75rem;opacity:0.4;"></i>
        <p style="font-size:0.875rem;">Tidak ada komentar ditemukan.</p>
    </div>
    @endforelse
</div>

{{-- Pagination --}}
<div style="margin-top:1.25rem;display:flex;align-items:center;justify-content:space-between;font-size:0.8125rem;color:#6B7280;">
    <span>Menampilkan {{ $comments->firstItem() ?? 0 }}–{{ $comments->lastItem() ?? 0 }} dari {{ $comments->total() }} komentar</span>
    <div style="display:flex;gap:0.375rem;">
        @if(!$comments->onFirstPage())
            <a href="{{ $comments->previousPageUrl() }}" class="page-btn"><i class="ph ph-caret-left"></i></a>
        @endif
        @foreach($comments->getUrlRange(max(1,$comments->currentPage()-2), min($comments->lastPage(),$comments->currentPage()+2)) as $page => $url)
            <a href="{{ $url }}" class="page-btn {{ $page == $comments->currentPage() ? 'active' : '' }}">{{ $page }}</a>
        @endforeach
        @if($comments->hasMorePages())
            <a href="{{ $comments->nextPageUrl() }}" class="page-btn"><i class="ph ph-caret-right"></i></a>
        @endif
    </div>
</div>

{{-- ── Comment Detail Modal ── --}}
<div x-show="open" x-cloak class="modal-backdrop" @click.self="close()">
    <div class="comment-modal modal-card" @click.stop style="max-width:620px;padding:0;text-align:left;overflow:hidden;">

        {{-- Article Banner --}}
        <div class="comment-modal-banner">
            <img :src="comment?.article_cover || '/images/default-cover.jpg'"
                 class="comment-modal-cover" alt="Cover">
            <div>
                <h3 style="font-size:1.125rem;font-weight:700;color:#111827;line-height:1.3;margin-bottom:0.375rem;" x-text="comment?.article_title"></h3>
                <a :href="comment?.article_slug ? '/artikel/'+comment.article_slug : '#'"
                   target="_blank"
                   style="font-size:0.8125rem;color:#40916C;text-decoration:none;display:inline-flex;align-items:center;gap:0.25rem;">
                    Buka Preview <i class="ph ph-arrow-square-out"></i>
                </a>
                <span style="font-size:0.75rem;color:#9CA3AF;margin-left:0.75rem;">Editorial Archive ID: #REST-2024-MV</span>
            </div>
            <button @click="close()" style="position:absolute;top:1rem;right:1rem;background:none;border:none;cursor:pointer;font-size:1.125rem;color:#9CA3AF;">
                <i class="ph ph-x"></i>
            </button>
        </div>

        {{-- Action Buttons --}}
        <div class="comment-modal-actions-bar">
            <button class="btn-approve" @click="doAction('approve')" :disabled="loading">
                <i class="ph ph-check-circle"></i> Approve
            </button>
            <button class="btn-reject" @click="doAction('reject')" :disabled="loading">
                <i class="ph ph-x-circle"></i> Reject
            </button>
            <div style="margin-left:auto;display:flex;gap:0.5rem;">
                <button class="btn-text-action danger" @click="doAction('device-ban')" :disabled="loading"
                        style="background:#FEF2F2;color:#EF4444;border:1px solid #FECACA;border-radius:8px;padding:0.375rem 0.75rem;">
                    <i class="ph ph-prohibit"></i> Device Ban
                </button>
                <button class="btn-text-action" @click="doAction('undevice-ban')" :disabled="loading"
                        style="background:#F0FDF4;color:#16A34A;border:1px solid #BBF7D0;border-radius:8px;padding:0.375rem 0.75rem;">
                    <i class="ph ph-shield-check"></i> Undevice Ban
                </button>
            </div>
        </div>

        {{-- Comment Body --}}
        <div class="comment-body">
            <div class="commenter-info">
                <div class="commenter-avatar" x-text="comment?.user_name?.slice(0,2).toUpperCase()"></div>
                <div>
                    <div>
                        <span class="commenter-name" x-text="comment?.user_name"></span>
                        <span class="commenter-badge">TOP CONTRIBUTOR</span>
                    </div>
                    <div class="commenter-sub" x-text="(comment?.created_at ?? '') + ' • Terverifikasi'"></div>
                </div>
            </div>

            <div class="comment-content" x-text="comment?.content"></div>

            <div class="comment-meta-bar">
                <span><i class="ph ph-envelope-simple"></i> <span x-text="comment?.user_email || '—'"></span></span>
                <span><i class="ph ph-globe"></i> <span x-text="comment?.ip_address || '—'"></span></span>
                <span><i class="ph ph-monitor"></i> <span x-text="comment?.user_agent || '—'"></span></span>
            </div>
        </div>

        {{-- Admin Reply --}}
        <div class="admin-reply-section">
            <div class="admin-reply-header">
                <span class="admin-reply-label">BALAS SEBAGAI ADMIN</span>
                <span class="admin-badge"><i class="ph ph-shield-check"></i> GREENVEST TEAM</span>
            </div>
            <textarea class="reply-textarea" x-model="reply" placeholder="Tulis balasan resmi Anda di sini..."></textarea>
            <div style="display:flex;justify-content:flex-end;">
                <button class="btn-send" @click="sendReply()" :disabled="loading">
                    Kirim Balasan <i class="ph ph-paper-plane-tilt"></i>
                </button>
            </div>
        </div>
    </div>
</div>

</div>
@endsection
