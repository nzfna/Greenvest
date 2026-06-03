<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GREENVEST.CO - Reset Password</title>
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
                        Buat Password<br>
                        <span class="highlight" style="color:#E9C46A;">Baru yang Kuat.</span>
                    </h2>
                    <p style="font-size:1rem; line-height:1.6; max-width:420px; color:rgba(255,255,255,0.85);">
                        Identitas Anda telah terverifikasi. Sekarang buat password baru yang aman untuk melindungi akun administrator.
                    </p>
                </div>
            </div>
        </div>

        {{-- Reset Password Form --}}
        <div class="auth-form-panel" x-data="{ loading: false, showNew: false, showConfirm: false }">
            <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:1.5rem;">
                <i class="ph ph-lock-key" style="color:#8B6B1B;font-size:1.125rem;"></i>
                <span style="font-size:0.6875rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:#9CA3AF;">Password Reset</span>
            </div>

            <h1 style="font-size:1.875rem;font-weight:800;color:#111827;margin-bottom:0.75rem;">Buat password baru.</h1>
            <p style="font-size:0.875rem;color:#6B7280;line-height:1.6;margin-bottom:1.75rem;">
                Password baru minimal 8 karakter. Gunakan kombinasi huruf, angka, dan simbol agar lebih aman.
            </p>

            @if(session('success'))
                <div style="margin-bottom:1.25rem;padding:0.875rem;background:#D1FAE5;border-radius:10px;font-size:0.875rem;color:#065F46;display:flex;gap:0.5rem;">
                    <i class="ph ph-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div style="margin-bottom:1.25rem;padding:0.875rem;background:#FEF2F2;border-radius:10px;font-size:0.875rem;color:#B91C1C;display:flex;gap:0.5rem;flex-direction:column;">
                    @foreach($errors->all() as $error)
                        <div style="display:flex;gap:0.5rem;align-items:flex-start;">
                            <i class="ph ph-warning-circle" style="flex-shrink:0;margin-top:1px;"></i> {{ $error }}
                        </div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.reset-password') }}" @submit.prevent="loading=true;$el.submit()">
                @csrf

                {{-- Password Baru --}}
                <div style="margin-bottom:1.25rem;">
                    <label style="display:block;font-size:0.75rem;font-weight:600;color:#6B7280;margin-bottom:0.375rem;text-transform:uppercase;letter-spacing:0.06em;">
                        PASSWORD BARU
                    </label>
                    <div style="position:relative;">
                        <input
                            :type="showNew ? 'text' : 'password'"
                            name="password"
                            placeholder="Minimal 8 karakter"
                            autocomplete="new-password"
                            style="width:100%;padding:0.75rem 2.75rem 0.75rem 0.875rem;background:#F9FAFB;border:1.5px solid {{ $errors->has('password') ? '#EF4444' : '#E5E7EB' }};border-radius:10px;font-size:0.9375rem;outline:none;box-sizing:border-box;"
                        >
                        <button type="button" @click="showNew=!showNew"
                            style="position:absolute;right:0.75rem;top:50%;transform:translateY(-50%);background:none;border:none;color:#9CA3AF;cursor:pointer;">
                            <i :class="showNew ? 'ph ph-eye-slash' : 'ph ph-eye'"></i>
                        </button>
                    </div>
                    @error('password')
                        <p style="font-size:0.75rem;color:#EF4444;margin-top:0.25rem;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Konfirmasi Password --}}
                <div style="margin-bottom:1.75rem;">
                    <label style="display:block;font-size:0.75rem;font-weight:600;color:#6B7280;margin-bottom:0.375rem;text-transform:uppercase;letter-spacing:0.06em;">
                        KONFIRMASI PASSWORD BARU
                    </label>
                    <div style="position:relative;">
                        <input
                            :type="showConfirm ? 'text' : 'password'"
                            name="password_confirmation"
                            placeholder="Ulangi password baru"
                            autocomplete="new-password"
                            style="width:100%;padding:0.75rem 2.75rem 0.75rem 0.875rem;background:#F9FAFB;border:1.5px solid {{ $errors->has('password_confirmation') ? '#EF4444' : '#E5E7EB' }};border-radius:10px;font-size:0.9375rem;outline:none;box-sizing:border-box;"
                        >
                        <button type="button" @click="showConfirm=!showConfirm"
                            style="position:absolute;right:0.75rem;top:50%;transform:translateY(-50%);background:none;border:none;color:#9CA3AF;cursor:pointer;">
                            <i :class="showConfirm ? 'ph ph-eye-slash' : 'ph ph-eye'"></i>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <p style="font-size:0.75rem;color:#EF4444;margin-top:0.25rem;">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn-primary" style="width:100%;" :disabled="loading">
                    <span x-show="!loading">Simpan Password Baru →</span>
                    <span x-show="loading">Menyimpan...</span>
                </button>

                <div style="text-align:center;margin-top:1.25rem;">
                    <a href="{{ route('admin.login') }}"
                       style="font-size:0.875rem;color:#6B7280;text-decoration:none;">
                        ← Kembali ke halaman login
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>