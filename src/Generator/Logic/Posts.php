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

        return Post::create(compact('title', 'content', 'slug', 'model'));
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
}
