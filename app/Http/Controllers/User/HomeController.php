<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::withCount(['articles' => fn($q) => $q->published()])->get();

        // Featured article (latest published)
        $featured = Article::with('category')->published()->latest('published_at')->first();

        // Articles grouped by category (3 each)
        $articlesByCategory = $categories->mapWithKeys(fn($cat) => [
            $cat->slug => Article::with('category')
                ->published()
                ->where('category_id', $cat->id)
                ->latest('published_at')
                ->limit(3)
                ->get(),
        ]);

        return view('user.home', compact('categories', 'featured', 'articlesByCategory'));
    }
}