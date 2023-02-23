<?php

namespace Postgen\Moderator\Controllers;

use Illuminate\Routing\Controller;
use Postgen\Generator\Models\Post;
use Postgen\Common\Logic\Categories;
use Postgen\Common\Logic\Months;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function edit(int $id)
    {
        return view('app', [
            'app' => 'edit',
            'id' => $id,
        ]);
    }

    public function preview(int $id)
    {
        $query = Post::with('user')
            ->select(['id', 'category', 'title', 'slug', 'content', 'published_at', 'user_id', 'tags', 'image'])
            ->where('id', $id);

        if (auth()->user()->id > 1) {
            $query->where('user_id', auth()->user()->id);
        }

        $post = $query->first();

        if (!$post) abort(403);

        $category = request('category', $post->category);
        $tags = request('tags', $post->tags);

        $post->title = request('title', $post->title);
        $post->slug = request('slug', $post->slug);
        $post->content = request('content', $post->content);
        $post->category = is_array($category) ? $category['name'] : $category;
        $post->tags = array_map(fn($tag) => trim($tag), is_array($tags) ? $tags : explode(',', $tags));
        $post->published_at = request('published_at', $post->published_at);
        $post->image = request('image', $post->image);

        $months = Months::list();
        $categories = Categories::list();

        return view('pages.post', compact('post', 'months', 'categories'));
    }
}
