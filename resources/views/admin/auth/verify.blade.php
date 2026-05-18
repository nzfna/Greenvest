<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GREENVEST.CO - @yield('title', 'Verify Admin')</title>
    <link rel="icon" type="image/svg+xml" href="/images/favicon.svg">
    <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/regular/style.css">
    @vite(['resources/css/admin.css', 'resources/js/admin/app.js'])
</head>
<body>
<div class="auth-page">
    <div class="auth-card">

        {{-- Banner --}}
        <div class="auth-banner">
            <div class="auth-banner-content" style="position:relative; padding-top:8rem; padding-left:0.5rem; padding-bottom:6rem; display:flex; flex-direction:column; justify-content:flex-end; height:100%; box-sizing:border-box;">
                
                <div class="auth-logo" style="position:absolute; top:2rem; left:0.5rem;">
                    <div class="auth-logo-icon">
                        <i class="ph ph-leaf" style="color:#D8F3DC; font-size:1rem;"></i>
                    </div>
                    <span class="auth-logo-name">GREENVEST.CO</span>
                </div>

                <div class="auth-tagline">
                    <h2 style="font-size:2rem; line-height:1.2; margin-bottom:1.25rem; color:#fff;">
                        Edukasi Pintar<br>
                        <span class="highlight" style="color:#E9C46A;">Investasi Hijau.</span>
                    </h2>
                    <p style="font-size:1rem; line-height:1.6; max-width:420px; color:rgba(255,255,255,0.85);">
                        Tempat edukasi pintar yang presisi untuk pengelolaan aset lingkungan masa depan. 
                        Selamat datang kembali di portal administrasi.
                    </p>
                </div>
            </div>
        </div>

        {{-- OTP Form --}}
        <div class="auth-form-panel" x-data="{ ...otpInput(), ...resendTimer(60), loading: false }">
            <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:1.5rem;">
                <i class="ph ph-shield-check" style="color:#8B6B1B;font-size:1.125rem;"></i>
                <span style="font-size:0.6875rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:#9CA3AF;">Security Protocol</span>
            </div>

            <h1 style="font-size:1.875rem;font-weight:800;color:#111827;margin-bottom:0.75rem;">Check your inbox.</h1>
            <p style="font-size:0.875rem;color:#6B7280;line-height:1.6;margin-bottom:1.75rem;">
                Kami telah mengirimkan kode verifikasi 6 digit ke
                <span style="color:#1B4332;font-weight:600;">{{ session('reset_email', '...') }}</span>.
                @if(config('app.debug') && session('reset_token_hint'))
                    <br><small style="color:#8B6B1B;">[DEBUG] Kode: {{ session('reset_token_hint') }}</small>
                @endif
            </p>

            @error('code')
                <div style="margin-bottom:1.25rem;padding:0.875rem;background:#FEF2F2;border-radius:10px;font-size:0.875rem;color:#B91C1C;display:flex;gap:0.5rem;">
                    <i class="ph ph-warning-circle"></i> {{ $message }}
                </div>
            @enderror

            <form method="POST" action="{{ route('admin.verify') }}" @submit.prevent="loading=true;$el.submit()">
                @csrf

                {{-- Hidden input --}}
                <input type="hidden" name="code" id="otp-code">

                {{-- OTP Cells --}}
                <div class="otp-input-group" style="margin-bottom:1.75rem;" @paste="onPaste($event)">
                    <template x-for="(cell, idx) in cells" :key="idx">
                        <input
                            type="text"
                            inputmode="numeric"
                            maxlength="1"
                            class="otp-cell"
                            :class="cell ? 'filled' : ''"
                            :value="cell"
                            @input="onInput(idx, $event)"
                            @keydown="onKeydown(idx, $event)"
                        >
                    </template>
                </div>

                <div style="display:flex;gap:0.75rem;align-items:center;">
                    <button type="submit" class="btn-primary" style="flex:1;" :disabled="loading">
                        <span x-show="!loading">Verify Access →</span>
                        <span x-show="loading">Memverifikasi...</span>
                    </button>
                </div>

                <div style="text-align:center;margin-top:1.25rem;">
                    <button
                        type="button"
                        :disabled="!canResend"
                        @click="canResend && (window.location.href='{{ route('admin.forgot') }}')"
                        style="font-size:0.875rem;font-weight:600;color:#1B4332;background:none;border:none;cursor:pointer;"
                        :style="canResend ? 'opacity:1' : 'opacity:0.4;cursor:default'"
                    >
                        Resend Email
                    </button>
                    <span x-show="!canResend" style="display:block;font-size:0.75rem;color:#9CA3AF;margin-top:0.25rem;" x-text="label"></span>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
