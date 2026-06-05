<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Models\EmailVerification;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // ── Login ──────────────────────────────────────────────────────────────────

    public function showLogin()
    {
        if (auth()->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            ActivityLogService::log(
                'login',
                'user',
                auth()->id(),
                'Admin login dari IP ' . $request->ip()
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'success'  => true,
                    'redirect' => route('admin.dashboard'),
                ]);
            }

            return redirect()->intended(route('admin.dashboard'));
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Kredensial tidak valid.',
            ], 422);
        }

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors(['email' => 'Kredensial tidak valid.']);
    }

    public function logout(Request $request)
    {
        ActivityLogService::log('logout', 'user', auth()->id(), 'Admin logout');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    // ── Forgot Password ────────────────────────────────────────────────────────

    public function showForgot()
    {
        if (auth()->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.forgot-password');
    }

    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.exists' => 'Email tidak terdaftar sebagai admin.',
        ]);

        $user  = User::where('email', $request->email)->firstOrFail();
        $token = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Invalidate previous tokens
        EmailVerification::where('user_id', $user->id)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);

        EmailVerification::create([
            'user_id'    => $user->id,
            'email'      => $user->email,
            'token'      => $token,
            'expires_at' => now()->addMinutes(30),
        ]);

        // Store in session for verification page
        session(['reset_email' => $user->email, 'reset_token_hint' => $token]);

        // In production: send via email. For local: use log.
        \Log::info("OTP Reset Password untuk {$user->email}: {$token}");

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kode verifikasi telah dikirim ke email Anda.',
            ]);
        }

        return redirect()->route('admin.verify')
            ->with('success', 'Kode verifikasi dikirim ke ' . $user->email);
    }

    // ── OTP Verification ───────────────────────────────────────────────────────

    public function showVerify()
    {
        if (! session('reset_email')) {
            return redirect()->route('admin.forgot');
        }

        return view('admin.auth.verify');
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $email = session('reset_email');
        if (! $email) {
            return back()->withErrors(['code' => 'Sesi tidak valid. Silakan ulangi.']);
        }

        $user = User::where('email', $email)->firstOrFail();

        $verification = EmailVerification::where('user_id', $user->id)
            ->where('token', $request->code)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();

        if (! $verification) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode tidak valid atau sudah kadaluarsa.',
                ], 422);
            }
            return back()->withErrors(['code' => 'Kode tidak valid atau sudah kadaluarsa.']);
        }

        $verification->markAsUsed();

        // Auto-login after OTP verified
        Auth::login($user, false);
        $request->session()->regenerate();
        session()->forget(['reset_email', 'reset_token_hint']);
        session(['password_reset_verified' => true]);

        if ($request->expectsJson()) {
            return response()->json([
                'success'  => true,
                'redirect' => route('admin.reset-password'),
            ]);
        }

        return redirect()->route('admin.reset-password')
            ->with('success', 'Verifikasi berhasil. Silakan ganti password Anda.');
    }

    // ── Reset Password ─────────────────────────────────────────────────────────

    public function showResetPassword()
    {
        if (! session('password_reset_verified')) {
            return redirect()->route('admin.login');
        }

        return view('admin.auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        if (! session('password_reset_verified')) {
            return redirect()->route('admin.login');
        }

        $request->validate([
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ], [
            'password.required'    => 'Password baru wajib diisi.',
            'password.min'         => 'Password minimal 8 karakter.',
            'password.confirmed'   => 'Konfirmasi password tidak cocok.',
        ]);

        $user = auth()->user();
        $user->update(['password' => \Hash::make($request->password)]);

        session()->forget('password_reset_verified');

        ActivityLogService::log('change_password', 'user', $user->id, 'Reset password via OTP');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'success'  => true,
                'redirect' => route('admin.login'),
            ]);
        }

        return redirect()->route('admin.login')
            ->with('success', 'Password berhasil diubah. Silakan login dengan password baru.');
    }
}