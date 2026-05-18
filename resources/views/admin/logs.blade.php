@extends('layouts.admin')
@section('title', 'Log Aktivitas')

@section('content')
<div style="margin-bottom:0.25rem;" class="section-label">ARCHIVAL RECORDS</div>
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
    <h1 class="page-title">Riwayat Aktivitas</h1>
    <div style="display:flex;gap:0.625rem;">
        <form method="GET" action="{{ route('admin.logs.index') }}"
              style="display:flex;gap:0.5rem;align-items:center;">
            <select name="action"
                    style="padding:0.5rem 0.875rem;background:#fff;border:1.5px solid #E5E7EB;border-radius:8px;font-size:0.875rem;outline:none;cursor:pointer;">
                <option value="">Semua Aksi</option>
                @foreach($actions as $action)
                    <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>
                        {{ str_replace('_', ' ', ucfirst($action)) }}
                    </option>
                @endforeach
            </select>
            <input type="date" name="from" value="{{ request('from') }}"
                   style="padding:0.5rem 0.875rem;background:#fff;border:1.5px solid #E5E7EB;border-radius:8px;font-size:0.875rem;outline:none;cursor:pointer;">
            <input type="date" name="to" value="{{ request('to') }}"
                   style="padding:0.5rem 0.875rem;background:#fff;border:1.5px solid #E5E7EB;border-radius:8px;font-size:0.875rem;outline:none;cursor:pointer;">
            <button type="submit"
                    style="padding:0.5rem 0.875rem;background:#1B4332;color:#fff;font-size:0.875rem;font-weight:600;border:none;border-radius:8px;cursor:pointer;">
                <i class="ph ph-funnel"></i> Saring Data
            </button>
        </form>
        <a href="{{ route('admin.logs.export') }}"
           style="display:inline-flex;align-items:center;gap:0.375rem;padding:0.5rem 0.875rem;background:#8B6B1B;color:#fff;font-size:0.875rem;font-weight:600;border-radius:8px;text-decoration:none;">
            <i class="ph ph-download-simple"></i> Ekspor Log
        </a>
    </div>
</div>

<div class="table-wrap">
    <div style="display:grid;grid-template-columns:36px 1fr 180px 110px;gap:1rem;padding:0.75rem 1.5rem;background:#1B4332;color:rgba(255,255,255,0.7);font-size:0.6875rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">
        <div></div>
        <div>AKSI</div>
        <div>WAKTU</div>
        <div>STATUS</div>
    </div>

    @forelse($logs as $log)
    <div class="log-row">
        {{-- Icon --}}
        <div class="log-icon">
            <i class="ph {{ $log->icon }}"></i>
        </div>

        {{-- Description --}}
        <div>
            <div style="font-size:0.875rem;font-weight:600;color:#111827;margin-bottom:0.125rem;">
                {{ ucwords(str_replace('_',' ',$log->action)) }}
            </div>
            <div style="font-size:0.8125rem;color:#9CA3AF;">{{ $log->description ?? '—' }}</div>
        </div>

        {{-- Time --}}
        <div>
            <div style="font-size:0.875rem;color:#374151;">{{ $log->created_at->format('d M Y') }}</div>
            <div style="font-size:0.75rem;color:#9CA3AF;">{{ $log->created_at->format('H:i') }} WIB</div>
        </div>

        {{-- Status --}}
        <div>
            <span class="badge" style="{{ match($log->status_label['class']) {
                default => 'background:#D1E7DD;color:#0F5132'
            } }}; background:{{ str_contains($log->status_label['class'],'success') ? '#D1E7DD' : (str_contains($log->status_label['class'],'warning') ? '#FFF3CD' : (str_contains($log->status_label['class'],'danger') ? '#F8D7DA' : '#F3F4F6')) }};color:{{ str_contains($log->status_label['class'],'success') ? '#0F5132' : (str_contains($log->status_label['class'],'warning') ? '#856404' : (str_contains($log->status_label['class'],'danger') ? '#842029' : '#6B7280')) }};">
                {{ $log->status_label['label'] }}
            </span>
        </div>
    </div>
    @empty
    <div style="padding:3rem;text-align:center;color:#9CA3AF;">
        <i class="ph ph-clock-counter-clockwise" style="font-size:2.5rem;display:block;margin-bottom:0.75rem;opacity:0.4;"></i>
        <p style="font-size:0.875rem;">Tidak ada log aktivitas.</p>
    </div>
    @endforelse
</div>

{{-- Pagination --}}
<div style="margin-top:1.25rem;display:flex;align-items:center;justify-content:space-between;font-size:0.8125rem;color:#6B7280;">
    <span>Menampilkan 1–{{ $logs->count() }} dari {{ $logs->total() }} catatan</span>
    <div style="display:flex;gap:0.375rem;">
        @if(!$logs->onFirstPage())
            <a href="{{ $logs->previousPageUrl() }}" class="page-btn"><i class="ph ph-caret-left"></i></a>
        @endif
        @foreach($logs->getUrlRange(max(1,$logs->currentPage()-2), min($logs->lastPage(),$logs->currentPage()+2)) as $page => $url)
            <a href="{{ $url }}" class="page-btn {{ $page == $logs->currentPage() ? 'active' : '' }}">{{ $page }}</a>
        @endforeach
        @if($logs->hasMorePages())
            <a href="{{ $logs->nextPageUrl() }}" class="page-btn"><i class="ph ph-caret-right"></i></a>
        @endif
    </div>
</div>
@endsection
