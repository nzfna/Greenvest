@extends('layouts.admin')
@section('title', 'Profil')

@section('content')
<div x-data="profilePage()">

<div style="margin-bottom:0.25rem;" class="section-label">PENGATURAN AKUN</div>
<h1 class="page-title" style="margin-bottom:1.75rem;">Profil Admin</h1>

<div class="profile-grid">

    {{-- ── Informasi Pribadi ── --}}
    <div class="profile-panel">
        <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:1.5rem;">
            <i class="ph ph-identification-card" style="color:#8B6B1B;font-size:1rem;"></i>
            <h3 style="font-size:0.6875rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:#8B6B1B;">INFORMASI PRIBADI</h3>
        </div>

        {{-- Avatar --}}
        <div class="profile-avatar-wrap">
            <img id="profile-img"
                 src="{{ $admin->photo_url }}"
                 alt="Foto Profil"
                 class="profile-avatar">
            <div class="profile-avatar-actions">
                <label class="btn-upload" style="cursor:pointer;">
                    <i class="ph ph-pencil"></i> Ubah Foto Profil
                    <input type="file" class="sr-only" accept="image/*" @change="uploadPhoto($event)">
                </label>
                <button class="btn-upload-delete" type="button" @click="deletePhoto()">
                    <i class="ph ph-trash"></i> Hapus
                </button>
            </div>
        </div>

        {{-- Name --}}
        <div style="margin-bottom:1.25rem;">
            <label style="display:block;font-size:0.75rem;font-weight:600;color:#6B7280;margin-bottom:0.375rem;text-transform:uppercase;letter-spacing:0.06em;">NAMA PANGGILAN</label>
            <input type="text" id="admin-name"
                   value="{{ $admin->name }}"
                   style="width:100%;padding:0.6875rem 0.875rem;background:#F9FAFB;border:1.5px solid #E5E7EB;border-radius:10px;font-size:0.9375rem;color:#111827;outline:none;">
        </div>

        {{-- Email (read-only, change via verification) --}}
        <div style="margin-bottom:1.5rem;">
            <label style="display:block;font-size:0.75rem;font-weight:600;color:#6B7280;margin-bottom:0.375rem;text-transform:uppercase;letter-spacing:0.06em;">EMAIL</label>
            <input type="email" value="{{ $admin->email }}" readonly
                   style="width:100%;padding:0.6875rem 0.875rem;background:#F3F4F6;border:1.5px solid #E5E7EB;border-radius:10px;font-size:0.9375rem;color:#9CA3AF;outline:none;cursor:not-allowed;">
        </div>

        {{-- Change Email Request --}}
        <div x-data="{ showEmailChange: false }" style="margin-bottom:1.5rem;">
            <button type="button"
                    @click="showEmailChange = !showEmailChange"
                    style="display:inline-flex;align-items:center;gap:0.375rem;padding:0.5rem 1rem;background:#E8F5E9;color:#1B4332;font-size:0.8125rem;font-weight:600;border:1px solid #D8F3DC;border-radius:8px;cursor:pointer;">
                <i class="ph ph-shield-check"></i> VERIFY EMAIL TO CHANGE
            </button>
            <div x-show="showEmailChange" x-cloak style="margin-top:0.875rem;padding:1rem;background:#F9FAFB;border-radius:10px;">
                <label style="display:block;font-size:0.75rem;font-weight:600;color:#6B7280;margin-bottom:0.375rem;">EMAIL BARU</label>
                <div style="display:flex;gap:0.5rem;">
                    <input type="email" id="new-email" placeholder="email-baru@greenvest.co"
                           style="flex:1;padding:0.5625rem 0.875rem;background:#fff;border:1.5px solid #E5E7EB;border-radius:8px;font-size:0.875rem;outline:none;">
                    <button type="button" @click="requestEmailChange()"
                            style="padding:0.5625rem 1rem;background:#1B4332;color:#fff;font-size:0.875rem;font-weight:600;border:none;border-radius:8px;cursor:pointer;">
                        Kirim Verifikasi
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Keamanan Akun ── --}}
    <div class="profile-panel">
        <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:1.5rem;">
            <i class="ph ph-lock" style="color:#8B6B1B;font-size:1rem;"></i>
            <h3 style="font-size:0.6875rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:#8B6B1B;">KEAMANAN AKUN</h3>
        </div>

        <div style="margin-bottom:1.25rem;">
            <label style="display:block;font-size:0.75rem;font-weight:600;color:#6B7280;margin-bottom:0.375rem;text-transform:uppercase;letter-spacing:0.06em;">KATA SANDI SAAT INI</label>
            <div style="position:relative;" x-data="{ show: false }">
                <input :type="show ? 'text' : 'password'" id="current-password"
                       placeholder="••••••••••••"
                       style="width:100%;padding:0.6875rem 2.5rem 0.6875rem 0.875rem;background:#F9FAFB;border:1.5px solid #E5E7EB;border-radius:10px;font-size:0.9375rem;outline:none;">
                <button type="button" @click="show=!show"
                        style="position:absolute;right:0.75rem;top:50%;transform:translateY(-50%);background:none;border:none;color:#9CA3AF;cursor:pointer;">
                    <i :class="show ? 'ph ph-eye-slash' : 'ph ph-eye'"></i>
                </button>
            </div>
        </div>

        <div style="margin-bottom:1.25rem;">
            <label style="display:block;font-size:0.75rem;font-weight:600;color:#6B7280;margin-bottom:0.375rem;text-transform:uppercase;letter-spacing:0.06em;">KATA SANDI BARU</label>
            <input type="password" id="new-password"
                   style="width:100%;padding:0.6875rem 0.875rem;background:#F9FAFB;border:1.5px solid #E5E7EB;border-radius:10px;font-size:0.9375rem;outline:none;">
            <p style="font-size:0.75rem;color:#9CA3AF;margin-top:0.25rem;">Minimal 8 karakter dengan kombinasi simbol.</p>
        </div>

        <div style="margin-bottom:2rem;">
            <label style="display:block;font-size:0.75rem;font-weight:600;color:#6B7280;margin-bottom:0.375rem;text-transform:uppercase;letter-spacing:0.06em;">KONFIRMASI KATA SANDI BARU</label>
            <input type="password" id="confirm-password"
                   style="width:100%;padding:0.6875rem 0.875rem;background:#F9FAFB;border:1.5px solid #E5E7EB;border-radius:10px;font-size:0.9375rem;outline:none;">
        </div>
    </div>
