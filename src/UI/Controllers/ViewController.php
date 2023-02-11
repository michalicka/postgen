<?php

namespace Postgen\UI\Controllers;

use Illuminate\Routing\Controller;
use Postgen\Generator\Models\Post;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ViewController extends Controller
{
    public function index()
    {
        $posts = Post::with('user')
            ->select(['id', 'title', 'slug', 'content', 'created_at', 'user_id'])
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $months = $this->months();

        return view('pages.index', compact('posts', 'months'));
    }

    public function archive(int $year, int $month)
    {
        $posts = Post::with('user')
            ->select(['id', 'title', 'slug', 'content', 'created_at', 'user_id'])
            ->where('status', 'published')
            ->whereBetween('created_at', [
                Carbon::parse(sprintf('%d-%s-01', $year, Str::padLeft("$month", 2, '0')))->startOfMonth(),
                Carbon::parse(sprintf('%d-%s-01', $year, Str::padLeft("$month", 2, '0')))->endOfMonth()
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $months = $this->months();

        return view('pages.index', compact('posts', 'months'));
    }

    public function author(int $author)
    {
        $posts = Post::with('user')
            ->select(['id', 'title', 'slug', 'content', 'created_at', 'user_id'])
            ->where('status', 'published')
            ->where('user_id', $author)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $months = $this->months();

        return view('pages.index', compact('posts', 'months'));
    }

    public function get(string $slug)
    {
        $post = Post::with('user')
            ->select(['id', 'title', 'slug', 'content', 'created_at', 'user_id'])
            ->where('status', 'published')
            ->where('slug', $slug)
            ->first();

        if (!$post) abort();

        $months = $this->months();

        return view('pages.post', compact('post', 'months'));
    }

    private function months(): array
    {
        return \DB::select("
            SELECT DISTINCT
                DATE_FORMAT(created_at, '%M') AS name,
                DATE_FORMAT(created_at, '%m') AS month,
                DATE_FORMAT(created_at, '%Y') AS year
            FROM posts
            WHERE status = 'published'
            ORDER BY created_at DESC;
        ");
    }
}
