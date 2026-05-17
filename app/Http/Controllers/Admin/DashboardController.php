<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Article;
use App\Models\Comment;

class DashboardController extends Controller
{
    public function index()
    {
        // Recent activity cards (top 3)
        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->limit(3)
            ->get();

        // Latest articles (3)
        $latestArticles = Article::with(['category', 'author'])
            ->published()
            ->latest()
            ->limit(3)
            ->get();

        // Recent comments (4) for right column
        $recentComments = Comment::with('article')
            ->latest()
            ->limit(4)
            ->get();

        // Pending comments badge count (last 8 hours)
        $pendingCommentCount = Comment::where('status', 'pending')
            ->where('created_at', '>=', now()->subHours(8))
            ->count();

        return view('admin.dashboard', compact(
            'recentActivities',
            'latestArticles',
            'recentComments',
            'pendingCommentCount'
        ));
    }
}