<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChangePasswordRequest;
use App\Http\Requests\Admin\UpdateProfileRequest;
use App\Models\EmailVerification;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        return view('admin.profile', ['admin' => auth()->user()]);
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = auth()->user();
        $user->update(['name' => $request->name]);

        ActivityLogService::log('update_profile', 'user', $user->id, 'Mengupdate profil');

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui.',
            'name'    => $user->name,
        ]);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = auth()->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password saat ini tidak benar.',
                'errors'  => ['current_password' => ['Password saat ini tidak benar.']],
            ], 422);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        ActivityLogService::log('change_password', 'user', $user->id, 'Mengganti password');

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah.',
        ]);
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = auth()->user();

        // Remove old photo
        if ($user->photo_profile) {
            Storage::disk('public')->delete($user->photo_profile);
        }

        $path = $request->file('photo')->store('profiles', 'public');
        $user->update(['photo_profile' => $path]);

        return response()->json([
            'success'   => true,
            'message'   => 'Foto profil berhasil diperbarui.',
            'photo_url' => $user->photo_url,
        ]);
    }

    public function deletePhoto()
    {
        $user = auth()->user();

        if ($user->photo_profile) {
            Storage::disk('public')->delete($user->photo_profile);
            $user->update(['photo_profile' => null]);
        }

        return response()->json([
            'success'   => true,
            'message'   => 'Foto profil berhasil dihapus.',
            'photo_url' => $user->photo_url,
        ]);
    }

    public function requestEmailChange(Request $request)
    {
        $request->validate([
            'email' => [
                'required', 'email',
                Rule::unique('users', 'email')->ignore(auth()->id()),
            ],
        ], [
            'email.unique' => 'Email sudah digunakan oleh akun lain.',
        ]);

        $user  = auth()->user();
        $token = Str::random(64);

        EmailVerification::where('user_id', $user->id)->whereNull('used_at')->update(['used_at' => now()]);

        EmailVerification::create([
            'user_id'    => $user->id,
            'email'      => $request->email,
            'token'      => $token,
            'expires_at' => now()->addHours(24),
        ]);

        $verifyUrl = route('admin.profile.email.verify', $token);
        \Log::info("Verify email URL untuk {$user->email} → {$request->email}: {$verifyUrl}");

        return response()->json([
            'success'    => true,
            'message'    => 'Email verifikasi telah dikirim ke ' . $request->email,
            'verify_url' => config('app.debug') ? $verifyUrl : null,
        ]);
    }

    public function verifyEmail(string $token)
    {
        $verification = EmailVerification::where('token', $token)->firstOrFail();

        if (! $verification->isValid()) {
            return redirect()->route('admin.profile')
                ->with('error', 'Link verifikasi tidak valid atau sudah kadaluarsa.');
        }

        $user = $verification->user;
        $user->update(['email' => $verification->email]);
        $verification->markAsUsed();

        ActivityLogService::log('verify_email', 'user', $user->id,
            "Verifikasi email baru: {$verification->email}");

        return redirect()->route('admin.profile')
            ->with('success', 'Email berhasil diverifikasi dan diperbarui.');
    }
}