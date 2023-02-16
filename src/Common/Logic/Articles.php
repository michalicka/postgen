<?php

namespace Postgen\Common\Logic;

use Illuminate\Support\Collection;
use Postgen\Generator\Models\Post;
use Postgen\Moderator\Models\Article;
use Postgen\Moderator\Models\Site;

class Articles
{
    public static function list(int $postId): Collection
    {
        return Article::select(['id', 'site_id', 'title', 'link', 'created_at'])
            ->where('user_id', auth()->id())
            ->where('post_id', $postId)
            ->orderBy('created_at', 'desc')
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
            'link' => Site::find($siteId)->api_url // @todo remove
        ]);
    }

}
