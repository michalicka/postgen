<?php

namespace Postgen\UI\Controllers;

use Illuminate\Routing\Controller;
use Postgen\Generator\Models\Post;
use Postgen\Common\Logic\Categories;
use Postgen\Common\Logic\Months;
use Illuminate\Support\Str;
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

        $months = Months::list();
        $categories = Categories::list();

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
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        $months = Months::list();
        $categories = Categories::list();

        return view('pages.index', compact('posts', 'months', 'categories'));
    }

    public function author(int $author)
    {
        $posts = Post::with('user')
            ->select(['id', 'title', 'slug', 'content', 'published_at', 'user_id', 'image'])
            ->where('status', 'published')
            ->where('user_id', $author)
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        $months = Months::list();
        $categories = Categories::list();

        return view('pages.index', compact('posts', 'months', 'categories'));
    }

    public function category(string $slug)
    {
        $categories = Categories::list();
        $category = $categories->firstWhere('code', $slug);

        if (!$category) abort(404);

        $posts = Post::with('user')
            ->select(['id', 'title', 'slug', 'content', 'published_at', 'user_id', 'image'])
            ->where('status', 'published')
            ->where('category', $category->name)
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        $months = Months::list();
        $categories = Categories::list();

        return view('pages.index', compact('posts', 'months', 'categories'));
    }

    public function tag(string $slug)
    {
        $posts = Post::with('user')
            ->select(['id', 'title', 'slug', 'content', 'published_at', 'user_id', 'image'])
            ->where('status', 'published')
            ->whereRaw("JSON_CONTAINS(tags, ?)", '"'.Str::slug($slug).'"')
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        $months = Months::list();
        $categories = Categories::list();

        return view('pages.index', compact('posts', 'months', 'categories'));
    }

    public function get(string $slug)
    {
        $post = Post::with('user')
            ->select(['id', 'category', 'title', 'slug', 'content', 'published_at', 'user_id', 'tags', 'image'])
            ->where('status', 'published')
            ->where('slug', $slug)
            ->whereNotNull('published_at')
            ->first();

        if (!$post) abort(404);

        $months = Months::list();
        $categories = Categories::list();

        return view('pages.post', compact('post', 'months', 'categories'));
    }
}
