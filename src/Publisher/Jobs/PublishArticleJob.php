<?php

namespace Postgen\Publisher\Jobs;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Str;
use Postgen\Common\Jobs\BaseJob;
use Postgen\Moderator\Models\Article;
use Illuminate\Support\Facades\Storage;

class PublishArticleJob extends BaseJob
{
    private int $articleId;

    public function __construct(int $articleId)
    {
        $this->articleId = $articleId;
    }

    public function handle()
    {
        $article = Article::with('site')->find($this->articleId);

        $client = new Client([
            RequestOptions::HEADERS => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$article->site->api_key,
            ]
        ]);

        $data = [
            'action' => $article->external_id ? 'update' : 'create',
            'post' => [
                'id' => $article->external_id,
                'title' => $article->title,
                'excerpt' => count($parts = preg_split('/(\r?\n\r?\n)|(<\/p><p>)/', $article->content)) > 1 ? $parts[0] : '',
                'content' => $article->content,
                'category' => $article->category,
                'tags' => $article->tags,
                'image_url' => Str::startsWith($article->image, '/')
                    ? env('APP_URL').$article->image
                    : $article->image,
                'image_data' => Str::startsWith($article->image, '/')
                    ? base64_encode(Storage::disk('public')->get(pathinfo($article->image, PATHINFO_BASENAME)))
                    : null
            ]
        ];

        $response = $client->post($article->site->api_url, [
            RequestOptions::JSON => $data
        ]);

        $result = json_decode((string)$response->getBody());

        if (isset($result->post)) {
            $article->update([
                'external_id' => $result->post->id ?? null,
                'link' => $result->post->link ?? null,
                'published_at' => Carbon::now(),
            ]);
        }
    }
}
