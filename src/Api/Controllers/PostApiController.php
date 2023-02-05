<?php

namespace Postgen\Api\Controllers;

use Illuminate\Routing\Controller;
use Postgen\Generator\Models\Post;
use Postgen\Generator\Logic\Posts;

class PostApiController extends Controller
{
    public function list()
    {
        $posts = Post::select(['id', 'title', 'status', 'likes', 'created_at'])
            ->where('status', 'draft')
            ->orderBy('created_at', 'desc')
            ->get();

        return response(compact('posts'));
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
