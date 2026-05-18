@extends('layouts.user')
@section('title', 'Simulasi Investasi')

@section('content')
<div class="simulation-page">
    <div class="page-wrap">

        {{-- Hero --}}
        <div class="simulation-hero">
            <h1 class="simulation-title">Simulasi Masa Depan Hijau</h1>
            <p class="simulation-sub">Rencanakan investasi berkelanjutan Anda dan lihat dampak positifnya bagi finansial serta bumi.</p>
        </div>

        {{-- Cards --}}
        <div class="simulation-card-wrap" x-data="simulation()">

            {{-- Form Card --}}
            <div class="simulation-form-card">
                <h2 style="font-size:1.0625rem;font-weight:700;color:#0D3B2E;margin-bottom:1.5rem;">Parameter Investasi</h2>

                {{-- Jenis Investasi --}}
                <div style="margin-bottom:1.25rem;">
                    <label class="sim-label">Jenis Investasi</label>
                    <select class="sim-select" x-model="typeId">
                        <option value="">Pilih jenis investasi...</option>
                        @foreach($simulationTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Modal Awal --}}
                <div style="margin-bottom:1.25rem;">
                    <label class="sim-label">Modal Awal</label>
                    <div class="sim-input-wrap">
                        <span class="sim-currency-prefix">Rp</span>
                        <input type="text" class="sim-input"
                               :value="Number(amount).toLocaleString('id-ID')"
                               @input="onAmountInput($event)"
                               placeholder="5.000.000">
                    </div>
                </div>

                {{-- Jangka Waktu --}}
                <div class="sim-slider-wrap">
                    <div class="sim-slider-header">
                        <label class="sim-label" style="margin-bottom:0;">Jangka Waktu</label>
                        <span class="sim-slider-val" x-text="duration + ' Tahun'"></span>
                    </div>
                    <input type="range" class="sim-range"
                           min="1" max="30" step="1"
                           x-model.number="duration">
                    <div class="sim-slider-limits">
                        <span>1 Tahun</span>
                        <span>30 Tahun</span>
                    </div>
                </div>

                <button type="button" class="btn-simulate-action"
                        @click="calculate()" :disabled="loading || !typeId || !amount">
                    <span x-show="!loading">
                        Hitung Simulasi <i class="ph ph-calculator"></i>
                    </span>
                    <span x-show="loading">Menghitung...</span>
                </button>
            </div>

            {{-- Result Card --}}
            <div class="simulation-result-card">
                <div class="result-header">
                    <h3 class="result-title">Hasil Simulasi</h3>
                    <div class="result-icon"><i class="ph ph-trend-up"></i></div>
                </div>

                {{-- Before calculation --}}
                <template x-if="!result">
                    <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;opacity:0.5;padding:2rem 0;">
                        <i class="ph ph-calculator" style="font-size:3rem;display:block;margin-bottom:0.75rem;color:rgba(255,255,255,0.4);"></i>
                        <p style="font-size:0.9375rem;color:rgba(255,255,255,0.6);">
                            Isi parameter di sebelah kiri dan klik "Hitung Simulasi"
                        </p>
                    </div>
                </template>

                {{-- After calculation --}}
                <template x-if="result">
                    <div>
                        <div class="result-meta-grid">
                            <div class="result-meta-item">
                                <div class="result-meta-label">Modal Diinvestasikan</div>
                                <div class="result-meta-value" x-text="formatRp(result.invested)"></div>
                            </div>
                            <div class="result-meta-item">
                                <div class="result-meta-label">Jangka Waktu</div>
                                <div class="result-meta-value" x-text="result.duration + ' Tahun'"></div>
                            </div>
                            <div class="result-meta-item" style="grid-column:1/-1;">
                                <div class="result-meta-label" x-text="'Estimasi Return (' + result.return_rate + '%)'"></div>
                                <div class="result-meta-value" id="sim-return" x-text="formatRp(result.estimated_return)"></div>
                            </div>
                        </div>

                        <div class="result-divider"></div>

                        <div class="result-total-label">TOTAL KEUNTUNGAN</div>
                        <div class="result-total-value" id="sim-total" x-text="formatRp(result.total)"></div>

                        <div style="margin-top:1.25rem;padding:0.75rem;background:rgba(255,255,255,0.07);border-radius:10px;font-size:0.8125rem;color:rgba(255,255,255,0.6);">
                            <i class="ph ph-info"></i>
                            Hasil simulasi menggunakan bunga majemuk tahunan berdasarkan data historis rata-rata. Bukan merupakan jaminan hasil investasi.
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Learn more --}}
        <div style="text-align:center;margin-top:2.5rem;">
            <p style="font-size:0.9375rem;color:#6B7280;margin-bottom:0.875rem;">
                Ingin belajar lebih lanjut tentang investasi hijau?
            </p>
            <a href="{{ route('user.articles.index') }}" class="btn-cta-primary">
                Baca Artikel Edukasi <i class="ph ph-arrow-right"></i>
            </a>
        </div>

    </div>
</div>
@endsection
