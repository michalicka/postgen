<?php

namespace Postgen\Generator\Logic;

use Postgen\Generator\Models\Post;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Postgen\Common\Logic\Articles;
use Postgen\Common\Logic\Images;
use Postgen\Common\Logic\Links;
use Postgen\Publisher\Jobs\PublishArticleJob;

class Posts
{
    public static function store(string $title, string $content, string $model = 'text-davinci-003')
    {
        $title = $title ?: Str::words($content, 10, '...');
        $title = Str::of($title)->contains(':') ? trim(Str::after($title, ':')) : $title;
        $title = preg_match('/"([^["]+)"/', $title, $match) ? $match[1] : $title;
        $slug = self::slug($title);
        $user_id = auth()->check() ? auth()->id() : null;
        $content = htmlspecialchars($content);

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

    public static function update(Post $post, string $title, ?string $slug, string $content): Post
    {
        $post->update([
            'title' => strip_tags($title),
            'slug' => $slug ? Str::slug($slug) : $post->slug,
            'content' => Str::of($content)
                ->stripTags('<p><h1><h2><strong><em><u><ol><ul><li><a><blockquote><pre>')
                ->replace('</p><p>', "\n\n")
                ->replace('<p>', '')
                ->replace('</p>', '')
                ->trim(),
        ]);

        return $post;
    }

    public static function updateMeta(Post $post, ?string $category, array $tags, ?string $published_at): Post
    {
        $post->update([
            'category' => strip_tags($category),
            'tags' => array_map(fn($tag) => strip_tags($tag), $tags),
            'published_at' => $published_at ?: null,
        ]);

        return $post;
    }

    public static function updateImage(Post $post, ?string $image): Post
    {
        if ($image && !Str::startsWith($image, '/')) {
            $image = Images::store($image);
        }

        $post->update([
            'image' => $image ?: null,
        ]);

        return $post;
    }

    public static function remove(Post $post): bool
    {
        $post->update(['slug' => "{$post->slug}-{$post->id}"]);
        $post->delete();

        return true;
    }

    public static function publish(Post $post, array $publishTo): bool
    {
        if (array_search(0, $publishTo, true) !== false) {
            $post->update([
                'status' => 'published',
                'published_at' => Carbon::now(),
            ]);
        }

        collect($publishTo)
            ->filter()
            ->each(function($siteId) use ($post) {
                $article = Articles::create($post->id, $siteId, auth()->id());
                \Queue::push(new PublishArticleJob($article->id));
            });

        return true;
    }

    public static function autoLinks(Post $post): Post
    {
        $list = Links::list()
            ->map(fn($link) => (object)[
                'url' => $link['code'],
                'options' => $link['name'],
                'count' => count($link['name']),
            ])
            ->sortBy([['count', 'desc']]);

        $places = [];
        $text = str($post->content);
        foreach ($list as $link) {
            foreach ($link->options as $option) {
                if (($pos = mb_stripos($text, $option)) !== false) {
                    $places[] = (object)[
                        'url' => $link->url,
                        'name' => mb_substr($text, $pos, mb_strlen($option)),
                        'start' => $pos,
                    ];
                    $text = $text->mask('*', $pos, mb_strlen($option));
                    break;
                }
            }
            if (count($places) >= 3) break;
        }

        foreach (collect($places)->sortBy([['start', 'desc']]) as $item) {
            $post->content = sprintf(
                '%s<a href="%s" rel="noopener noreferrer" target="_blank">%s</a>%s',
                mb_substr($post->content, 0, $item->start),
                $item->url,
                $item->name,
                mb_substr($post->content, $item->start + mb_strlen($item->name))
            );
        }

        return $post;
    }

    public static function autoTags(Post $post): Post
    {
        $tags = collect($post->tags)
            ->map(fn($tag) => [
                'name' => $tag,
                'length' => mb_strlen($tag),
            ])
            ->sortBy([['length', 'desc']]);

        $places = [];
        $text = str($post->content);

        preg_match_all('/<[^>]+>[^<]+<\/\w+>/', $text, $matches, PREG_OFFSET_CAPTURE);
        foreach (array_reverse($matches) as $match) {
            foreach (array_reverse($match) as $tag) {
                $pos = mb_stripos($text, $tag[0]);
                $text = $text->mask('*', $pos, mb_strlen($tag[0]));
            }
        }

        foreach ($tags as $option) {
            if (($pos = mb_stripos($text, $option['name'])) !== false) {
                $places[] = (object)[
                    'name' => mb_substr($text, $pos, mb_strlen($option['name'])),
                    'start' => $pos,
                ];
                $text = $text->mask('*', $pos, mb_strlen($option['name']));
            }
        }

        foreach (collect($places)->sortBy([['start', 'desc']]) as $item) {
            $post->content = sprintf(
                '%s<strong>%s</strong>%s',
                mb_substr($post->content, 0, $item->start),
                $item->name,
                mb_substr($post->content, $item->start + mb_strlen($item->name))
            );
        }

        return $post;
    }
}
