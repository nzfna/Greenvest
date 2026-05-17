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

        // Attach fingerprint to request so controllers can use it
        $request->merge(['device_fingerprint' => $fingerprint]);

        return $next($request);
    }

    public static function getFingerprint(Request $request): string
    {
        $data = $request->ip() . '|' . $request->userAgent();
        return hash('sha256', $data);
    }
}