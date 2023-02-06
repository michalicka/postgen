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

        $query = Post::select(['id', 'title', 'status', 'likes', 'dislikes', 'created_at'])
            ->orderBy('created_at', 'desc');

        if ($status) $query->where('status', $status);
        if ($search) $query->where('title', 'like', "%$search%");

        return $query->paginate(20);
    }

    public function get(int $id)
    {
        $post = Post::find($id);

        return response($post->toArray());
    }

    public function update(int $id)
    {
        $title = request('title');
        $slug = request('slug');
        $content = request('content');

        $post = Posts::update($id, $title, $slug, $content);

        return response($post->toArray());
    }

    public function remove(int $id)
    {
        $success = Posts::remove($id);

        return response(compact('success'));
    }

    public function publish(int $id)
    {
        $title = request('title');
        $slug = request('slug');
        $content = request('content');

        $post = Posts::update($id, $title, $slug, $content);
        $success = Posts::publish($post);

        return response(compact('success'));
    }
}
