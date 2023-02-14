<?php

namespace Postgen\Generator\Controllers;

use Illuminate\Routing\Controller;
use Postgen\Generator\Logic\Posts;

class PostController extends Controller
{
    public function store()
    {
        $title = request('title');
        $content = request('content', '');
        $model = request('model', 'text-davinci-003');

        $parts = preg_split('/\r?\n/', $content, 2);
        if (count($parts) > 1 && strlen($parts[0]) && (strlen($parts[0]) < 5 || ucfirst($parts[0]) !== $parts[0])) {
            $title = trim($title . $parts[0]);
            $content = trim($parts[1]);
        }

        $post = $content ? Posts::store($title, $content, $model) : null;

        return response($post ? $post->only('id') : []);
    }

    public function rate()
    {
        $id = request('id');
        $type = request('type');

        if ($type === 'like') Posts::like($id);
        else if ($type === 'dislike') Posts::dislike($id);

        return response(compact('type'));
    }
}
