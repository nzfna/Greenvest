<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GREENVEST.CO - @yield('title', 'Login Admin')</title>
    <link rel="icon" type="image/svg+xml" href="/images/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
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

        {{-- Form --}}
        <div class="auth-form-panel" x-data="{ showPass: false, loading: false }">
            <h1 style="font-size:1.625rem;font-weight:800;color:#111827;margin-bottom:0.25rem;">@yield('title', 'Login Admin')</h1>
            <p style="font-size:0.875rem;color:#6B7280;margin-bottom:2rem;">Silakan masukkan kredensial Anda untuk mengakses dashboard Greenvest.</p>

            @if($errors->has('email'))
                <div style="margin-bottom:1.25rem;padding:0.75rem 1rem;background:#FEF2F2;border-radius:10px;font-size:0.875rem;color:#B91C1C;display:flex;align-items:center;gap:0.5rem;">
                    <i class="ph ph-warning-circle"></i>
                    {{ $errors->first('email') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}"
                  @submit.prevent="
                    loading = true;
                    $el.submit();
                  ">
                @csrf

                <div style="margin-bottom:1.25rem;">
                    <label class="form-label">Email Administrator</label>
                    <div class="form-input-wrap">
                        <i class="ph ph-at form-input-icon"></i>
                        <input
                            type="email"
                            name="email"
                            class="form-input {{ $errors->has('email') ? 'error' : '' }}"
                            placeholder="admin@greenvest.co"
                            value="{{ old('email') }}"
                            required autocomplete="email"
                        >
                    </div>
                </div>

                <div style="margin-bottom:1.25rem;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.375rem;">
                        <label class="form-label" style="margin-bottom:0;">Kata Sandi</label>
                        <a href="{{ route('admin.forgot') }}"
                           style="font-size:0.8125rem;font-weight:600;color:#8B6B1B;text-decoration:none;">
                           Lupa Sandi?
                        </a>
                    </div>
                    <div class="form-input-wrap">
                        <i class="ph ph-lock form-input-icon"></i>
                        <input
                            :type="showPass ? 'text' : 'password'"
                            name="password"
                            class="form-input"
                            placeholder="••••••••"
                            required autocomplete="current-password"
                            style="padding-right:2.75rem;"
                        >
                        <button type="button" @click="showPass=!showPass"
                                style="position:absolute;right:0.875rem;top:50%;transform:translateY(-50%);background:none;border:none;color:#9CA3AF;cursor:pointer;font-size:1rem;">
                            <i :class="showPass ? 'ph ph-eye-slash' : 'ph ph-eye'"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-primary" :disabled="loading">
                    <span x-show="!loading">MASUK KE SISTEM →</span>
                    <span x-show="loading">Memproses...</span>
                </button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
