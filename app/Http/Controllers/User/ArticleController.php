<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        $query      = Article::with('category')->published()->latest('published_at');

        if ($cat = $request->get('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $cat));
        }

        if ($search = $request->get('search')) {
            $query->where(fn($q) => $q
                ->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
            );
        }

        $articles        = $query->paginate(6)->withQueryString();
        $activeCategory  = $request->get('category', 'all');

        return view('user.articles.index', compact('articles', 'categories', 'activeCategory'));
    }

    public function show(string $slug)
    {
        $article  = Article::with(['category', 'author', 'approvedComments'])->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $related  = Article::with('category')
            ->published()
            ->where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('user.articles.show', compact('article', 'related'));
    }
}