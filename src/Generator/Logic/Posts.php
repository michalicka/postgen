<?php

namespace Postgen\Generator\Logic;

use Postgen\Generator\Models\Post;
use Illuminate\Support\Str;
use Carbon\Carbon;

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

    public static function update(Post $post, string $title, string $slug, string $content): Post
    {
        $post->update([
            'title' => strip_tags($title),
            'slug' => Str::slug($slug),
            'content' => strip_tags($content, "<b><strong><i><em><h2><h3><a>"),
        ]);

        return $post;
    }

    public static function updateMeta(Post $post, ?string $category, array $tags, ?string $published_at): Post
    {
        $post->update([
            'category' => $category,
            'tags' => $tags,
            'published_at' => $published_at ?: null,
        ]);

        return $post;
    }

    public static function updateImage(Post $post, ?string $image): Post
    {
        $post->update([
            'image' => $image ?: null,
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
        $post->update([
            'status' => 'published',
            'published_at' => Carbon::now(),
        ]);

        return true;
    }
}
