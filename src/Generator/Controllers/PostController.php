<?php

namespace Postgen\Generator\Controllers;

use Illuminate\Routing\Controller;
use Postgen\Generator\Logic\Posts;

class PostController extends Controller
{
    public function store()
    {
        $title = request('title');
        $content = request('content');
        $model = request('model');

        $post = $content ? Posts::store($title, $content, $model) : null;

        return response($post ? $post->only('slug') : []);
    }
}
