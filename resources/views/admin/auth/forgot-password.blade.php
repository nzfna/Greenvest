<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lupa Kata Sandi — Greenvest.co</title>
    <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/regular/style.css">
    @vite(['resources/css/admin.css', 'resources/js/admin/app.js'])
</head>
<body>
<div class="auth-page">
    <div class="auth-card">

        {{-- Banner --}}
        <div class="auth-banner">
            <div class="auth-banner-content">
                <div class="auth-logo" style="position:absolute;top:2rem;left:2.5rem;">
                    <div class="auth-logo-icon"><i class="ph ph-leaf" style="color:#D8F3DC;font-size:1rem;"></i></div>
                    <span class="auth-logo-name">GREENVEST.CO</span>
                </div>
                <div class="auth-tagline">
                    <h2>Edukasi Pintar<br><span class="highlight">Investasi Hijau.</span></h2>
                    <p>Tempat edukasi pintar yang presisi untuk pengelolaan aset lingkungan masa depan. Selamat datang kembali di portal administrasi.</p>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <div class="auth-form-panel" x-data="{ loading: false }">
            <div style="display:flex;align-items:center;gap:0.625rem;margin-bottom:1.5rem;">
                <div style="width:32px;height:32px;background:#FDF8E8;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                    <i class="ph ph-lock-open" style="color:#8B6B1B;font-size:1rem;"></i>
                </div>
                <span style="font-size:0.875rem;font-weight:700;color:#1B4332;">Greenvest.co</span>
            </div>

            <h1 style="font-size:1.625rem;font-weight:800;color:#111827;margin-bottom:0.25rem;border-bottom:3px solid #D4A843;padding-bottom:0.5rem;display:inline-block;">Lupa Kata Sandi</h1>
            <p style="font-size:0.875rem;color:#6B7280;line-height:1.6;margin-top:0.75rem;margin-bottom:1.75rem;">
                Masukkan email administrator Anda. Kami akan mengirimkan kode verifikasi 6 digit untuk reset password.
            </p>

            @if(session('success'))
                <div style="margin-bottom:1.25rem;padding:0.875rem;background:#D1E7DD;border-radius:10px;font-size:0.875rem;color:#0F5132;display:flex;gap:0.5rem;">
                    <i class="ph ph-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @error('email')
                <div style="margin-bottom:1.25rem;padding:0.875rem;background:#FEF2F2;border-radius:10px;font-size:0.875rem;color:#B91C1C;display:flex;gap:0.5rem;">
                    <i class="ph ph-warning-circle"></i> {{ $message }}
                </div>
            @enderror

            <form method="POST" action="{{ route('admin.forgot') }}" @submit.prevent="loading=true;$el.submit()">
                @csrf
                <div style="margin-bottom:1.5rem;">
                    <label class="form-label">Admin Email Address</label>
                    <div class="form-input-wrap">
                        <i class="ph ph-envelope-simple form-input-icon"></i>
                        <input type="email" name="email" class="form-input"
                               placeholder="archivist@Greenvest.org"
                               value="{{ old('email') }}" required>
                    </div>
                </div>

                <button type="submit" class="btn-primary" :disabled="loading">
                    <span x-show="!loading">Kirim Kode Reset →</span>
                    <span x-show="loading">Mengirim...</span>
                </button>

                <div style="text-align:center;margin-top:1.5rem;">
                    <p style="font-size:0.8125rem;color:#9CA3AF;margin-bottom:0.5rem;">Ingat kata sandi Anda?</p>
                    <a href="{{ route('admin.login') }}"
                       style="font-size:0.875rem;font-weight:600;color:#1B4332;text-decoration:none;">
                        ← Back to Login
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
