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
            'api_url' => env('API_URL'),
            'api_user' => env('API_USER'),
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

        $months = Months::list();
        $categories = Categories::list();

        return view('pages.post', compact('post', 'months', 'categories'));
    }
}
