<?php

namespace Postgen\Generator\Logic;

use Postgen\Generator\Models\Post;
use Illuminate\Support\Str;

class Posts
{
    public static function store(string $title, string $content, string $model)
    {
        $title = $title ?: Str::words($content, 10, '...');
        $title = Str::of($title)->contains(':') ? trim(Str::after($title, ':')) : $title;
        $title = preg_match('/"([^["]+)"/', $title, $match) ? $match[1] : $title;
        $slug = self::slug($title);
        $user_id = auth()->check() ? auth()->user()->id : null;

        return Post::create(compact('user_id', 'title', 'content', 'slug', 'model'));
    }

    private static function slug(string $title)
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

    public static function like(int $id)
    {
        if ($post = Post::find($id)) {
            $post->update([
                'likes' => $post->likes ? $post->likes + 1 : 1
            ]);
        }
    }

    public static function dislike(int $id)
    {
        if ($post = Post::find($id)) {
            $post->update([
                'dislikes' => $post->dislikes ? $post->dislikes - 1 : -1
            ]);
        }
    }

    public static function update(int $id, string $title, string $slug, string $content): Post
    {
        $post = Post::find($id);

        $post->update([
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
        ]);

        return $post;
    }

    public static function remove(int $id): bool
    {
        $post = Post::find($id)->delete();

        return true;
    }

    public static function publish(Post $post): bool
    {
        $post->update(['status' => 'publish']);

        return true;
    }
}
