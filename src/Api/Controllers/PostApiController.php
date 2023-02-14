<?php

namespace Postgen\Api\Controllers;

use Illuminate\Routing\Controller;
use Postgen\Generator\Models\Post;
use Postgen\Generator\Logic\Posts;
use Postgen\Common\Logic\Categories;

class PostApiController extends Controller
{
    public function list()
    {
        $status = request('status');
        $search = request('search');

        $query = Post::select(['id', 'category', 'title', 'slug', 'status', 'tags', 'likes', 'dislikes', 'image', 'created_at'])
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

        $dropdowns = [
            'categories' => Categories::list()
        ];

        return response(compact('post', 'dropdowns'));
    }

    public function update(int $id)
    {
        $post = $this->store($id);

        return response($post->toArray());
    }

    public function remove(int $id)
    {
        $post = Post::find($id);
        if (auth()->user()?->id !== 1 && $post->user_id !== auth()->user()?->id) abort(403);
        $success = Posts::remove($post);

        return response(compact('success'));
    }

    public function publish(int $id)
    {
        $post = $this->store($id);
        $success = Posts::publish($post);

        return response(compact('success'));
    }

    public function store(?int $id = null)
    {
        $title = request('title');
        $slug = request('slug');
        $content = request('content');
        $category = request('category');
        $tags = request('tags');
        $published_at = request('published_at');
        $image = request('image');

        $post = $id ? Post::find($id) : Posts::store($title, $content);
        if (auth()->user()?->id !== 1 && $post->user_id !== auth()->user()?->id) abort(403);

        $category = is_array($category) ? $category['name'] : $category;
        $tags = array_map(fn($tag) => trim($tag), is_array($tags) ? $tags : explode(',', $tags));

        $post = Posts::update($post, $title, $slug, $content);
        $post = Posts::updateMeta($post, $category, $tags, $published_at);
        $post = Posts::updateImage($post, $image);

        return $post;
    }
}
