<?php

namespace App\Http\Middleware;

use App\Models\DeviceBan;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBanned
{
    public function handle(Request $request, Closure $next): Response
    {
        $fingerprint = $this->getFingerprint($request);

        if (DeviceBan::isBanned($fingerprint)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'banned'  => true,
                    'message' => 'Anda tidak dapat mengirim komentar.',
                ], 403);
            }
            return back()->with('error', 'Anda tidak dapat mengirim komentar.');
        }

        $request->merge(['device_fingerprint' => $fingerprint]);

        return $next($request);
    }

    /**
     * Fingerprint prioritas:
     * 1. Pakai client fingerprint dari JS (paling akurat, per perangkat)
     * 2. Fallback: server-side (IP + UserAgent) kalau JS mati
     */
    public static function getFingerprint(Request $request): string
    {
        // Cek apakah ada fingerprint dari client JS
        $clientFp = $request->input('_device_fp');

        if ($clientFp && strlen($clientFp) === 64 && ctype_xdigit($clientFp)) {
            // Sudah di-hash SHA256 dari client, langsung pakai
            return $clientFp;
        }

        // Fallback: server-side fingerprint
        $data = implode('|', [
            $request->ip(),
            $request->userAgent(),
            $request->header('Accept-Language', ''),
            $request->header('Accept-Encoding', ''),
        ]);

        return hash('sha256', $data);
    }
}
