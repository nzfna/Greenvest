@extends('layouts.user')
@section('title', $article->title . ' — Greenvest')
@section('meta_desc', $article->excerpt)

@section('content')
<div class="page-wrap">

    {{-- Breadcrumb --}}
    <nav class="article-breadcrumb">
        <a href="{{ route('user.home') }}">Beranda</a>
        <i class="ph ph-caret-right" style="font-size:0.75rem;"></i>
        <a href="{{ route('user.articles.index') }}">Artikel</a>
        <i class="ph ph-caret-right" style="font-size:0.75rem;"></i>
        <a href="{{ route('user.articles.index', ['category' => $article->category->slug]) }}">
            {{ $article->category->name }}
        </a>
        <i class="ph ph-caret-right" style="font-size:0.75rem;"></i>
        <span style="color:#374151;">{{ Str::limit($article->title, 40) }}</span>
    </nav>

    <div class="article-detail-wrap">
        {{-- ── Main Content ── --}}
        <main>
            {{-- Category + read time --}}
            <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.875rem;">
                <span class="category-pill pill-{{ $article->category->slug }}">
                    {{ $article->category->name }}
                </span>
                <span style="font-size:0.8125rem;color:#9CA3AF;">{{ $article->read_time }} Menit</span>
            </div>

            {{-- Title --}}
            <h1 class="article-main-title">{{ $article->title }}</h1>

            {{-- Cover --}}
            @if($article->cover_image)
                <img src="{{ $article->cover_url }}" alt="{{ $article->title }}" class="article-hero-img">
            @endif

            {{-- Content --}}
            <div class="article-prose">
                {!! $article->content !!}
            </div>

            {{-- Share bar --}}
            <div class="share-bar" x-data="shareBar()">
                <span class="share-label">Bagikan:</span>
                <button class="share-btn" @click="copy()" title="Salin link">
                    <i :class="copied ? 'ph ph-check' : 'ph ph-link'"></i>
                </button>
                <a href="https://wa.me/?text={{ urlencode($article->title . ' — ' . request()->url()) }}"
                   target="_blank" class="share-btn" title="WhatsApp">
                    <i class="ph ph-whatsapp-logo"></i>
                </a>
                <a href="https://twitter.com/intent/tweet?text={{ urlencode($article->title) }}&url={{ urlencode(request()->url()) }}"
                   target="_blank" class="share-btn" title="Twitter">
                    <i class="ph ph-twitter-logo"></i>
                </a>
            </div>

            {{-- ── Comments ── --}}
            <div class="comments-section">
                <h3 class="comments-title">Diskusi &amp; Komentar</h3>

                {{-- Comment form --}}
                <div class="comment-form" x-data="commentForm('{{ $article->slug }}')">

                    <div x-show="success" x-cloak
                         style="margin-bottom:1rem;padding:0.875rem;background:#D1E7DD;border-radius:10px;font-size:0.875rem;color:#0F5132;display:flex;gap:0.5rem;">
                        <i class="ph ph-check-circle"></i>
                        Komentar berhasil dikirim! Komentar kamu langsung tampil.
                    </div>

                    <div x-show="error" x-cloak
     style="margin-bottom:1rem;padding:0.875rem;background:#FEF2F2;border-radius:10px;font-size:0.875rem;color:#B91C1C;">
    <div style="display:flex;align-items:center;gap:0.5rem;font-weight:600;">
        <i class="ph ph-prohibit"></i>
        <span x-text="error"></span>
    </div>
    <div x-show="banReason" x-cloak style="margin-top:0.375rem;font-size:0.8125rem;">
        Alasan: <span x-text="banReason"></span>
    </div>