</div>

{{-- Save / Cancel buttons (full row) --}}
<div style="display:flex;align-items:center;justify-content:flex-end;gap:1rem;margin-top:1.5rem;padding-top:1.25rem;border-top:1px solid #F3F4F6;">
    <button type="button"
            style="font-size:0.875rem;font-weight:600;color:#6B7280;background:none;border:none;cursor:pointer;text-decoration:underline;"
            @click="document.getElementById('admin-name').value = '{{ $admin->name }}';['current-password','new-password','confirm-password'].forEach(id=>document.getElementById(id).value='')">
        BATALKAN
    </button>
    <button type="button"
            @click="
                const hasPw = document.getElementById('new-password').value;
                if(hasPw) savePassword(); else saveProfile();
            "
            :disabled="saving"
            style="padding:0.75rem 2rem;background:#8B6B1B;color:#fff;font-size:0.875rem;font-weight:700;border:none;border-radius:10px;cursor:pointer;letter-spacing:0.04em;">
        <span x-show="!saving">SIMPAN PERUBAHAN</span>
        <span x-show="saving">Menyimpan...</span>
    </button>
</div>

{{-- Logout (bottom) --}}
<div style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid #F3F4F6;">
    <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit"
                style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.625rem 1.25rem;background:#FEF2F2;color:#EF4444;font-size:0.875rem;font-weight:600;border:1px solid #FECACA;border-radius:10px;cursor:pointer;">
            <i class="ph ph-sign-out"></i> Logout dari sistem
        </button>
    </form>
</div>

</div>
@endsection
