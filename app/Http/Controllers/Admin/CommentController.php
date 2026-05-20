<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Category;
use App\Models\DeviceBan;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $query = Comment::with('article.category')->latest();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                  ->orWhere('user_email', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($category = $request->get('category')) {
            $query->whereHas('article', fn($q) => $q->where('category_id', $category));
        }

        $comments   = $query->paginate(15)->withQueryString();
        $categories = Category::all();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'data' => $comments]);
        }

        return view('admin.comments.index', compact('comments', 'categories'));
    }

    public function approve(int $id)
    {
        $comment = Comment::findOrFail($id);
        $comment->update(['status' => 'approved']);

        ActivityLogService::log('approve_comment', 'comment', $id,
            "Approve komentar dari {$comment->user_name}");

        return response()->json(['success' => true, 'message' => 'Komentar disetujui.']);
    }

    public function reject(int $id)
    {
        $comment = Comment::findOrFail($id);
        $comment->update(['status' => 'rejected']);

        ActivityLogService::log('reject_comment', 'comment', $id,
            "Reject komentar dari {$comment->user_name}");

        return response()->json(['success' => true, 'message' => 'Komentar ditolak.']);
    }





    public function deviceBan(int $id)
    {
        $comment = Comment::findOrFail($id);

        $fingerprint = $comment->device_fingerprint
            ?? hash('sha256', $comment->ip_address . '|' . $comment->user_agent);

        DeviceBan::updateOrCreate(
            ['device_fingerprint' => $fingerprint],
            [
                'ip_address' => $comment->ip_address,
                'reason'     => 'Device Ban by admin',
                'banned_at'  => now(),
                'expires_at' => null, // permanen
            ]
        );

        // Update status komentar jadi rejected
        $comment->update(['status' => 'rejected']);

        ActivityLogService::log('device_ban', 'comment', $id,
            "Device Ban: user={$comment->user_name} fp={$fingerprint}");

        return response()->json(['success' => true, 'message' => 'Perangkat berhasil di-ban permanen.']);
    }

    public function undeviceBan(int $id)
    {
        $comment = Comment::findOrFail($id);

        $fingerprint = $comment->device_fingerprint
            ?? hash('sha256', $comment->ip_address . '|' . $comment->user_agent);

        // Hapus ban dari device_bans
        DeviceBan::where('device_fingerprint', $fingerprint)->delete();

        // Kembalikan status komentar jadi approved
        $comment->update(['status' => 'approved']);

        ActivityLogService::log('unban', 'comment', $id,
            "Unban device: user={$comment->user_name} fp={$fingerprint}");

        return response()->json(['success' => true, 'message' => 'Device ban berhasil dicabut.']);
    }

    public function reply(Request $request, int $id)
    {
        $request->validate([
            'content' => ['required', 'string', 'max:2000'],
        ]);

        $comment = Comment::findOrFail($id);
        $comment->update([
            'admin_reply' => $request->content,
            'replied_at'  => now(),
        ]);

        ActivityLogService::log('reply_comment', 'comment', $id,
            "Membalas komentar dari {$comment->user_name}");

        return response()->json([
            'success'     => true,
            'message'     => 'Balasan berhasil dikirim.',
            'admin_reply' => $comment->admin_reply,
            'replied_at'  => $comment->replied_at->diffForHumans(),
        ]);
    }

    public function exportLog()
    {
        $comments = Comment::with('article')->get();

        $csv  = "ID,Article,User,Email,Status,IP,Date\n";
        foreach ($comments as $c) {
            $csv .= implode(',', [
                $c->id,
                '"' . ($c->article->title ?? '') . '"',
                '"' . $c->user_name . '"',
                $c->user_email ?? '',
                $c->status,
                $c->ip_address ?? '',
                $c->created_at->format('Y-m-d H:i'),
            ]) . "\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="comments-' . now()->format('Ymd') . '.csv"',
        ]);
    }
}