</div>

                    <div style="font-size:0.9375rem;font-weight:700;color:#374151;margin-bottom:1rem;">Tinggalkan Komentar</div>

                    <div class="comment-form-row">
                        <div>
                            <label style="display:block;font-size:0.75rem;font-weight:600;color:#6B7280;margin-bottom:0.375rem;">Nama Panggilan *</label>
                            <input type="text" x-model="name" placeholder="Contoh: Budi Hijau"
                                   class="comment-input"
                                   :style="errors.user_name ? 'border-color:#EF4444' : ''">
                            <p x-show="errors.user_name" x-cloak style="font-size:0.75rem;color:#EF4444;margin-top:0.25rem;" x-text="errors.user_name?.[0]"></p>
                        </div>
                        <div>
                            <label style="display:block;font-size:0.75rem;font-weight:600;color:#6B7280;margin-bottom:0.375rem;">Email (Opsional)</label>
                            <input type="email" x-model="email" placeholder="email@contoh.com"
                                   class="comment-input">
                        </div>
                    </div>

                    <div>
                        <label style="display:block;font-size:0.75rem;font-weight:600;color:#6B7280;margin-bottom:0.375rem;">Komentar</label>
                        <textarea x-model="content" rows="4"
                                  placeholder="Tuliskan pendapat atau pertanyaan Anda di sini..."
                                  class="comment-textarea"
                                  :style="errors.content ? 'border-color:#EF4444' : ''"></textarea>
                        <p x-show="errors.content" x-cloak style="font-size:0.75rem;color:#EF4444;margin-top:-0.625rem;margin-bottom:0.625rem;" x-text="errors.content?.[0]"></p>
                    </div>

                    <div class="comment-form-footer">
                        <span class="comment-privacy-note">
                            <i class="ph ph-shield-check"></i>
                            Identitas Anda akan dilindungi untuk keamanan berikutnya.
                        </span>
                        <button type="button" class="btn-submit-comment"
                                @click="submit()" :disabled="loading">
                            <span x-show="!loading">Kirim Komentar</span>
                            <span x-show="loading">Mengirim...</span>
                        </button>
                    </div>
                </div>

                {{-- Approved comments list --}}
                @foreach($article->approvedComments as $comment)
                <div class="comment-item">
                    <div class="comment-avatar">{{ $comment->initials }}</div>
                    <div class="comment-bubble">
                        <div class="comment-bubble-header">
                            <span class="comment-bubble-name">{{ $comment->user_name }}</span>
                            <span class="comment-bubble-time">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="comment-bubble-text">{{ $comment->content }}</div>

                        @if($comment->admin_reply)
                        <div class="admin-reply-bubble">
                            <div class="admin-reply-bubble-tag">
                                <i class="ph ph-shield-check"></i> Greenvest Team
                            </div>
                            {{ $comment->admin_reply }}
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach

                @if($article->approvedComments->isEmpty())
                <p style="font-size:0.875rem;color:#9CA3AF;text-align:center;padding:2rem 0;">
                    Jadilah yang pertama berkomentar!
                </p>
                @endif
            </div>
        </main>

        {{-- ── Sidebar ── --}}
        <aside class="article-sidebar">
            {{-- Progress widget --}}
            {{-- Progress Literasi Minggu Ini (localStorage) --}}
            <div class="sidebar-widget" x-data="literacyWidget()">
                <div class="sidebar-widget-title">Progress Literasi Minggu Ini</div>

                {{-- Pct display --}}
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.5rem;">
                    <span style="font-size:0.8125rem;color:#6B7280;" x-text="statusLabel"></span>
                    <span style="font-size:1.125rem;font-weight:800;color:#0D3B2E;" x-text="displayText"></span>
                </div>

                {{-- Progress bar --}}
                <div style="height:8px;background:#E5E7EB;border-radius:999px;overflow:hidden;margin-bottom:0.75rem;">
                    <div style="height:100%;border-radius:999px;transition:width 0.6s ease;"
                         :style="{ width: animatedPct + '%', background: barColor }"></div>
                </div>

                {{-- Petunjuk poin --}}
                <div style="font-size:0.6875rem;color:#9CA3AF;display:flex;flex-direction:column;gap:0.25rem;margin-bottom:0.875rem;">
                    <span>Baca artikel: <strong style="color:#374151;">+1%</strong></span>
                    <span>Tulis komentar: <strong style="color:#374151;">+3%</strong></span>
                    <span>Coba simulasi: <strong style="color:#374151;">+2%</strong></span>
                </div>

                <a href="{{ route('user.articles.index') }}" class="btn-continue">
                    Baca Artikel Lain <i class="ph ph-arrow-right"></i>
                </a>
            </div>


            {{-- Simulation CTA --}}
            <div class="sidebar-widget">
                <div style="font-size:0.875rem;font-weight:700;color:#0D3B2E;margin-bottom:0.375rem;">Siap berinvestasi?</div>
                <p style="font-size:0.8125rem;color:#6B7280;margin-bottom:0.875rem;line-height:1.5;">
                    Uji pengetahuan Anda dan lihat potensi dampak hijau yang bisa Anda hasilkan melalui alat simulasi kami.
                </p>
                <a href="{{ route('user.simulation') }}" class="btn-simulate">
                    Coba Simulasi
                </a>
            </div>

            {{-- Related articles --}}
            @if($related->isNotEmpty())
            <div class="sidebar-widget">
                <div class="sidebar-widget-title">ARTIKEL TERKAIT</div>
                @foreach($related as $rel)
                <a href="{{ route('user.articles.show', $rel->slug) }}" class="related-article-item">
                    <span class="related-tag">{{ $rel->category->name }} • {{ $rel->read_time }} Menit</span>
                    <span class="related-title">{{ $rel->title }}</span>
                </a>
                @endforeach
            </div>
            @endif
        </aside>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // +1% literacy: artikel ini sudah dibaca
    document.addEventListener('DOMContentLoaded', function () {
        window.literacyReadArticle('{{ $article->slug }}');
    });
</script>
@endpush
