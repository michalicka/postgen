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

        if ($content) {
            Posts::store($title, $content);
        }

        return response(compact('title', 'content'));
    }
}
