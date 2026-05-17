<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreArticleRequest;
use App\Models\Article;
use App\Models\Category;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::with(['category', 'author'])
            ->whereNull('deleted_at');

        if ($search = $request->get('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($category = $request->get('category')) {
            $query->where('category_id', $category);
        }

        $articles   = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::all();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $articles,
            ]);
        }

        return view('admin.articles.index', compact('articles', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.articles.create', compact('categories'));
    }

    public function store(StoreArticleRequest $request)
    {
        $data = $request->validated();
        $data['author_id']    = auth()->id();
        $data['status']       = $data['status'] ?? 'published';
        $data['revision_count'] = 1;

        if ($data['status'] === 'published') {
            $data['published_at'] = now();
        }

        // Handle cover image
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')
                ->store('articles', 'public');
        }

        $article = Article::create($data);

        ActivityLogService::log(
            'create_article',
            'article',
            $article->id,
            "Membuat artikel: {$article->title}"
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Artikel berhasil dibuat.',
                'article' => $article,
            ]);
        }

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel berhasil dibuat.');
    }

    public function show(Article $article)
    {
        return view('admin.articles.preview', compact('article'));
    }

    public function edit(Article $article)
    {
        $categories = Category::all();
        return view('admin.articles.edit', compact('article', 'categories'));
    }

    public function update(StoreArticleRequest $request, Article $article)
    {
        $data = $request->validated();

        if (isset($data['status']) && $data['status'] === 'published' && ! $article->published_at) {
            $data['published_at'] = now();
        }

        // Handle new cover image
        if ($request->hasFile('cover_image')) {
            if ($article->cover_image) {
                Storage::disk('public')->delete($article->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')
                ->store('articles', 'public');
        }

        $data['revision_count'] = $article->revision_count + 1;
        $article->update($data);

        ActivityLogService::log(
            'update_article',
            'article',
            $article->id,
            "Mengupdate artikel: {$article->title} (Revisi ke-{$article->revision_count})"
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Artikel berhasil diperbarui.',
            ]);
        }

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(Article $article)
    {
        $title = $article->title;

        // Soft delete + update status
        $article->update(['status' => 'deleted']);
        $article->delete();

        ActivityLogService::log(
            'delete_article',
            'article',
            $article->id,
            "Menghapus artikel: {$title}"
        );

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Artikel berhasil dihapus.',
            ]);
        }

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel berhasil dihapus.');
    }

    public function preview(string $slug)
    {
        $article = Article::where('slug', $slug)->firstOrFail();
        return view('admin.articles.preview', compact('article'));
    }
}