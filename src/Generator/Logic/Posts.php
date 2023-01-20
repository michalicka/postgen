<?php

namespace Postgen\Generator\Logic;

use Postgen\Generator\Models\Post;
use Illuminate\Support\Str;

class Posts
{
    public static function store($title, $content, $model)
    {
        $title = $title ?: Str::words($content, 10, '...');
        $slug = self::slug($title);
        $user_id = auth()->check() ? auth()->user()->id : null;

        return Post::create(compact('user_id', 'title', 'content', 'slug', 'model'));
    }

    private static function slug($title)
    {
        $slug = Str::slug(Str::words($title, 20));
        $last = Post::where('slug', 'regexp', "$slug-[0-9]+")->orderBy('id', 'desc')->first();
        if ($last) {
            $slug .= preg_match('/-(\d+)$/', $last->slug, $match) ? '-'.(intval($match[1]) + 1) : '';
        } else if (Post::firstWhere('slug', $slug)) {
            $slug .= '-1';
        }

        return $slug;
    }

    public static function like($id)
    {
        if ($post = Post::find($id)) {
            $post->update([
                'likes' => $post->likes ? $post->likes + 1 : 1
            ]);
        }
    }

    public static function dislike($id)
    {
        if ($post = Post::find($id)) {
            $post->update([
                'dislikes' => $post->dislikes ? $post->dislikes - 1 : -1
            ]);
        }
    }
}
