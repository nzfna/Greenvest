<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Middleware\CheckBanned;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller

{
    public function index(string $slug)
{
    $article  = Article::where('slug', $slug)->published()->firstOrFail();
    $comments = $article->approvedComments()
        ->orderBy('created_at', 'asc')
        ->get()
        ->map(fn($c) => [
            'id'          => $c->id,
            'user_name'   => $c->user_name,
            'initials'    => $c->initials,
            'content'     => $c->content,
            'admin_reply' => $c->admin_reply,
            'created_at'  => $c->created_at->diffForHumans(),
        ]);

    return response()->json(['comments' => $comments]);
}
    public function store(Request $request, string $slug)
    {
        $request->validate([
            'user_name' => ['required', 'string', 'max:100'],
            'user_email'=> ['nullable', 'email', 'max:255'],
            'content'   => ['required', 'string', 'min:5', 'max:2000'],
        ], [
            'user_name.required' => 'Nama wajib diisi.',
            'content.required'   => 'Komentar tidak boleh kosong.',
            'content.min'        => 'Komentar minimal 5 karakter.',
        ]);

        $article     = Article::where('slug', $slug)->published()->firstOrFail();
        $fingerprint = CheckBanned::getFingerprint($request);

        $comment = Comment::create([
            'article_id'         => $article->id,
            'user_name'          => $request->user_name,
            'user_email'         => $request->user_email,
            'content'            => $request->content,
            'status'             => 'approved', // auto-approve, admin tinggal reject/ban kalau toxic
            'ip_address'         => $request->ip(),
            'user_agent'         => $request->userAgent(),
            'device_fingerprint' => $fingerprint,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Komentar berhasil dikirim!',
            ]);
        }

        return back()->with('success', 'Komentar berhasil dikirim!');
    }
}
