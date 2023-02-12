<?php

namespace Postgen\Api\Controllers;

use Illuminate\Routing\Controller;
use Postgen\Generator\Models\Post;
use Postgen\Generator\Logic\Posts;

class PostApiController extends Controller
{
    public function list()
    {
        $status = request('status');
        $search = request('search');
        $page = request('page');

        $query = Post::select(['id', 'category', 'title', 'status', 'tags', 'likes', 'dislikes', 'image', 'created_at'])
            ->orderBy('created_at', 'desc');

        if (auth()->user()?->id !== 1) $query->where('user_id', auth()->user()?->id);
        if ($status) $query->where('status', $status);
        if ($search) $query->where('title', 'like', "%$search%");

        return $query->paginate(20);
    }

    public function get(int $id)
    {
        $post = Post::find($id);

        if (auth()->user()?->id !== 1 && $post->user_id !== auth()->user()?->id) abort(403);

        return response($post->toArray());
    }

    public function update(int $id)
    {
        $title = request('title');
        $slug = request('slug');
        $content = request('content');
        $category = request('category');
        $tags = request('tags');
        $published_at = request('published_at');
        $image = request('image');

        $post = Post::find($id);
        if (auth()->user()?->id !== 1 && $post->user_id !== auth()->user()?->id) abort(403);

        $post = Posts::update($post, $title, $slug, $content);
        $post = Posts::updateMeta($post, $category, array_map(fn($tag) => trim($tag), is_array($tags) ? $tags : explode(',', $tags)), $published_at);
        $post = Posts::updateImage($post, $image);

        return response($post->toArray());
    }

    public function remove(int $id)
    {
        $success = Posts::remove($id);
        if (auth()->user()?->id !== 1 && $post->user_id !== auth()->user()?->id) abort(403);

        return response(compact('success'));
    }

    public function publish(int $id)
    {
        $title = request('title');
        $slug = request('slug');
        $content = request('content');

        $post = Post::find($id);
        if (auth()->user()?->id !== 1 && $post->user_id !== auth()->user()?->id) abort(403);

        $post = Posts::update($post, $title, $slug, $content);
        $success = Posts::publish($post);

        return response(compact('success'));
    }
}
