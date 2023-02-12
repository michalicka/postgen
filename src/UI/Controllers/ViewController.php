<?php

namespace Postgen\UI\Controllers;

use Illuminate\Routing\Controller;
use Postgen\Generator\Models\Post;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class ViewController extends Controller
{
    public function index()
    {
        $posts = Post::with('user')
            ->select(['id', 'title', 'slug', 'content', 'published_at', 'user_id', 'image'])
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        $months = $this->months();
        $categories = $this->categories();

        return view('pages.index', compact('posts', 'months', 'categories'));
    }

    public function archive(int $year, int $month)
    {
        $posts = Post::with('user')
            ->select(['id', 'title', 'slug', 'content', 'published_at', 'user_id', 'image'])
            ->where('status', 'published')
            ->whereBetween('published_at', [
                Carbon::parse(sprintf('%d-%s-01', $year, Str::padLeft("$month", 2, '0')))->startOfMonth(),
                Carbon::parse(sprintf('%d-%s-01', $year, Str::padLeft("$month", 2, '0')))->endOfMonth()
            ])
            ->where('published_at', '<=', Carbon::now())
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        $months = $this->months();
        $categories = $this->categories();

        return view('pages.index', compact('posts', 'months', 'categories'));
    }

    public function author(int $author)
    {
        $posts = Post::with('user')
            ->select(['id', 'title', 'slug', 'content', 'published_at', 'user_id', 'image'])
            ->where('status', 'published')
            ->where('user_id', $author)
            ->where('published_at', '<=', Carbon::now())
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        $months = $this->months();
        $categories = $this->categories();

        return view('pages.index', compact('posts', 'months', 'categories'));
    }

    public function category(string $slug)
    {
        $categories = $this->categories();
        $category = $categories->firstWhere('slug', $slug);

        if (!$category) abort(404);

        $posts = Post::with('user')
            ->select(['id', 'title', 'slug', 'content', 'published_at', 'user_id', 'image'])
            ->where('status', 'published')
            ->where('category', $category->name)
            ->where('published_at', '<=', Carbon::now())
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        $months = $this->months();
        $categories = $this->categories();

        return view('pages.index', compact('posts', 'months', 'categories'));
    }

    public function get(string $slug)
    {
        $post = Post::with('user')
            ->select(['id', 'category', 'title', 'slug', 'content', 'published_at', 'user_id', 'image'])
            ->where('status', 'published')
            ->where('slug', $slug)
            ->where('published_at', '<=', Carbon::now())
            ->first();

        if (!$post) abort(404);

        $months = $this->months();
        $categories = $this->categories();

        return view('pages.post', compact('post', 'months', 'categories'));
    }

    private function months(): array
    {
        return \DB::select("
            SELECT DISTINCT
                DATE_FORMAT(published_at, '%M') AS name,
                DATE_FORMAT(published_at, '%m') AS month,
                DATE_FORMAT(published_at, '%Y') AS year
            FROM posts
            WHERE status = ?
              AND published_at <= ?
            ORDER BY published_at DESC;
        ", ['published', Carbon::now()]);
    }

    private function categories(): Collection
    {
        return collect(\DB::select("
            SELECT DISTINCT category
            FROM posts
            WHERE status = ?
              AND published_at <= ?
              and category is not null
              and category != ''
            ORDER BY category;
        ", ['published', Carbon::now()]))
        ->map(fn($cat) => (object)[
            'slug' => Str::slug($cat->category),
            'name' => $cat->category,
        ]);
    }
}
