<?php

namespace Postgen\Common\Logic;

use Illuminate\Support\Collection;
use Postgen\Generator\Models\Post;
use Postgen\Moderator\Models\Article;

class Articles
{
    public static function list(int $postId): Collection
    {
        return Article::select(['id', 'site_id', 'title', 'content', 'link', 'twitter', 'facebook', 'adwords', 'published_at'])
            ->where('user_id', auth()->id())
            ->where('post_id', $postId)
            ->orderBy('published_at', 'desc')
            ->get();
    }

    public static function create(int $postId, int $siteId, int $userId): Article
    {
        $post = Post::findOrFail($postId);

        return Article::updateOrCreate([
            'post_id' => $postId,
            'site_id' => $siteId,
        ], [
            'user_id' => $userId,
            'category' => $post->category,
            'title' => $post->title,
            'content' => $post->content,
            'tags' => $post->tags,
            'image' => $post->image,
        ]);
    }

    public static function updateSocial(Article $post, string $type, string $content): bool
    {
        return $post->update([ $type => $content ]);
    }
}
